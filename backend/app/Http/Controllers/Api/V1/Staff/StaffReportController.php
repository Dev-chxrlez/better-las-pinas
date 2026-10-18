<?php

namespace App\Http\Controllers\Api\V1\Staff;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReportResource;
use App\Models\Report;
use App\Models\ReportAssignment;
use App\Models\ReportResolution;
use App\Models\ReportStatusHistory;
use App\Models\ReportUpdate;
use App\Models\StaffAssignment;
use App\Models\User;
use App\Notifications\ReportStatusChanged;
use App\Services\AuditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

final class StaffReportController extends Controller
{
    public function officers(Request $request): JsonResponse
    {
        abort_unless($request->user()->can('assign reports'),403);
        $officers=User::role(['barangay-officer','city-department-officer'])
            ->whereHas('staffAssignments',fn($query)=>$query->where('is_active',true))
            ->with(['roles:id,name','staffAssignments'=>fn($query)=>$query->where('is_active',true)])
            ->orderBy('name')->get()
            ->map(fn($user)=>['id'=>$user->id,'name'=>$user->name,'role'=>$user->roles->first()?->name,'scopes'=>$user->staffAssignments->map(fn($scope)=>['barangay_id'=>$scope->barangay_id,'department_id'=>$scope->department_id])]);
        return response()->json(['success'=>true,'message'=>'Assignable officers.','data'=>['items'=>$officers]]);
    }

    public function index(Request $request): JsonResponse
    {
        $user=$request->user();
        $query=Report::query()->with(['category','barangay','assignments.assignee']);
        if(!$user->hasRole('system-administrator')) {
            if($user->can('review incoming reports')) $query->whereIn('status',['submitted','under_review','rejected','duplicate']);
            else $query->whereHas('assignments',fn($q)=>$q->where('assigned_to_user_id',$user->id)->where('is_active',true));
        }
        if($request->filled('status')) $query->where('status',$request->string('status'));
        $reports=$query->latest('submitted_at')->paginate(20);
        return response()->json(['success'=>true,'message'=>'Authorized reports.','data'=>['items'=>ReportResource::collection($reports->items()),'pagination'=>['current_page'=>$reports->currentPage(),'last_page'=>$reports->lastPage(),'total'=>$reports->total()]]]);
    }

    public function show(Request $request, Report $report): JsonResponse
    {
        $this->authorize('view',$report);
        $report->load(['category','barangay','attachments','statusHistory.actor','updates.author','assignments.assignee','resolution']);
        return response()->json(['success'=>true,'message'=>'Authorized report.','data'=>['report'=>new ReportResource($report)]]);
    }

    public function review(Request $request, Report $report): JsonResponse
    {
        abort_unless($request->user()->can('review incoming reports'),403);
        abort_unless(in_array($report->status, ['submitted', 'under_review'], true), 422, 'This report is no longer awaiting intake review.');
        $validated=$request->validate(['decision'=>['required',Rule::in(['accept','reject','duplicate'])],'note'=>['nullable','string','max:2000']]);
        $next=['accept'=>'under_review','reject'=>'rejected','duplicate'=>'duplicate'][$validated['decision']];
        $before=$report->status;
        $this->transition($request,$report,$next,$validated['note']??null);
        AuditService::record($request,'report.reviewed',$report,['status'=>$before],['status'=>$next,'decision'=>$validated['decision']]);
        return response()->json(['success'=>true,'message'=>'Review decision recorded.','data'=>['report'=>new ReportResource($report->fresh(['category','barangay']))]]);
    }

    public function assign(Request $request, Report $report): JsonResponse
    {
        abort_unless($request->user()->can('assign reports'),403);
        abort_unless(in_array($report->status, ['under_review', 'assigned'], true), 422, 'Accept the report before assigning it.');
        $validated=$request->validate(['assigned_to_user_id'=>['required','integer','exists:users,id'],'barangay_id'=>['nullable','uuid','exists:barangays,id'],'department_id'=>['nullable','uuid','exists:departments,id'],'note'=>['nullable','string','max:2000']]);
        abort_if(empty($validated['barangay_id'])===empty($validated['department_id']),422,'Choose exactly one barangay or department assignment.');
        $officer=User::findOrFail($validated['assigned_to_user_id']);
        abort_unless($officer->hasAnyRole(['barangay-officer','city-department-officer']),422,'The selected user is not an assignable officer.');
        $authorized=StaffAssignment::query()->where('user_id',$officer->id)->where('is_active',true)->when($validated['barangay_id']??null,fn($q,$id)=>$q->where('barangay_id',$id))->when($validated['department_id']??null,fn($q,$id)=>$q->where('department_id',$id))->exists();
        abort_unless($authorized,422,'The selected officer is not authorized for this assignment.');
        DB::transaction(function() use($request,$report,$validated): void { $report->assignments()->where('is_active',true)->update(['is_active'=>false]); ReportAssignment::create(['report_id'=>$report->id,'assigned_to_user_id'=>$validated['assigned_to_user_id'],'assigned_by_user_id'=>$request->user()->id,'barangay_id'=>$validated['barangay_id']??null,'department_id'=>$validated['department_id']??null,'is_active'=>true,'assigned_at'=>now()]); $this->transition($request,$report,'assigned',$validated['note']??'Report assigned to an authorized officer.'); });
        AuditService::record($request,'report.assigned',$report,null,['assigned_to_user_id'=>$officer->id]);
        return response()->json(['success'=>true,'message'=>'Report assigned.','data'=>['report'=>new ReportResource($report->fresh(['category','barangay','assignments.assignee']))]]);
    }

    public function updateStatus(Request $request, Report $report): JsonResponse
    {
        $this->authorize('updateStatus',$report);
        $validated=$request->validate(['status'=>['required',Rule::in(['in_progress','needs_information','resolved','closed'])],'note'=>['required','string','max:3000'],'resolution_notes'=>['required_if:status,resolved','nullable','string','max:5000'],'resolution_photo'=>['nullable','file','image','mimes:jpeg,jpg,png,webp','max:10240']]);
        $allowed = ['assigned'=>['in_progress','needs_information'],'in_progress'=>['in_progress','needs_information','resolved'],'needs_information'=>['in_progress'],'resolved'=>['closed']];
        abort_unless(in_array($validated['status'], $allowed[$report->status] ?? [], true), 422, 'That status transition is not allowed.');
        if ($validated['status'] === 'resolved') abort_unless($request->user()->can('resolve assigned reports'), 403);
        DB::transaction(function() use($request,$report,$validated): void { $this->transition($request,$report,$validated['status'],$validated['note']); ReportUpdate::create(['report_id'=>$report->id,'author_id'=>$request->user()->id,'body'=>$validated['note'],'visible_to_resident'=>true]); if($validated['status']==='resolved'){$evidence=$request->hasFile('resolution_photo')?$request->file('resolution_photo')->store("reports/{$report->id}/resolution",config('filesystems.default')):null;ReportResolution::updateOrCreate(['report_id'=>$report->id],['resolved_by'=>$request->user()->id,'notes'=>$validated['resolution_notes'],'evidence_path'=>$evidence,'resolved_at'=>now()]);} });
        AuditService::record($request,'report.status_updated',$report,null,['status'=>$validated['status']]);
        return response()->json(['success'=>true,'message'=>'Report status updated.','data'=>['report'=>new ReportResource($report->fresh(['category','barangay']))]]);
    }

    private function transition(Request $request, Report $report, string $next, ?string $note, bool $notify=true): void
    {
        $before=$report->status; $report->update(['status'=>$next]); ReportStatusHistory::create(['report_id'=>$report->id,'changed_by'=>$request->user()->id,'from_status'=>$before,'to_status'=>$next,'note'=>$note,'visible_to_resident'=>true]); if($notify) $report->reporter->notify(new ReportStatusChanged($report));
    }
}

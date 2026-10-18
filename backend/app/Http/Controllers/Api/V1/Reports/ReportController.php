<?php

namespace App\Http\Controllers\Api\V1\Reports;

use App\Http\Controllers\Controller;
use App\Http\Requests\Reports\StoreReportRequest;
use App\Http\Resources\ReportResource;
use App\Models\Report;
use App\Models\ReportAttachment;
use App\Models\ReportStatusHistory;
use App\Notifications\ReportSubmitted;
use App\Services\AuditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class ReportController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query=$request->user()->reports()->with(['category','barangay']);
        if($request->filled('status')) $query->where('status',$request->string('status'));
        if($request->filled('search')) { $search='%'.$request->string('search')->toString().'%'; $query->where(fn($q)=>$q->where('reference','like',$search)->orWhere('title','like',$search)->orWhereHas('category',fn($c)=>$c->where('name','like',$search))); }
        $reports=$query->orderBy('submitted_at',$request->string('sort')->toString()==='oldest'?'asc':'desc')->paginate(10);
        return response()->json(['success'=>true,'message'=>'Your reports.','data'=>['items'=>ReportResource::collection($reports->items()),'pagination'=>['current_page'=>$reports->currentPage(),'last_page'=>$reports->lastPage(),'per_page'=>$reports->perPage(),'total'=>$reports->total()]]]);
    }

    public function store(StoreReportRequest $request): JsonResponse
    {
        $validated=$request->validated();
        $report=DB::transaction(function() use($request,$validated): Report {
            do{$reference='LPC-'.now()->format('Ym').'-'.Str::upper(Str::random(8));}while(Report::where('reference',$reference)->exists());
            $report=Report::create(['reference'=>$reference,'reporter_id'=>$request->user()->id,'category_id'=>$validated['category_id'],'barangay_id'=>$validated['barangay_id'],'title'=>$validated['title'],'description'=>$validated['description'],'location_text'=>$validated['location_text'],'latitude'=>$validated['latitude']??null,'longitude'=>$validated['longitude']??null,'status'=>'submitted','submitted_at'=>now()]);
            ReportStatusHistory::create(['report_id'=>$report->id,'changed_by'=>$request->user()->id,'from_status'=>null,'to_status'=>'submitted','note'=>'Report submitted by resident.','visible_to_resident'=>true]);
            if($request->hasFile('photo')) { $file=$request->file('photo'); $disk=config('filesystems.default'); $path=$file->store("reports/{$report->id}",$disk); ReportAttachment::create(['report_id'=>$report->id,'uploaded_by'=>$request->user()->id,'disk'=>$disk,'path'=>$path,'original_name'=>$file->getClientOriginalName(),'mime_type'=>$file->getMimeType()?:'application/octet-stream','size'=>$file->getSize()]); }
            $request->user()->notify(new ReportSubmitted($report));
            AuditService::record($request,'report.created',$report,null,['status'=>'submitted','reference'=>$report->reference]);
            return $report;
        });
        $report->load(['category','barangay','attachments','statusHistory.actor']);
        return response()->json(['success'=>true,'message'=>'Report submitted successfully.','data'=>['report'=>new ReportResource($report)]],201);
    }

    public function show(Request $request, Report $report): JsonResponse
    {
        $this->authorize('view',$report);
        $report->load(['category','barangay','attachments','statusHistory'=>fn($q)=>$q->where('visible_to_resident',true)->with('actor')->oldest(),'updates'=>fn($q)=>$q->where('visible_to_resident',true)->with('author')->oldest(),'assignments.assignee','resolution']);
        return response()->json(['success'=>true,'message'=>'Report details.','data'=>['report'=>new ReportResource($report)]]);
    }

    public function feedback(Request $request, Report $report): JsonResponse
    {
        $this->authorize('view', $report);
        abort_unless($report->reporter_id === $request->user()->id, 403);
        abort_unless(in_array($report->status, ['resolved', 'closed'], true) && $report->resolution, 422, 'Feedback is available after a report is resolved.');
        $validated = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'feedback' => ['nullable', 'string', 'max:2000'],
        ]);
        $before = ['resident_rating' => $report->resolution->resident_rating, 'resident_feedback' => $report->resolution->resident_feedback];
        $report->resolution->update(['resident_rating' => $validated['rating'], 'resident_feedback' => $validated['feedback'] ?? null]);
        AuditService::record($request, 'report.feedback_recorded', $report, $before, $validated);
        $report->load(['category','barangay','attachments','statusHistory.actor','updates.author','assignments.assignee','resolution']);
        return response()->json(['success'=>true,'message'=>'Thank you for your feedback.','data'=>['report'=>new ReportResource($report)]]);
    }

    public function attachment(Request $request, Report $report, ReportAttachment $attachment): StreamedResponse
    {
        $this->authorize('view', $report);
        abort_unless($attachment->report_id === $report->id, 404);
        return Storage::disk($attachment->disk)->download($attachment->path, $attachment->original_name, ['Content-Type'=>$attachment->mime_type]);
    }

    public function resolutionEvidence(Request $request, Report $report): StreamedResponse
    {
        $this->authorize('view', $report);
        abort_unless($report->resolution?->evidence_path, 404);
        return Storage::disk(config('filesystems.default'))->download($report->resolution->evidence_path);
    }
}

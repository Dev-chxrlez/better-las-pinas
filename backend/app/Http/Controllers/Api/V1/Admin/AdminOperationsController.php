<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Report;
use App\Models\ReportCategory;
use App\Services\AuditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

final class AdminOperationsController extends Controller
{
    public function analytics(): JsonResponse
    {
        return response()->json(['success'=>true,'message'=>'Live report analytics.','data'=>[
            'total'=>Report::count(),
            'by_status'=>Report::selectRaw('status, count(*) as aggregate')->groupBy('status')->pluck('aggregate','status'),
            'by_category'=>ReportCategory::withCount('reports')->orderByDesc('reports_count')->get(['id','name'])->map(fn($item)=>['name'=>$item->name,'count'=>$item->reports_count]),
        ]]);
    }

    public function auditLogs(): JsonResponse
    {
        $items=AuditLog::with('actor:id,name')->latest()->paginate(50);
        return response()->json(['success'=>true,'message'=>'Audit log.','data'=>['items'=>$items->getCollection()->map(fn($item)=>['id'=>$item->id,'action'=>$item->action,'actor'=>$item->actor?->name,'subject_type'=>class_basename($item->subject_type),'subject_id'=>$item->subject_id,'created_at'=>$item->created_at?->toIso8601String()]),'pagination'=>['current_page'=>$items->currentPage(),'last_page'=>$items->lastPage(),'total'=>$items->total()]]]);
    }

    public function categories(): JsonResponse
    {
        return response()->json(['success'=>true,'message'=>'Report categories.','data'=>['items'=>ReportCategory::orderBy('sort_order')->get()]]);
    }

    public function storeCategory(Request $request): JsonResponse
    {
        $validated=$request->validate(['name'=>['required','string','max:100','unique:report_categories,name'],'description'=>['nullable','string','max:500']]);
        $category=ReportCategory::create(['name'=>$validated['name'],'slug'=>Str::slug($validated['name']).'-'.Str::lower(Str::random(5)),'description'=>$validated['description']??null,'sort_order'=>ReportCategory::max('sort_order')+1,'is_active'=>true]);
        AuditService::record($request,'category.created',$category,null,$category->only(['name','is_active']));
        return response()->json(['success'=>true,'message'=>'Category created.','data'=>['category'=>$category]],201);
    }

    public function updateCategory(Request $request, ReportCategory $category): JsonResponse
    {
        $validated=$request->validate(['name'=>['sometimes','required','string','max:100','unique:report_categories,name,'.$category->id],'description'=>['nullable','string','max:500'],'is_active'=>['sometimes','boolean']]);
        $before=$category->only(['name','description','is_active']); $category->update($validated);
        AuditService::record($request,'category.updated',$category,$before,$category->only(['name','description','is_active']));
        return response()->json(['success'=>true,'message'=>'Category updated.','data'=>['category'=>$category]]);
    }
}

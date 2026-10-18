<?php
namespace App\Http\Controllers\Api\V1\Reports;
use App\Http\Controllers\Controller;
use App\Http\Resources\ReportResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
final class ResidentDashboardController extends Controller { public function __invoke(Request $request): JsonResponse { $query=$request->user()->reports(); $counts=['submitted'=>(clone $query)->whereIn('status',['submitted','under_review','assigned'])->count(),'in_progress'=>(clone $query)->whereIn('status',['in_progress','needs_information'])->count(),'resolved'=>(clone $query)->whereIn('status',['resolved','closed'])->count()]; $recent=(clone $query)->with(['category','barangay'])->latest('submitted_at')->limit(5)->get(); $notifications=$request->user()->notifications()->latest()->limit(5)->get()->map(fn($item)=>['id'=>$item->id,'data'=>$item->data,'read_at'=>$item->read_at?->toIso8601String(),'created_at'=>$item->created_at?->toIso8601String()]); return response()->json(['success'=>true,'message'=>'Resident dashboard.','data'=>['counts'=>$counts,'recent_reports'=>ReportResource::collection($recent),'notifications'=>$notifications]]); } }

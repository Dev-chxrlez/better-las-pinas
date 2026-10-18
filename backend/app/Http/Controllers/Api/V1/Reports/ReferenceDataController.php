<?php
namespace App\Http\Controllers\Api\V1\Reports;
use App\Http\Controllers\Controller;
use App\Models\Barangay;
use App\Models\ReportCategory;
use App\Models\Department;
use Illuminate\Http\JsonResponse;
final class ReferenceDataController extends Controller { public function __invoke(): JsonResponse{return response()->json(['success'=>true,'message'=>'Reference data.','data'=>['categories'=>ReportCategory::query()->where('is_active',true)->orderBy('sort_order')->get(['id','name']),'barangays'=>Barangay::query()->where('is_active',true)->orderBy('name')->get(['id','name']),'departments'=>Department::query()->where('is_active',true)->orderBy('name')->get(['id','name'])]]);} }

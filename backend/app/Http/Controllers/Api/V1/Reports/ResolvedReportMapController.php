<?php

namespace App\Http\Controllers\Api\V1\Reports;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\JsonResponse;

final class ResolvedReportMapController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $items = Report::query()
            ->whereIn('status', ['resolved', 'closed'])
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->with(['category:id,name', 'barangay:id,name'])
            ->latest('submitted_at')
            ->limit(250)
            ->get(['id', 'category_id', 'barangay_id', 'latitude', 'longitude', 'status'])
            ->map(fn (Report $report): array => [
                'id' => $report->id,
                'latitude' => (float) $report->latitude,
                'longitude' => (float) $report->longitude,
                'status' => $report->status,
                'category' => $report->category->name,
                'barangay' => $report->barangay->name,
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Resolved report locations.',
            'data' => ['items' => $items],
        ]);
    }
}

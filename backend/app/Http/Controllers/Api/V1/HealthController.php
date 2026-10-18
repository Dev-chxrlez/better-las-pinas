<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

final class HealthController extends Controller
{
    public function __invoke(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Las Piñas Connect API is healthy.',
            'data' => ['status' => 'ok'],
        ]);
    }
}

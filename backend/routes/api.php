<?php

use App\Http\Controllers\Api\V1\HealthController;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Admin\UserManagementController;
use App\Http\Controllers\Api\V1\Admin\AdminOperationsController;
use App\Http\Controllers\Api\V1\Reports\ReferenceDataController;
use App\Http\Controllers\Api\V1\Reports\ReportController;
use App\Http\Controllers\Api\V1\Reports\ResidentDashboardController;
use App\Http\Controllers\Api\V1\Reports\ResolvedReportMapController;
use App\Http\Controllers\Api\V1\Staff\StaffReportController;
use App\Http\Controllers\Api\V1\NotificationsController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::get('/health', HealthController::class)->name('api.v1.health');
    Route::get('/reference-data', ReferenceDataController::class);
    Route::get('/map/resolved-reports', ResolvedReportMapController::class);
    Route::prefix('auth')->group(function (): void {
        Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:3,1');
        Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
        Route::middleware('auth:sanctum')->group(function (): void {
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::get('/me', [AuthController::class, 'me']);
        });
    });

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::get('/notifications', NotificationsController::class);
        Route::get('/dashboard', ResidentDashboardController::class)->middleware('permission:view own reports');
        Route::get('/reports', [ReportController::class, 'index'])->middleware('permission:view own reports');
        Route::post('/reports', [ReportController::class, 'store'])->middleware(['permission:submit report','throttle:10,1']);
        Route::get('/reports/{report}', [ReportController::class, 'show']);
        Route::put('/reports/{report}/feedback', [ReportController::class, 'feedback']);
        Route::get('/reports/{report}/attachments/{attachment}', [ReportController::class, 'attachment'])->name('api.v1.reports.attachments.show');
        Route::get('/reports/{report}/resolution-evidence', [ReportController::class, 'resolutionEvidence'])->name('api.v1.reports.resolution-evidence.show');

        Route::prefix('staff')->middleware('role:intake-officer|barangay-officer|city-department-officer|system-administrator')->group(function (): void {
            Route::get('/officers', [StaffReportController::class, 'officers']);
            Route::get('/reports', [StaffReportController::class, 'index']);
            Route::get('/reports/{report}', [StaffReportController::class, 'show']);
            Route::post('/reports/{report}/review', [StaffReportController::class, 'review']);
            Route::post('/reports/{report}/assign', [StaffReportController::class, 'assign']);
            Route::post('/reports/{report}/status', [StaffReportController::class, 'updateStatus']);
        });

        Route::prefix('admin')->middleware('role:system-administrator')->group(function (): void {
            Route::get('/users', [UserManagementController::class, 'index']);
            Route::post('/users', [UserManagementController::class, 'store']);
            Route::put('/users/{user}', [UserManagementController::class, 'update']);
            Route::get('/categories', [AdminOperationsController::class, 'categories']);
            Route::post('/categories', [AdminOperationsController::class, 'storeCategory']);
            Route::put('/categories/{category}', [AdminOperationsController::class, 'updateCategory']);
            Route::get('/analytics', [AdminOperationsController::class, 'analytics']);
            Route::get('/audit-logs', [AdminOperationsController::class, 'auditLogs']);
        });
    });
});

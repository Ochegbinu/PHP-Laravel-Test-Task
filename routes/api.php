<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\BrtController;
use App\Http\Controllers\API\DashboardController;
use App\Http\Controllers\API\NotificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    Route::post('/verify-email', [AuthController::class, 'verify']);
    Route::post('/resend-verification', [AuthController::class, 'resendVerification']);
    
    Route::get('/user', function (Request $request) {
        return $request->user();
    })->middleware('auth:sanctum');

 
    Route::middleware('auth:api')->group(function () {
        // Route to create a BRT
        Route::post('brts', [BrtController::class, 'create']);
        Route::get('brts', [BrtController::class, 'index']);
        Route::get('brts/{id}', [BrtController::class, 'show']);
        Route::put('brts/{id}', [BrtController::class, 'update']);
        Route::delete('brts/{id}', [BrtController::class, 'destroy']);
        Route::get('/notification',[NotificationController::class, 'getLatest'] );
    });

    Route::middleware(['auth', 'admin'])->group(function () {
        Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
        Route::get('/admin/dashboard/chart-data', [DashboardController::class, 'getChartData'])->name('admin.dashboard.chart-data');
    });
});

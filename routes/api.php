<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\API\DeviceController as AdminApiDevice;
use App\Http\Controllers\Web\API\AuthController as ApiAuthController;
use App\Http\Controllers\Admin\API\AuthController as ApiAdminAuthController;
use App\Http\Controllers\Admin\API\DashboardController as ApiAdminDashboardController;
use App\Http\Controllers\Admin\API\DeviceController;
use App\Http\Controllers\MQTTController;
use App\Http\Controllers\Web\API\DeviceController as ApiDeviceController;
use App\Http\Controllers\Web\API\ValveController;
use App\Http\Controllers\Web\DeviceController as UserDeviceController;

// Admin API Routes - Sanctum Auth + Role & Permission Based Access
Route::post('/device/onboard', [AdminApiDevice::class, 'onboard']);
Route::post('/device/control', [MQTTController::class, 'publishValveCommand']);
Route::post('/device/health-report', [DeviceController::class, 'storeHardwareStatus']);






Route::post('/signup', [ApiAuthController::class, 'signup'])->name('api.signup');
Route::post('/verify-otp', [ApiAuthController::class, 'verifyUserOtp'])->name('api.verifyOtp');
Route::post('/login', [ApiAuthController::class, 'loginApi'])->name('api.login');


Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [ApiAuthController::class, 'logout']);
    Route::get('/profile', [ApiAuthController::class, 'profile']);
    Route::post('/update-password', [ApiAuthController::class, 'updatePassword']);
    Route::post('/valve-control', [ValveController::class, 'control']);
});






Route::post('/admin/signup', [ApiAdminAuthController::class, 'superadminSignupApi'])->name('api.superadmin.signup');
Route::post('/admin/verify-otp', [ApiAdminAuthController::class, 'verifySuperadminOtpApi'])->name('api.superadmin.verifyOtp');
Route::post('/admin/login', [ApiAdminAuthController::class, 'superadminLoginApi'])->name('api.superadmin.login');
// Protected routes
Route::middleware(['auth:sanctum', 'role:superadmin|admin|technician'])->prefix('api/admin')->group(function () {

    Route::get('/dashboard', [ApiAdminDashboardController::class, 'indexApi'])->name('api.admin.dashboard');

    Route::post('/logout', [ApiAdminAuthController::class, 'logout'])->name('api.admin.logout');
});


Route::prefix('admin')->name('admin.')->middleware(['auth:sanctum', 'auth:admin'])->group(function () {

    // Create Device - Only for Superadmin with Permission
    Route::middleware(['role:superadmin', 'permission:Can Create Device'])->group(function () {

    });
    
});

// routes/api.php
Route::post('/device/heartbeat', [ApiDeviceController::class, 'heartbeat']);


Route::get('/device/status', [ApiDeviceController::class, 'getStatus']);
Route::post('/device/data', [UserDeviceController::class, 'store']);

Route::get('/device/{device_number}/latest-reading', [UserDeviceController::class, 'latest'])->name('api.device.latest-reading');


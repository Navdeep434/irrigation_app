<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\API\DeviceController as AdminApiDevice;
use App\Http\Controllers\Web\API\AuthController as ApiAuthController;
use App\Http\Controllers\Web\API\DeviceController as UserApiDevice;

// Admin API Routes - Sanctum Auth + Role & Permission Based Access
Route::post('/device/onboard', [AdminApiDevice::class, 'onboard']);


Route::prefix('admin')->name('admin.')->middleware(['auth:sanctum', 'auth:admin'])->group(function () {

    // Create Device - Only for Superadmin with Permission
    Route::middleware(['role:superadmin', 'permission:Can Create Device'])->group(function () {

    });
    
});


Route::post('/signup', [ApiAuthController::class, 'signup'])->name('api.signup');
Route::post('/verify-otp', [ApiAuthController::class, 'verifyUserOtp'])->name('api.verifyOtp');




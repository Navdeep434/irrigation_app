<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\API\DeviceController as AdminApiDevice;
use App\Http\Controllers\Admin\API\DeviceController;

// Admin API Routes - Sanctum Auth + Role & Permission Based Access
Route::post('/admin/device/onboard', [DeviceController::class, 'onboard']);
Route::prefix('admin')->name('admin.')->middleware(['auth:sanctum', 'auth:admin'])->group(function () {

    // Create Device - Only for Superadmin with Permission
    Route::middleware(['role:superadmin', 'permission:Can Create Device'])->group(function () {

    });
    
});

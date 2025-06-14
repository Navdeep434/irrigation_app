<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\API\DeviceController as AdminApiDevice;
use App\Http\Controllers\Web\API\DeviceController as UserApiDevice;

// Admin API Routes - Sanctum Auth + Role & Permission Based Access
Route::post('/device/onboard', [AdminApiDevice::class, 'onboard']);
Route::post('/device/update', [AdminApiDevice::class, 'updateDevice']);

//User API Routes - Sanctum Auth + Role & Permission Based Access
Route::post('/device-data', [UserApiDevice::class, 'store']);
Route::get('/device-data/{device_id}', [UserApiDevice::class, 'show']);


Route::prefix('admin')->name('admin.')->middleware(['auth:sanctum', 'auth:admin'])->group(function () {

    // Create Device - Only for Superadmin with Permission
    Route::middleware(['role:superadmin', 'permission:Can Create Device'])->group(function () {

    });
    
});

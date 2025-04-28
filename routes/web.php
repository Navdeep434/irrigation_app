<?php

use App\Http\Controllers\Admin\API\DeviceController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthController as WebAuth;
use App\Http\Controllers\Web\DashboardController as WebDashboard;
use App\Http\Controllers\Admin\AuthController as AdminAuth;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\DeviceController as AdminDevice;
use App\Http\Controllers\Admin\RoleAndPermissionController;
use App\Http\Controllers\OtpController;
use App\Http\Controllers\Admin\UserController as AdminUser;

// =======================
// User Routes
// =======================

Route::middleware('guest:web')->group(function () {
    Route::get('/signup', [WebAuth::class, 'showSignup'])->name('user.signup');
    Route::post('/signup', [WebAuth::class, 'signup'])->name('user.signup.post');

    Route::get('/login', [WebAuth::class, 'showLoginForm'])->name('login');
    Route::post('/login', [WebAuth::class, 'login'])->name('user.login.post');
});

Route::middleware('auth:web')->group(function () {
    Route::post('/logout', [WebAuth::class, 'logout'])->name('user.logout');

    // Protected User Dashboard Route
    Route::middleware('role:user')->get('/dashboard', [WebDashboard::class, 'index'])->name('user.dashboard');
});

Route::get('/verify-otp', [OtpController::class, 'showUserOtpForm'])->name('user.verifyOtp');
Route::post('/verify-otp', [OtpController::class, 'verifyUserOtp'])->name('user.verifyOtp.post');


// =======================
// Admin Routes
// =======================

Route::prefix('admin')->name('admin.')->middleware('guest:admin')->group(function () {
    Route::get('/signup', [AdminAuth::class, 'showSuperadminSignup'])->name('signup');
    Route::post('/signup', [AdminAuth::class, 'superadminSignup'])->name('signup.post');

    Route::get('/login', [AdminAuth::class, 'showLogin'])->name('login');
    Route::post('/login', [AdminAuth::class, 'login'])->name('login.post');
});

Route::prefix('admin')->name('admin.')->middleware('auth:admin')->group(function () {
    Route::post('/logout', [AdminAuth::class, 'logout'])->name('logout');

    // Routes for superadmin only with permission checks
    Route::middleware('role:superadmin')->group(function () {
        // User Permissions
        Route::get('/create-user', [AdminUser::class, 'create'])->name('create-user')->middleware('permission:Can Create User');
        Route::post('/create-user', [AdminUser::class, 'store'])->name('store-user')->middleware('permission:Can Create User');
        Route::get('/edit-user/{id}', [AdminUser::class, 'edit'])->name('edit-user')->middleware('permission:Can Edit User');
        Route::post('/update-user/{id}', [AdminUser::class, 'update'])->name('update-user')->middleware('permission:Can Edit User');
        Route::delete('/delete-user/{id}', [AdminUser::class, 'destroy'])->name('delete-user')->middleware('permission:Can Delete User');

        // Role Permissions
        Route::get('/roles/create', [RoleAndPermissionController::class, 'createRole'])->name('roles.create')->middleware('permission:Can Create Role');
        Route::post('/roles/store', [RoleAndPermissionController::class, 'storeRole'])->name('roles.store')->middleware('permission:Can Create Role');
        Route::get('/roles/edit/{id}', [RoleAndPermissionController::class, 'editRole'])->name('roles.edit')->middleware('permission:Can Edit Role');
        Route::post('/roles/update/{id}', [RoleAndPermissionController::class, 'updateRole'])->name('roles.update')->middleware('permission:Can Edit Role');
        Route::delete('/roles/delete/{id}', [RoleAndPermissionController::class, 'destroyRole'])->name('roles.delete')->middleware('permission:Can Delete Role');

        // Permission Management
        Route::get('/permission/create', [RoleAndPermissionController::class, 'createPermission'])->name('permission.create')->middleware('permission:Can Create Permission');
        Route::post('/permission/store', [RoleAndPermissionController::class, 'storePermission'])->name('permission.store')->middleware('permission:Can Create Permission');
        Route::get('/permission/{id}/edit', [RoleAndPermissionController::class, 'editPermission'])->name('permission.edit')->middleware('permission:Can Edit Permission');
        Route::post('/permission/{id}/update', [RoleAndPermissionController::class, 'updatePermission'])->name('permission.update')->middleware('permission:Can Edit Permission');
        Route::delete('/permission/{id}', [RoleAndPermissionController::class, 'destroyPermission'])->name('permission.delete')->middleware('permission:Can Delete Permission');

        // Device Management
        Route::get('/devices/create', [AdminDevice::class, 'create'])->name('devices.create');
        Route::post('/devices/store', [AdminDevice::class, 'store'])->name('devices.store');
        Route::get('/devices', [AdminDevice::class, 'deviceList'])->name('devices.list');
        Route::get('/devices/{device}/edit', [AdminDevice::class, 'edit'])->name('devices.edit');
        Route::put('/devices/{device}', [AdminDevice::class, 'update'])->name('devices.update');
        Route::delete('/devices/{device}', [AdminDevice::class, 'destroy'])->name('devices.destroy');
        // Routes for toggling device actions
        Route::post('/devices/{deviceId}/toggle-status', [AdminDevice::class, 'toggleStatus'])->name('devices.toggle.status');
        Route::post('/devices/{deviceId}/toggle-repair', [AdminDevice::class, 'toggleRepair'])->name('devices.toggle.repair');
        Route::post('/devices/{deviceId}/toggle-blocked', [AdminDevice::class, 'toggleBlocked'])->name('devices.toggle.blocked');

        Route::get('/devices/trash', [AdminDevice::class, 'trashed'])->name('devices.trash');
        Route::post('/devices/{id}/restore', [AdminDevice::class, 'restore'])->name('devices.restore');
        Route::get('/devices/repair-list', [AdminDevice::class, 'repairList'])->name('devices.repairList');
        Route::get('/devices/available', [AdminDevice::class, 'showUnassociatedForm'])->name('devices.availble');
        // Route::post('/devices/assign', [AdminDevice::class, 'assignDevice'])->name('devices.assign');

        
        // Customer Management
        Route::get('customers', [CustomerController::class, 'index'])->name('customers.list');
        Route::get('customer/{id}/edit', [CustomerController::class, 'edit'])->name('customer.edit');
        Route::post('customer/{id}/update', [CustomerController::class, 'update'])->name('customer.update');
        Route::post('customers/{id}/toggle-status', [CustomerController::class, 'toggleStatus'])->name('customers.toggleStatus');
        Route::post('customers/{id}/toggleBlock', [CustomerController::class, 'toggleBlock'])->name('customers.toggleBlock');
        Route::delete('customers/{id}', [CustomerController::class, 'destroy'])->name('customers.destroy');
        Route::post('/devices/{id}/unassociate', [CustomerController::class, 'unassociateCustomer'])->name('devices.unassociate');
        Route::post('/customers/attach-device', [CustomerController::class, 'attachDeviceCustomer'])->name('customer.attachDevice');





        // Settings (optional permission)
        Route::get('/settings', function () {
            return view('admin.admin-pages.settings');
        })->name('settings');
    });

    // Routes for superadmin, admin, and technician
    Route::middleware('role:superadmin|admin|technician')->group(function () {
        Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');
        Route::get('/user/profile', [AdminUser::class, 'editProfile'])->name('profile');
        Route::post('/user/profile/update', [AdminUser::class, 'updateProfile'])->name('profile.update');
    });

    // Routes for superadmin and admin with permission checks
    Route::middleware('role:superadmin|admin')->group(function () {
        Route::get('/list-users', [AdminUser::class, 'index'])->name('list-users')->middleware('permission:Can View UserList');
        Route::post('/verify-user/{id}', [AdminUser::class, 'verifyUser'])->name('verify-user');

        Route::get('/roles/list', [RoleAndPermissionController::class, 'listRole'])->name('roles.list')->middleware('permission:Can View RoleList');
        Route::get('/permission/list', [RoleAndPermissionController::class, 'listPermission'])->name('permission.list')->middleware('permission:Can View PermissionList');

        Route::get('/roles/assign-permission', [RoleAndPermissionController::class, 'getRolesAndPermissions'])->name('roles.assign.permission')->middleware('permission:Can Assign Permissions');
        Route::post('/roles/assign-permission', [RoleAndPermissionController::class, 'assignPermissionToRole'])->name('roles.assign.permission.store')->middleware('permission:Can Assign Permissions');
    });

    Route::middleware('role:superadmin|technician')->group(function () {
        // Add technician-specific routes here
    });
});

Route::get('/admin/verify-otp', [OtpController::class, 'showAdminOtpForm'])->name('admin.verifyOtp');
Route::post('/admin/verify-otp', [OtpController::class, 'verifySuperadminOtp'])->name('admin.verifyOtp.post');


// =======================
// MQTT Routes
// =======================
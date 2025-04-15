<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthController as WebAuth;
use App\Http\Controllers\Web\DashboardController as WebDashboard;
use App\Http\Controllers\Admin\AuthController as AdminAuth;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\OtpController;
use App\Http\Controllers\Admin\UserController;

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

    // Protected Admin Routes (Superadmin/Admin/Technician roles)
    Route::middleware('role:superadmin|admin|technician')->get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

    // User Management Routes
    Route::middleware('role:superadmin')->get('/create-user', [UserController::class, 'create'])->name('createUser');
    Route::middleware('role:superadmin')->post('/create-user', [UserController::class, 'store'])->name('store-user');
    Route::middleware('role:superadmin')->get('/list-users', [UserController::class, 'index'])->name('list-users');
    Route::middleware('role:superadmin')->get('/edit-user/{id}', [UserController::class, 'edit'])->name('edit-user');
    Route::middleware('role:superadmin')->post('/update-user/{id}', [UserController::class, 'update'])->name('update-user');
    Route::middleware('role:superadmin')->delete('/delete-user/{id}', [UserController::class, 'destroy'])->name('delete-user');
});

Route::get('/admin/verify-otp', [OtpController::class, 'showAdminOtpForm'])->name('admin.verifyOtp');
Route::post('/admin/verify-otp', [OtpController::class, 'verifySuperadminOtp'])->name('admin.verifyOtp.post');

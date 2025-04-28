<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use \App\Models\User;
use Spatie\Permission\Models\Role;

class DashboardController extends Controller
{
    public function index()
    { 
        $user = Auth::guard('admin')->user();
        $totalUsers = User::count();
        $activeUsers = User::where('status', 'active')->count();
        $inactiveUsers = User::where('status', 'inactive')->count();
        $verifiedUsers = User::where('is_verified', 1)->count();
        $technicians = User::whereHas('roles', function($query) {
            $query->where('name', 'technician');
        })->count();
        $admins = User::whereHas('roles', function($query) {
            $query->where('name', 'admin');
        })->count();
        $users = User::whereHas('roles', function($query) {
            $query->where('name', 'user');
        })->count();
        $superadmins = User::whereHas('roles', function($query) {
            $query->where('name', 'superadmin');
        })->count();

        $trashedUsers = User::onlyTrashed()->count();
        
        return view('admin.admin-pages.dashboard', compact(
            'user', 'totalUsers', 'activeUsers', 'inactiveUsers', 'verifiedUsers', 
            'technicians', 'admins', 'users', 'superadmins', 'trashedUsers'
        ));
    }


}

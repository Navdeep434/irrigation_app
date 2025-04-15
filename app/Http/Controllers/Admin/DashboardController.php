<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use \App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // User counts and stats
        $totalUsers     = User::count();
        $activeUsers    = User::where('status', 'active')->count();
        $inactiveUsers  = User::where('status', 'inactive')->count();
        $verifiedUsers  = User::whereNotNull('email_verified_at')->count();
        $technicians    = User::role('technician')->count();

        return view('admin.admin-pages.dashboard', compact(
            'user',
            'totalUsers',
            'activeUsers',
            'inactiveUsers',
            'verifiedUsers',
            'technicians'
        ));
    }

}

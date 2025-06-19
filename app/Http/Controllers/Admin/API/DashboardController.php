<?php

namespace App\Http\Controllers\Admin\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function indexApi(Request $request)
    {
        $user = $request->user();

        if (!$user || !$user->hasAnyRole(['superadmin', 'admin', 'technician'])) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized access.',
            ], 403);
        }

        $stats = [
            'user' => [
                'id'    => $user->id,
                'name'  => $user->first_name . ' ' . $user->last_name,
                'email' => $user->email,
                'roles' => $user->getRoleNames(),
            ],
            'total_users'     => User::count(),
            'active_users'    => User::where('status', 'active')->count(),
            'inactive_users'  => User::where('status', 'inactive')->count(),
            'verified_users'  => User::where('is_verified', true)->count(),
            'technicians'     => User::role('technician')->count(),
            'admins'          => User::role('admin')->count(),
            'users'           => User::role('user')->count(),
            'superadmins'     => User::role('superadmin')->count(),
            'trashed_users'   => User::onlyTrashed()->count(),
        ];

        return response()->json([
            'status'  => 'true',
            'message' => 'Dashboard stats retrieved successfully.',
            'data'    => $stats,
        ]);
    }

}

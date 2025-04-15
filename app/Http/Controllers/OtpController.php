<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Session;
use Spatie\Permission\Models\Role;

class OtpController extends Controller
{
    // Display OTP form for user verification
    public function showUserOtpForm(Request $request)
    {
        $email = $request->query('email');
        return view('web.auth.verify-otp', compact('email'));
    }

    // Handle user OTP verification
    public function verifyUserOtp(Request $request)
    {
        // Validate the OTP
        $request->validate([
            'otp' => 'required|digits:6',
            'email' => 'required|email',
        ]);

        // Look for unverified user
        $user = User::where('email', $request->input('email'))
                    ->where('is_verified', false)
                    ->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'No unverified user found with this email.',
            ]);
        }

        if ($request->otp != $user->otp) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid OTP.',
            ]);
        }

        if ($user->otp_expires_at && now()->gt($user->otp_expires_at)) {
            return response()->json([
                'status' => 'error',
                'message' => 'OTP has expired. Please request a new one.',
            ]);
        }

        // Assign role
        $role = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
        $user->assignRole($role);

        // Update verification status and save assigned role
        $user->is_verified = true;
        $user->status = 'active';
        // $user->role = $user->role ?? 'user';
        $user->otp = null;
        $user->otp_expires_at = null;
        $user->email_verified_at = now();
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'OTP verified successfully. You can now log in.',
        ]);
    }

    // Display OTP form for superadmin verification
    public function showAdminOtpForm()
    {
        return view('admin.auth.verify-otp');
    }

    // Handle superadmin OTP verification and user creation
    public function verifySuperadminOtp(Request $request)
    {
        // Validate the OTP input
        $request->validate([
            'otp' => 'required|digits:6',
            'email' => 'required|email',
        ]);
        // Fetch user with pending status
        $user = User::where('email', $request->input('email'))
                    ->where('status', 'pending')
                    ->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'No pending superadmin signup found.',
            ]);
        }

        if ($request->otp != $user->otp) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid OTP.',
            ]);
        }

        if ($user->otp_expires_at && now()->gt($user->otp_expires_at)) {
            return response()->json([
                'status' => 'error',
                'message' => 'OTP has expired. Please request a new one.',
            ]);
        }

        // Update user verification and status
        $user->status = 'active';
        // $user->role = $user->role ?? 'superadmin';
        $user->is_verified = true;
        $user->otp = null;
        $user->otp_expires_at = null;
        $user->email_verified_at = now();
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'OTP verified successfully. Redirecting to dashboard...',
            // 'redirect_url' => route('admin.dashboard'),
        ]);
    }

}

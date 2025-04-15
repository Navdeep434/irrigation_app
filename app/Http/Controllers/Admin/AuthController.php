<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Mail\SuperadminOtpMail;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;


class AuthController extends Controller
{
    public function showSuperadminSignup()
    {
        return view('admin.auth.signup');
    }

    public function superadminSignup(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email',
            'gender'     => 'required|string|in:male,female,other',
            'dob'        => 'required|date',
            'password'   => 'required|string|min:8|confirmed',
        ]);

        DB::beginTransaction();

        try {
            $otp = rand(100000, 999999);
            $otpExpiration = now()->addMinutes(5);
            $ownerEmail = config('app.owner_email');
            $ownerName = config('app.owner_name');

            $user = new User([
                'first_name'     => $validated['first_name'],
                'last_name'      => $validated['last_name'],
                'email'          => $validated['email'],
                'gender'         => $validated['gender'],
                'dob'            => $validated['dob'],
                'password'       => bcrypt($validated['password']),
                'status'         => 'pending',
                'otp'            => $otp,
                'otp_expires_at' => $otpExpiration,
            ]);

            $user->save();

            // Assign superadmin role
            $user->assignRole(Role::findByName('superadmin', 'admin'));

            // Send email
            Mail::to($ownerEmail)->send(new SuperadminOtpMail($validated, $otp , $ownerName));

            DB::commit();

            return redirect()->route('admin.verifyOtp')->with('message', 'OTP has been sent to the owner for verification.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->withErrors(['error' => 'Something went wrong. Please try again.']);
        }
    }


    public function showLogin()
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::guard('admin')->attempt($credentials)) {
            $user = Auth::guard('admin')->user();

            if (!$user->hasAnyRole(['superadmin', 'admin', 'technician'])) {
                Auth::guard('admin')->logout();
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized login.'
                ], 403);
            }

            if (!$user->is_verified) {
                Auth::guard('admin')->logout();

                if (!$user->otp || $user->otp_expires_at < now()) {
                    $user->otp = rand(100000, 999999);
                    $user->otp_expires_at = now()->addMinutes(10);
                    $user->save();
                }

                try {
                    $userData = [
                        'first_name' => $user->first_name,
                        'last_name'  => $user->last_name,
                        'email'      => $user->email,
                        'gender'     => $user->gender,
                        'dob'        => $user->dob,
                    ];
                    Mail::to(config('app.owner_email'))->send(new SuperadminOtpMail($userData, $user->otp));
                } catch (\Exception $e) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Failed to send OTP email.'
                    ], 500);
                }

                return response()->json([
                    'status' => 'verify',
                    'message' => 'Please verify your email first.',
                    'redirect_url' => route('admin.verifyOtp', ['email' => $user->email])
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Login successful.',
                'redirect_url' => route('admin.dashboard')
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Invalid credentials.'
        ], 422);
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('message', 'Logged out successfully.');
    }
}

<?php

namespace App\Http\Controllers\Admin\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Spatie\Permission\Models\Role;
use App\Mail\SuperadminOtpMail;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function superadminSignupApi(Request $request)
    {
        $validated = $request->validate([
            'first_name'      => 'required|string|max:255',
            'last_name'       => 'required|string|max:255',
            'email'           => 'required|email|unique:users,email',
            'country_code'    => 'required|string|max:5',
            'contact_number'  => 'required|digits_between:5,15',
            'gender'          => 'required|string|in:male,female,other',
            'dob'             => 'required|date',
            'password'        => 'required|string|min:8|confirmed',
            'profile_image'   => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
        ]);

        DB::beginTransaction();

        try {
            $otp = rand(100000, 999999);
            $otpExpiration = now()->addMinutes(5);
            $ownerEmail = config('app.owner_email');
            $ownerName  = config('app.owner_name');

            // Handle profile image upload
            $profileImagePath = null;
            if ($request->hasFile('profile_image')) {
                $file = $request->file('profile_image');
                $ext = $file->getClientOriginalExtension();
                $filename = 'profile_' . time() . '_' . uniqid() . '.' . $ext;
                $profileImagePath = $file->storeAs('profile_images', $filename, 'public');
            }

            $user = User::create([
                'first_name'      => $validated['first_name'],
                'last_name'       => $validated['last_name'],
                'email'           => $validated['email'],
                'country_code'    => $validated['country_code'],
                'contact_number'  => $validated['contact_number'],
                'gender'          => $validated['gender'],
                'dob'             => $validated['dob'],
                'password'        => bcrypt($validated['password']),
                'status'          => 'pending',
                'otp'             => $otp,
                'otp_expires_at'  => $otpExpiration,
                'profile_image'   => $profileImagePath,
                'two_fa_enabled'  => false,
            ]);

            $user->assignRole(Role::findByName('superadmin', 'admin'));

            // Send OTP to owner
            Mail::to($ownerEmail)->send(new SuperadminOtpMail($validated, $otp, $ownerName));

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Superadmin signup successful. OTP sent to owner.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Superadmin Signup Error: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong. Please try again.',
            ], 500);
        }
    }

    public function verifySuperadminOtpApi(Request $request)
    {
        $request->validate([
            'otp'   => 'required|digits:6',
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)
                    ->where('status', 'pending')
                    ->whereHas('roles', fn ($q) => $q->where('name', 'superadmin'))
                    ->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'No pending superadmin signup found.',
            ], 404);
        }

        if ($user->otp !== $request->otp) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid OTP.',
            ], 422);
        }

        if (now()->gt($user->otp_expires_at)) {
            return response()->json([
                'status' => false,
                'message' => 'OTP has expired. Please request a new one.',
            ], 410);
        }

        $user->update([
            'status' => 'active',
            'is_verified' => true,
            'otp' => null,
            'otp_expires_at' => null,
            'email_verified_at' => now(),
        ]);

        $token = $user->createToken('superadmin-token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'OTP verified successfully.',
            'token' => $token,
            'user' => $user,
        ]);
    }

    public function superadminLoginApi(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid credentials.',
            ], 401);
        }

        if (!$user->hasRole('superadmin')) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized. Not a superadmin.',
            ], 403);
        }

        if (!$user->is_verified) {
            return response()->json([
                'status' => false,
                'message' => 'Account not verified. Please complete OTP verification.',
            ], 403);
        }

        // if ($user->is_2fa_enabled ?? false) {
        //     $otp = rand(100000, 999999);
        //     $user->otp = $otp;
        //     $user->otp_expires_at = now()->addMinutes(10);
        //     $user->save();

        //     try {
        //         Mail::to($user->email)->send(new SuperadminOtpMail([
        //             'first_name' => $user->first_name,
        //             'last_name' => $user->last_name,
        //             'email' => $user->email,
        //             'gender' => $user->gender,
        //             'dob' => $user->dob,
        //         ], $otp));
        //     } catch (\Exception $e) {
        //         return response()->json([
        //             'status' => false,
        //             'message' => 'Failed to send 2FA OTP.',
        //         ], 500);
        //     }

        //     return response()->json([
        //         'status' => 'verify_2fa',
        //         'message' => '2FA is enabled. OTP sent.',
        //         'email' => $user->email,
        //     ], 200);
        // }

        $token = $user->createToken('superadmin-token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Login successful.',
            'token' => $token,
            'user' => $user,
        ]);
    }

    public function logout(Request $request)
    {
        $user = $request->user();

        $user->currentAccessToken()->delete();

        return response()->json([
            'status' => true,
            'message' => 'Logged out successfully.'
        ]);
    }

}

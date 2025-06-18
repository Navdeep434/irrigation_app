<?php

namespace App\Http\Controllers\Web\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserOtpMail;
use App\Models\Customer;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function signup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'country_code' => 'required|string|max:5',
            'contact_number' => 'required|digits_between:5,15',
            'gender' => 'required|string|in:male,female,other',
            'dob' => 'required|date',
            'password' => 'required|string|min:8|confirmed',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        $otp = rand(100000, 999999);
        $otpExpiration = now()->addMinutes(5);
        $profileImagePath = null;

        if ($request->hasFile('profile_image')) {
            $file = $request->file('profile_image');
            $ext = $file->getClientOriginalExtension();
            $filename = 'profile_' . time() . '_' . uniqid() . '.' . $ext;

            $profileImagePath = $file->storeAs('profile_images', $filename, 'public');
        }

        $user = new User([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'country_code' => $validated['country_code'],
            'contact_number' => $validated['contact_number'],
            'gender' => $validated['gender'],
            'dob' => $validated['dob'],
            'password' => bcrypt($validated['password']),
            'status' => 'pending',
            'otp' => $otp,
            'otp_expires_at' => $otpExpiration,
            'profile_image' => $profileImagePath,
        ]);

        $user->save();
        $user->assignRole('user');

        try {
            Mail::to($user->email)->send(new UserOtpMail([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'gender' => $validated['gender'],
                'dob' => $validated['dob'],
            ], $otp));
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to send OTP email. Please try again later.'
            ], 500);
        }

        return response()->json([
            'status' => true,
            'message' => 'Signup successful. An OTP has been sent to your email for verification.',
            'data' => [
                'email' => $user->email
            ]
        ], 201);
    }

    public function verifyUserOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6',
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->input('email'))
                    ->where('is_verified', false)
                    ->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'No unverified user found with this email.',
            ], 404);
        }

        if ($request->otp != $user->otp) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid OTP.',
            ], 401);
        }

        if ($user->otp_expires_at && now()->gt($user->otp_expires_at)) {
            return response()->json([
                'status' => false,
                'message' => 'OTP has expired. Please request a new one.',
            ], 410);
        }

        // Mark user as verified
        $user->is_verified = true;
        $user->status = 'active';
        $user->otp = null;
        $user->otp_expires_at = null;
        $user->email_verified_at = now();
        $user->save();

        // Generate UID
        $uidData = Customer::generateCustomerUid($user);

        // Send UID mail
        try {
            Mail::to($user->email)->send(new \App\Mail\CustomerUidMail(
                $user->first_name . ' ' . $user->last_name,
                $uidData['uid']
            ));
        } catch (\Exception $e) {
            \Log::error('Failed to send UID email: ' . $e->getMessage());
        }

        // ✅ Generate Sanctum token
        $token = $user->createToken('user-auth-token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'OTP verified successfully. UID sent to your email.',
            'data' => [
                'token' => $token,
                'uid' => $uidData['uid'],
                'name' => $user->first_name . ' ' . $user->last_name,
            ]
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid credentials.'
            ], 422);
        }

        $user = Auth::user();

        // Role check
        if (!$user->hasRole('user')) {
            Auth::logout();
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized login.'
            ], 403);
        }

        // If user is not verified or opted for 2FA
        if (!$user->is_verified || $user->two_factor_enabled) {
            Auth::logout(); // Logout to prevent session issues

            // Generate OTP if expired or missing
            if (!$user->otp || now()->gt($user->otp_expires_at)) {
                $user->otp = rand(100000, 999999);
                $user->otp_expires_at = now()->addMinutes(10);
                $user->save();
            }

            // Send OTP
            try {
                Mail::to($user->email)->send(new UserOtpMail([
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'email' => $user->email,
                    'gender' => $user->gender,
                    'dob' => $user->dob,
                ], $user->otp));
            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed to send OTP email.'
                ], 500);
            }

            return response()->json([
                'status' => 'verify',
                'message' => 'OTP sent for verification.',
                'email' => $user->email
            ]);
        }

        // All checks passed, issue Sanctum token
        $token = $user->createToken('user-login-token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Login successful.',
            'token' => $token,
            'user' => $user
        ]);
    }


}

<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use App\Mail\UserOtpMail;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function showSignup()
    {
        return view('web.auth.signup');
    }

    public function signup(Request $request)
    {
        $validated = $request->validate([
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

        $otp = rand(100000, 999999);
        $otpExpiration = now()->addMinutes(5);
        $profileImagePath = null;

        if ($request->hasFile('profile_image')) {
            $file = $request->file('profile_image');
            $ext = $file->getClientOriginalExtension();
            $filename = 'profile_' . time() . '_' . uniqid() . '.' . $ext;

            // Store image in 'profile_images' folder in public disk
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
            return redirect()->back()->withErrors(['email' => 'Failed to send OTP email. Please try again later.']);
        }

        return redirect()->to('/verify-otp?email=' . $user->email)->with('message', 'An OTP has been sent to your email for verification.');
    }


    public function showLoginForm()
    {
        // Check if the user is already logged in
        if (Auth::check()) {
            return redirect()->route('user.dashboard')->with('success', 'You are already logged in.');
        }
        return view('web.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        // Attempt login
        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Check if user has 'user' role
            if (!$user->hasRole('user')) {
                Auth::logout();
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized login.',
                ], 403);
            }

            // Check if user is verified
            if (!$user->is_verified) {
                Auth::logout();
                
                // Check if user has a non-expired OTP
                if ($user->otp && $user->otp_expires_at > now()) {
                    // Use existing OTP
                    $otp = $user->otp;
                } else {
                    // Generate and save new OTP
                    $otp = rand(100000, 999999); // 6-digit OTP
                    $user->otp = $otp;
                    $user->otp_expires_at = now()->addMinutes(10);
                    $user->save();
                }
                
                // Send OTP email
                $userData = [
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'email' => $user->email,
                    'gender' => $user->gender,
                    'dob' => $user->dob,
                ];
                
                try {
                    Mail::to($user->email)->send(new UserOtpMail($userData, $otp));
                } catch (\Exception $e) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Failed to send OTP email. Please try again later.'
                    ], 500);
                }
                
                return response()->json([
                    'status' => 'verify',
                    'message' => 'Please verify your email first.',
                    'redirect_url' => route('user.verifyOtp', ['email' => $user->email])
                ]);
            }

            // Login successful
            return response()->json([
                'status' => 'success',
                'message' => 'Login successful.',
                'redirect_url' => route('user.dashboard')
            ]);
        }

        // Invalid credentials
        return response()->json([
            'status' => 'error',
            'message' => 'Invalid credentials.'
        ], 422);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'You have been logged out.');
    }
    
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{

    public function index(Request $request)
    {
        $query = User::query();

        // Handle search
        if ($request->has('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('first_name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('last_name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('email', 'like', '%' . $searchTerm . '%');
            });
        }
        
        // Handle role filter
        if ($request->has('role') && $request->role !== '') {
            $role = $request->role;
            // Ensure the role filtering works based on the correct relationship
            $query->whereHas('roles', function($q) use ($role) {
                $q->where('roles.name', $role);
            });
        }

        // Handle sorting
        $sortColumn = $request->get('sort', 'id');
        $sortDirection = $request->get('direction', 'desc');
        
        // Validate sort column to prevent SQL injection
        $allowedColumns = ['id', 'first_name', 'last_name', 'email', 'gender', 'dob'];
        if (in_array($sortColumn, $allowedColumns)) {
            $query->orderBy($sortColumn, $sortDirection);
        }
        
        // Fetch users with pagination
        $users = $query->paginate(10);

        if ($request->ajax()) {
            return view('admin.admin-pages.list-users', compact('users'))->render();
        }
        
        return view('admin.admin-pages.list-users', compact('users'));
    }


    // Show the Create User Form
    public function create()
    {
        // Exclude the superadmin role
        $roles = Role::where('name', '!=', 'superadmin')->get();
        return view('admin.admin-pages.create-user', compact('roles'));
    }


    // Store the New User
    public function store(Request $request)
    {
        // Validate the incoming request data
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'country_code' => 'required|string|max:5',
            'contact_number' => 'required|digits_between:5,15',
            'gender' => 'required|string|in:male,female,other',
            'dob' => 'required|date',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|exists:roles,name',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validated['role'] === 'superadmin') {
            abort(403, 'Unauthorized to assign superadmin role.');
        }
        
        // Create the new user
        $user = new User([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'country_code' =>$validated['country_code'],
            'contact_number' => $validated['contact_number'],
            'gender' => $validated['gender'],
            'dob' => $validated['dob'],
            'password' => Hash::make($validated['password']),
            'status' => 'pending',
        ]);
        $user->save();

        // Attach the selected role
        $user->assignRole($validated['role']);

        // Redirect back to the user creation page with a success message
        return redirect()->route('admin.create-user')->with('message', 'User created successfully!');
    }

    public function edit($id)
    {
        // Get the user by ID
        $user = User::findOrFail($id);

        // Get all roles for the dropdown (except 'superadmin')
        $roles = Role::where('name', '!=', 'superadmin')->get();

        // Pass the user and roles to the view
        return view('admin.admin-pages.edit-user', compact('user', 'roles'));
    }

    // Update the user
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $currentUser = auth()->user();

        // If the user is superadmin and current user is NOT a superadmin, prevent update
        if ($user->hasRole('superadmin') && !$currentUser->hasRole('superadmin')) {
            abort(403, 'You are not authorized to update a superadmin.');
        }

        // Validate input
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email,' . $id,
            'country_code' => 'required|string|max:5',
            'contact_number' => 'required|digits_between:5,15',
            'gender'     => 'required|string|in:male,female,other',
            'dob'        => 'required|date',
            'password'   => 'nullable|string|min:8|confirmed',
            'role'       => 'nullable|exists:roles,name',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Prevent updating anyone's role to superadmin
        if ($request->filled('role') && $request->role === 'superadmin' && !$user->hasRole('superadmin')) {
            abort(403, 'You are not allowed to assign the superadmin role.');
        }

        // Update user details
        $user->first_name = $validated['first_name'];
        $user->last_name  = $validated['last_name'];
        $user->email      = $validated['email'];
        $user->country_code = $validated['country_code'];
        $user->contact_number = $validated['contact_number'];
        $user->gender     = $validated['gender'];
        $user->dob        = $validated['dob'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        if (
            $request->filled('role') &&
            $request->role !== 'superadmin' &&
            (!$user->hasRole('superadmin') || $currentUser->hasRole('superadmin'))
        ) {
            $desiredRole = $request->role;
        
            // Check if user already has this role
            if (!$user->hasRole($desiredRole)) {
                // Determine correct guard
                $guard = in_array($desiredRole, ['user']) ? 'web' : 'admin';
        
                // Fetch role under that guard
                $role = Role::where('name', $desiredRole)->where('guard_name', $guard)->first();
        
                if (!$role) {
                    return back()->withErrors(['role' => "Role '{$desiredRole}' does not exist for guard '{$guard}'."]);
                }
        
                // Before assigning, check the guard compatibility of the user
                if ($user->guard_name !== $guard) {
                    return back()->withErrors(['role' => "User must be under '{$guard}' guard to assign this role."]);
                }
        
                $user->syncRoles([$role]);
            }
        }

        return redirect()->route('admin.list-users')->with('success', 'User updated successfully.');
    }


    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if (auth('admin')->id() === $user->id) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete(); // Soft delete

        return redirect()->route('admin.user-list')->with('success', 'User deleted successfully.');
    }


    public function verifyUser(Request $request, $id)
    {
        // Find the user by ID or fail
        $user = User::findOrFail($id);

        // Toggle verification
        if ($user->is_verified) {
            // If already verified, unverify the user
            $user->is_verified = false;
            $user->status = 'pending';
            $message = 'User has been unverified.';
        } else {
            // If not verified, verify the user
            $user->is_verified = true;
            $user->status = 'active'; 
            $message = 'User verified successfully!';
        }

        $user->save();

        // Return success response with appropriate message
        return response()->json(['success' => true, 'message' => $message]);
    }

    public function editProfile()
    {
        $user = auth()->user(); // or User::find(auth()->id());
        return view('admin.admin-pages.profile ', compact('user'));
    }
    
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'gender' => 'required|in:male,female,other',
            'dob' => 'required|date',
            'country_code' => 'required|string|max:5',
            'contact_number' => 'required|digits_between:5,15',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg|max:10240',
        ]);
    
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
    
        // Update fields
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->gender = $request->gender;
        $user->dob = $request->dob;
        $user->country_code = $request->country_code;
        $user->contact_number = $request->contact_number;
    
        // Optional: Handle profile image
        if ($request->hasFile('profile_image')) {
            if ($user->profile_image && Storage::disk('public')->exists($user->profile_image)) {
                Storage::disk('public')->delete($user->profile_image);
            }
    
            $imagePath = $request->file('profile_image')->store('profile_images', 'public');
            $user->profile_image = $imagePath;
        }
    
        $user->save();
    
        return redirect()->back()->with('success', 'Profile updated successfully.');
    }
    
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

    public function index()
    {
        $users = User::paginate(10);
        return view('admin.admin-pages.list-users', compact('users'));
    }

    // Show the Create User Form
    public function create()
    {
        // Get all roles for the dropdown
        $roles = Role::all();
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
            'gender' => 'required|string|in:male,female,other',
            'dob' => 'required|date',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|exists:roles,id',
        ]);

        // Create the new user
        $user = new User([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'gender' => $validated['gender'],
            'dob' => $validated['dob'],
            'password' => Hash::make($validated['password']),
        ]);
        $user->save();

        // Attach the selected role
        $user->assignRole(Role::find($validated['role'])->name);

        // Redirect back to the user creation page with a success message
        return redirect()->route('admin.createUser')->with('message', 'User created successfully!');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.admin-pages.edit-user', compact('user'));
    }

    // Update the user
    public function update(Request $request, $id)
    {
        // Validate the incoming data
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email,' . $id,
            'gender'     => 'required|string|in:male,female,other',
            'dob'        => 'required|date',
            'password'   => 'nullable|string|min:8|confirmed',
        ]);

        // Find the user and update their data
        $user = User::findOrFail($id);
        $user->first_name = $validated['first_name'];
        $user->last_name  = $validated['last_name'];
        $user->email      = $validated['email'];
        $user->gender     = $validated['gender'];
        $user->dob        = $validated['dob'];

        // Only update the password if provided
        if ($validated['password']) {
            $user->password = Hash::make($validated['password']);
        }

        // Save the updated user
        $user->save();

        return redirect()->route('admin.list-users')->with('success', 'User updated successfully.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.users')->with('success', 'User deleted successfully.');
    }
}

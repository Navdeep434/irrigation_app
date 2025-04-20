<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;


class RoleAndPermissionController extends Controller
{
    public function listRole(Request $request)
    {
        // Fetch all roles
        $roles = Role::query();

        if ($request->has('search')) {
            $search = $request->search;
        
            $roles->where(function ($query) use ($search) {
                $query->where('name', 'like', "%$search%")
                    ->orWhere('guard_name', 'like', "%$search%");
            });
        }

        if ($request->has('sort') && $request->has('direction')) {
            $roles->orderBy($request->sort, $request->direction);
        }

        $roles = $roles->paginate(10);

        // Check if users are attached to roles
        foreach ($roles as $role) {
            $role->has_users = $role->users()->exists();  // Check if users are attached
        }

        return view('admin.admin-pages.list-role', compact('roles'));
    }


    public function createRole()
    {
        // Show the form to create a new role
        return view('admin.admin-pages.create-role');
    }

    public function storeRole(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name',
            'guard_name' => 'required|string',
        ]);

        Role::create([
            'name' => $request->name,
            'guard_name' => $request->guard_name,
        ]);

        return redirect()->back()->with('success', 'Role created successfully!');
    }

    public function editRole($id)
    {
        $role = Role::findOrFail($id);
        return view('admin.admin-pages.edit-role', compact('role'));
    }

    public function updateRole(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'guard_name' => 'required|string|max:255',
        ]);

        $role = Role::findOrFail($id);
        $role->update([
            'name' => $request->name,
            'guard_name' => $request->guard_name,
        ]);

        return redirect()->route('admin.roles.list')->with('success', 'Role updated successfully.');
    }

    public function destroyRole($id)
    {
        $role = Role::findOrFail($id);

        // Prevent deletion of superadmin role
        if ($role->name === 'superadmin') {
            return response()->json(['success' => false, 'message' => 'Cannot delete the superadmin role.']);
        }

        // Check if the role is attached to any users from either guard
        $isUserRoleAttached = $role->users()->where('guard_name', 'user')->count() > 0;
        $isAdminRoleAttached = $role->users()->where('guard_name', 'admin')->count() > 0;

        if ($isUserRoleAttached || $isAdminRoleAttached) {
            return response()->json(['success' => false, 'message' => 'Cannot delete a role that is attached to users.']);
        }

        // Delete the role
        $role->delete();

        return response()->json(['success' => true, 'message' => 'Role deleted successfully.']);
    }

    public function listPermission(Request $request)
    {
        $search = $request->get('search');
        $permissions = Permission::when($search, function ($query, $search) {
                return $query->where('name', 'like', "%$search%");
            })
            ->paginate(10);
        return view('admin.admin-pages.list-permission', compact('permissions'));
    }

    // Show the form for creating a new permission
    public function createPermission()
    {
        return view('admin.admin-pages.create-permission');
    }

    // Store a new permission
    public function storePermission(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:permissions|max:255',
        ]);

        Permission::create([
            'name' => $request->name,
        ]);

        return redirect()->route('admin.permission.list')->with('success', 'Permission created successfully!');
    }

    // Show the form for editing the specified permission
    public function editPermission($id)
    {
        $permission = Permission::findOrFail($id);
        return view('admin.admin-pages.edit-permission', compact('permission'));
    }

    // Update the specified permission
    public function updatePermission(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|max:255|unique:permissions,name,' . $id,
        ]);

        $permission = Permission::findOrFail($id);
        $permission->update([
            'name' => $request->name,
        ]);

        return redirect()->route('permission.index')->with('success', 'Permission updated successfully!');
    }

    // Delete the specified permission
    public function destroyPermission($id)
    {
        // Find the permission by ID or fail if not found
        $permission = Permission::findOrFail($id);

        // Check if the permission is assigned to any role
        $isPermissionAssignedToRole = $permission->roles()->count() > 0;

        // Check if the permission is assigned to any user
        $isPermissionAssignedToUser = $permission->users()->count() > 0;

        // If the permission is attached to a role or user, prevent deletion
        if ($isPermissionAssignedToRole || $isPermissionAssignedToUser) {
            return response()->json(['success' => false, 'message' => 'Cannot delete a permission that is attached to a role or user.']);
        }

        // If the permission is not assigned to any role or user, delete it
        $permission->delete();

        // Return success response
        return response()->json(['success' => true, 'message' => 'Permission deleted successfully!']);
    }

    /**
     * Get all roles and permissions for the assign view.
     */
    public function getRolesAndPermissions(Request $request)
    {
        // Specify the admin guard when retrieving roles
        $roles = Role::where('guard_name', 'admin')->get();
        $permissions = Permission::where('guard_name', 'admin')->get();

        // Get selected role ID from query or null
        $selectedRoleId = $request->input('role_id');

        // Get assigned permissions if role is selected
        $assignedPermissions = [];
        if ($selectedRoleId) {
            try {
                // Specify the guard name when finding the role
                $role = Role::findById($selectedRoleId, 'admin');
                $assignedPermissions = $role->permissions->pluck('id')->toArray();
            } catch (\Exception $e) {
                // Handle case where role doesn't exist
                return redirect()->route('admin.roles.assign.permission')
                    ->with('error', 'Selected role does not exist.');
            }
        }

        return view('admin.admin-pages.assign-permission', compact('roles', 'permissions', 'selectedRoleId', 'assignedPermissions'));
    }

    /**
     * Assign permissions to a selected role.
     */
    public function assignPermissionToRole(Request $request)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $roleId = $request->input('role_id');
        $permissionIds = $request->input('permissions', []);
        
        try {
            $role = Role::findById($roleId, 'admin');
            
            // Sync permissions with the role
            $permissions = Permission::whereIn('id', $permissionIds)
                ->where('guard_name', 'admin')
                ->get();
                
            $role->syncPermissions($permissions);
            
            return redirect()->back()->with('success', 'Permissions assigned successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to assign permissions: ' . $e->getMessage());
        }
    }

}

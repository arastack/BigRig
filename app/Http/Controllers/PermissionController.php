<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Permission;
use App\Models\RolePermission;
use App\Models\TrainingObjective;

class PermissionController extends Controller
{
    public function index($roleId)
    {
        $courses = TrainingObjective::orderBy('price', 'asc')->get();
        $rawPermissions = Permission::orderBy('name', 'asc')->get();
        $rolePermissions = RolePermission::where('role_id', $roleId)
            ->pluck('permission_id')
            ->toArray();
        $roleName = Role::find($roleId)->role_name;

        $groupedPermissions = [];
        foreach ($rawPermissions as $permission) {
            // Extract the entity and action from the name
            [$action, $entity] = explode('_', $permission->name, 2);

            // Group by entity and add actions
            if (!isset($groupedPermissions[$entity])) {
                $groupedPermissions[$entity] = [
                    'name' => ucfirst(str_replace('_', ' ', $entity)),
                    'actions' => [],
                ];
            }

            $groupedPermissions[$entity]['actions'][] = [
                'action' => $action,
                'id' => $permission->id,
                'checked' => in_array($permission->id, $rolePermissions), // Check if this permission is assigned to the role
            ];
        }

        $permissions = array_values($groupedPermissions); // Reset keys for iteration

        return view('pages/roles/permissions', compact('permissions', 'roleId', 'roleName', 'courses'));
    }

    public function permissionStore(Request $request)
    {
        $request->validate([
            'permission_name' => 'required|string|max:255',
        ]);
    
        // Ensure at least one checkbox is checked
        if (!$request->create && !$request->read && !$request->update && !$request->delete) {
            return redirect()->back()->with('error', 'At least one action (create, read, update, delete) must be selected.');
        }

        $permissionName = strtolower(str_replace(' ', '_', $request->permission_name));
        $actions = ['create', 'read', 'update', 'delete'];

        // Store only selected permissions
        foreach ($actions as $action) {
            if ($request->$action) {
                // Create the permission
                $permission = Permission::create([
                    'name' => "{$action}_{$permissionName}",
                ]);
        
                $roleIds = [1, 2]; // Add more role IDs here if needed

                foreach ($roleIds as $roleId) {
                    RolePermission::create([
                        'role_id' => $roleId,
                        'permission_id' => $permission->id,
                    ]);
                }
            }
        }

        return redirect()->back()->with('success', 'Permissions stored successfully!');
    }

    public function permissionUpdate(Request $request)
    {
        $request->validate([
            'role_id' => 'required|integer|exists:roles,id',
            'permission_id' => 'required|integer|exists:permissions,id',
            'action' => 'nullable|string',
            'checked' => 'required|boolean',
        ]);

        $role = Role::findOrFail($request->role_id);
        $actor = $request->user();

        if ($role->isSuperAdmin()) {
            return response()->json(['success' => false, 'message' => 'SuperAdmin always has every permission.'], 422);
        }

        // Prevent self-escalation: only a SuperAdmin may change the permissions of a role they hold.
        if (! $actor->isSuperAdmin() && $actor->roles->contains('id', $role->id)) {
            return response()->json(['success' => false, 'message' => 'You cannot change permissions for your own role.'], 403);
        }

        $roleId = $request->role_id;
        $permissionId = $request->permission_id;
        $action = $request->action;

        if ($request->checked) {
            // Add the permission
            RolePermission::updateOrCreate(
                ['role_id' => $roleId, 'permission_id' => $permissionId]
            );
        } else {
            // Remove the permission
            RolePermission::where([
                'role_id' => $roleId,
                'permission_id' => $permissionId,
            ])->delete();
        }

        return response()->json(['success' => true]);
    }
}

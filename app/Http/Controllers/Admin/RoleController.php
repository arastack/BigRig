<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Models\RoleCourse;
use App\Models\TrainingObjective;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/**
 * Roles, the permissions each one grants and, for instructor roles, the courses they teach.
 */
class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::withCount(['users', 'permissions'])
            ->orderByRaw("FIELD(portal, 'admin', 'instructor', 'student')")
            ->orderBy('role_name')
            ->get();

        $courseNames = TrainingObjective::pluck('objective', 'id');
        $coursesByRole = RoleCourse::get(['role_id', 'course_id'])
            ->groupBy('role_id')
            ->map(fn ($rows) => $rows->pluck('course_id')->map(fn ($id) => $courseNames[$id] ?? null)->filter()->unique()->values());

        return view('admin.roles.index', [
            'roles' => $roles,
            'coursesByRole' => $coursesByRole,
            'totalPermissions' => Permission::count(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'role_name' => ['required', 'string', 'max:255', Rule::unique('roles', 'role_name')],
            'role_description' => ['nullable', 'string', 'max:255'],
            'portal' => ['required', Rule::in(array_keys(Role::PORTALS))],
        ], ['role_name.unique' => 'A role with this name already exists.']);

        if (strcasecmp($data['role_name'], Role::SUPER_ADMIN) === 0) {
            return back()->withInput()->with('error', 'That name is reserved.');
        }

        $role = Role::create($data);

        return redirect()->route('user-permissions', $role)->with('success', "{$role->role_name} role created. Now choose what it can do.");
    }

    public function edit(Request $request, Role $role)
    {
        $granted = $role->permissions()->pluck('permissions.id')->all();
        $permissions = Permission::orderBy('name')->get()->keyBy('name');

        // Group by the catalogue; anything in the database but not the catalogue goes under "Other"
        $groups = collect(config('permissions.groups'))
            ->map(fn ($labels) => collect($labels)
                ->filter(fn ($label, $name) => $permissions->has($name))
                ->map(fn ($label, $name) => ['id' => $permissions[$name]->id, 'name' => $name, 'label' => $label]))
            ->filter(fn ($items) => $items->isNotEmpty());

        $catalogued = collect(config('permissions.groups'))->flatMap(fn ($labels) => array_keys($labels));
        $other = $permissions->except($catalogued->all());
        if ($other->isNotEmpty()) {
            $groups['Other'] = $other->map(fn ($p) => ['id' => $p->id, 'name' => $p->name, 'label' => ucfirst(str_replace('_', ' ', $p->name))]);
        }

        return view('admin.roles.edit', [
            'role' => $role,
            'groups' => $groups,
            'granted' => $granted,
            'courses' => TrainingObjective::orderBy('objective')->get(),
            'members' => $role->users()->with('roles')->orderBy('firstName')->orderBy('lastName')->limit(100)->get(),
            'memberCount' => $role->users()->count(),
            'roleCourses' => RoleCourse::where('role_id', $role->id)->pluck('course_id')->all(),
            'permissionError' => $this->permissionEditError($request, $role),
        ]);
    }

    public function update(Request $request, Role $role)
    {
        $data = $request->validate([
            'role_name' => ['required', 'string', 'max:255', Rule::unique('roles', 'role_name')->ignore($role->id)],
            'role_description' => ['nullable', 'string', 'max:255'],
            'portal' => ['required', Rule::in(array_keys(Role::PORTALS))],
        ], ['role_name.unique' => 'A role with this name already exists.']);

        if ($role->isProtected() && ($data['portal'] !== $role->portal || $data['role_name'] !== $role->role_name)) {
            return back()->withInput()->with('error', "The {$role->role_name} role is required by the system: its name and portal cannot be changed.");
        }

        if (! $role->isSuperAdmin() && strcasecmp($data['role_name'], Role::SUPER_ADMIN) === 0) {
            return back()->withInput()->with('error', 'That name is reserved.');
        }

        if (! $request->user()->isSuperAdmin() && $request->user()->roles->contains('id', $role->id) && $data['portal'] !== $role->portal) {
            return back()->withInput()->with('error', 'You cannot change the portal of a role you hold.');
        }

        $role->update($data);

        return back()->with('success', 'Role details saved.');
    }

    public function updatePermissions(Request $request, Role $role)
    {
        if ($error = $this->permissionEditError($request, $role)) {
            return back()->with('error', $error);
        }

        $data = $request->validate([
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['integer', 'exists:permissions,id'],
        ]);

        $ids = collect($data['permissions'] ?? [])->map(fn ($id) => (int) $id)->unique()->values();

        // No escalation: a non-SuperAdmin may only add permissions they hold themselves
        $actor = $request->user();
        if (! $actor->isSuperAdmin()) {
            $added = $ids->diff($role->permissions()->pluck('permissions.id'));
            $notHeld = \App\Models\Permission::whereIn('id', $added)->pluck('name')->diff($actor->permissionNames());
            if ($notHeld->isNotEmpty()) {
                return back()->with('error', "You can only grant permissions you have yourself. Not allowed: {$notHeld->join(', ')}.");
            }
        }

        DB::transaction(fn () => $role->permissions()->sync($ids));

        return back()->with('success', "Permissions saved. {$role->role_name} now has {$ids->count()} " . str('permission')->plural($ids->count()) . '.');
    }

    public function updateCourses(Request $request, Role $role)
    {
        if ($role->portal !== Role::PORTAL_INSTRUCTOR) {
            return back()->with('error', 'Only instructor roles teach courses.');
        }

        $data = $request->validate([
            'courses' => ['nullable', 'array'],
            'courses.*' => ['integer', 'exists:training_objectives,id'],
        ]);

        $ids = collect($data['courses'] ?? [])->map(fn ($id) => (int) $id)->unique();

        DB::transaction(function () use ($role, $ids) {
            RoleCourse::where('role_id', $role->id)->whereNotIn('course_id', $ids)->delete();
            $existing = RoleCourse::where('role_id', $role->id)->pluck('course_id');
            foreach ($ids->diff($existing) as $id) {
                RoleCourse::create(['role_id' => $role->id, 'course_id' => $id]);
            }
        });

        return back()->with('success', 'Courses saved for ' . $role->role_name . '.');
    }

    public function destroy(Role $role)
    {
        if ($role->isProtected()) {
            return back()->with('error', "The {$role->role_name} role is required by the system and cannot be deleted.");
        }

        if ($role->users()->exists()) {
            return back()->with('error', "Reassign the people who hold the {$role->role_name} role before deleting it.");
        }

        DB::transaction(function () use ($role) {
            $role->rolePermissions()->delete();
            RoleCourse::where('role_id', $role->id)->delete();
            $role->delete();
        });

        return redirect()->route('user-roles')->with('success', "{$role->role_name} role deleted.");
    }

    /**
     * Why the current user may not change this role's permissions, or null if they may.
     */
    private function permissionEditError(Request $request, Role $role): ?string
    {
        $actor = $request->user();

        if ($role->isSuperAdmin()) {
            return 'SuperAdmin always has every permission. Its permissions cannot be edited.';
        }

        // Prevent self-escalation: only a SuperAdmin may change the permissions of a role they hold.
        if (! $actor->isSuperAdmin() && $actor->roles->contains('id', $role->id)) {
            return 'You hold this role, so you cannot change its permissions. Ask a SuperAdmin.';
        }

        if (! $actor->hasPermission('update_roles_and_permissions')) {
            return 'You can view this role but not change its permissions.';
        }

        return null;
    }
}

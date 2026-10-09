<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\RoleCourse;
use App\Models\StudentInstructorDistribution;
use App\Models\TrainingObjective;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Staff accounts: administrators and instructors, their roles and website listing.
 */
class StaffController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only(['q', 'role', 'status']);

        $query = $this->staffQuery()->with('roles');

        if ($search = trim((string) $request->input('q'))) {
            $query->where(function ($q) use ($search) {
                $q->where('firstName', 'like', "%{$search}%")
                    ->orWhere('lastName', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('mobileNumber', 'like', "%{$search}%")
                    ->orWhereRaw("CONCAT(firstName, ' ', lastName) LIKE ?", ["%{$search}%"]);
            });
        }

        if ($role = $request->integer('role')) {
            $query->whereHas('roles', fn ($q) => $q->where('roles.id', $role));
        }

        // Suspended accounts are only listed for staff allowed to see them
        if (! $request->user()->hasPermission('read_suspend_user_account')) {
            $query->whereNull('suspended_at');
        }

        match ($request->input('status')) {
            'active' => $query->whereNull('suspended_at'),
            'suspended' => $query->whereNotNull('suspended_at'),
            default => null,
        };

        $staff = $query->orderBy('firstName')->orderBy('lastName')->paginate(25)->withQueryString();

        $ids = $staff->getCollection()->pluck('id');
        $studentCounts = StudentInstructorDistribution::whereIn('instructor_id', $ids)
            ->selectRaw('instructor_id, COUNT(DISTINCT student_id) as total')
            ->groupBy('instructor_id')
            ->pluck('total', 'instructor_id');

        $roles = Role::where('portal', '!=', Role::PORTAL_STUDENT)->orderBy('role_name')->get();

        return view('admin.staff.index', [
            'staff' => $staff,
            'filters' => $filters,
            'roles' => $roles,
            'assignableRoles' => $this->assignableRoles($request->user()),
            'studentCounts' => $studentCounts,
            'coursesByRole' => $this->coursesByRole(),
            'counts' => [
                'total' => $this->staffQuery()->count(),
                'active' => $this->staffQuery()->whereNull('suspended_at')->count(),
                'suspended' => $this->staffQuery()->whereNotNull('suspended_at')->count(),
                'website' => $this->staffQuery()->whereNull('suspended_at')->where('website_visibility', true)->count(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'firstName' => ['required', 'string', 'max:255'],
            'middleName' => ['nullable', 'string', 'max:255'],
            'lastName' => ['required', 'string', 'max:255'],
            'gender' => ['required', Rule::in(['Male', 'Female'])],
            'mobileNumber' => ['required', 'string', 'max:17', 'unique:users,mobileNumber'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'userRole' => ['required', 'array', 'min:1'],
            'userRole.*' => ['integer', 'exists:roles,id'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpg,png,jpeg', 'max:2048'],
            'website_visibility' => ['nullable', 'boolean'],
        ], ['userRole.required' => 'Choose at least one role.']);

        $roles = Role::whereIn('id', $data['userRole'])->get();

        if ($error = $this->roleSelectionError($roles, $request->user())) {
            return back()->withInput()->with('error', $error);
        }

        // A unique temporary password, shown once to the admin, replaces the old shared default.
        $temporaryPassword = Str::password(12, symbols: false);

        $user = User::create([
            'firstName' => $data['firstName'],
            'middleName' => $data['middleName'] ?? null,
            'lastName' => $data['lastName'],
            'gender' => $data['gender'],
            'mobileNumber' => $data['mobileNumber'],
            'email' => $data['email'],
            'password' => Hash::make($temporaryPassword),
            'profile_photo_path' => $request->hasFile('profile_photo') ? $request->file('profile_photo')->store('profile-photos', 'public') : null,
            'user_active' => true,
            'website_visibility' => $request->boolean('website_visibility'),
        ]);

        $user->roles()->sync($roles->pluck('id'));

        // Best effort: also email a link to choose their own password.
        after_response(fn () => Password::sendResetLink(['email' => $user->email]), 'Staff password link email');

        return redirect()->route('instructors')->with('success', "Account created for {$user->firstName}. Temporary password: {$temporaryPassword} — share it securely; we have also emailed them a link to choose their own password.");
    }

    public function edit(Request $request, User $staff)
    {
        abort_unless($this->isStaff($staff), 404);

        $staff->load('roles');
        $actor = $request->user();

        return view('admin.staff.edit', [
            'staff' => $staff,
            'roles' => $this->assignableRoles($actor, $staff),
            'coursesByRole' => $this->coursesByRole(),
            'students' => StudentInstructorDistribution::with(['student', 'course'])
                ->where('instructor_id', $staff->id)
                ->latest()
                ->get(),
            'canEditDetails' => $this->canManage($actor, $staff),
            'canEditRoles' => $this->canEditRoles($actor, $staff),
        ]);
    }

    public function update(Request $request, User $staff)
    {
        abort_unless($this->isStaff($staff), 404);

        $actor = $request->user();
        abort_unless($this->canManage($actor, $staff), 403, 'Only a SuperAdmin can edit a SuperAdmin account.');

        $data = $request->validate([
            'firstName' => ['required', 'string', 'max:255'],
            'middleName' => ['nullable', 'string', 'max:255'],
            'lastName' => ['required', 'string', 'max:255'],
            'gender' => ['required', Rule::in(['Male', 'Female'])],
            'mobileNumber' => ['required', 'string', 'max:17', Rule::unique('users', 'mobileNumber')->ignore($staff->id)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($staff->id)],
            'profile_photo' => ['nullable', 'image', 'mimes:jpg,png,jpeg', 'max:2048'],
            'remove_photo' => ['nullable', 'boolean'],
            'website_visibility' => ['nullable', 'boolean'],
            'userRole' => ['required_if:roles_submitted,1', 'array', 'min:1'],
            'userRole.*' => ['integer', 'exists:roles,id'],
        ], ['userRole.required_if' => 'A staff member needs at least one role.', 'userRole.min' => 'A staff member needs at least one role.']);

        if ($request->has('userRole')) {
            $newRoles = Role::whereIn('id', $data['userRole'])->get();

            if ($newRoles->pluck('id')->sort()->values()->all() !== $staff->roles->pluck('id')->sort()->values()->all()) {
                if (! $this->canEditRoles($actor, $staff)) {
                    return back()->withInput()->with('error', $staff->is($actor) ? 'You cannot change your own roles. Ask a SuperAdmin.' : 'Only a SuperAdmin can change the roles of a SuperAdmin.');
                }

                // Only roles being added are checked, so existing roles can be kept or removed
                $addedRoles = $newRoles->reject(fn (Role $role) => $staff->roles->contains('id', $role->id));
                if ($newRoles->contains(fn (Role $role) => $role->portal === Role::PORTAL_STUDENT)) {
                    return back()->withInput()->with('error', 'Staff accounts cannot be given a student role.');
                }
                if ($error = $this->roleSelectionError($addedRoles, $actor)) {
                    return back()->withInput()->with('error', $error);
                }

                $staff->roles()->sync($newRoles->pluck('id'));
            }
        }

        $photo = $staff->profile_photo_path;

        if ($request->hasFile('profile_photo')) {
            $photo = $request->file('profile_photo')->store('profile-photos', 'public');
        } elseif ($request->boolean('remove_photo')) {
            $photo = null;
        }

        if ($photo !== $staff->profile_photo_path && $staff->profile_photo_path) {
            Storage::disk('public')->delete($staff->profile_photo_path);
        }

        $staff->update([
            'firstName' => $data['firstName'],
            'middleName' => $data['middleName'] ?? null,
            'lastName' => $data['lastName'],
            'gender' => $data['gender'],
            'mobileNumber' => $data['mobileNumber'],
            'email' => $data['email'],
            'profile_photo_path' => $photo,
            // Suspended staff never appear on the public website
            'website_visibility' => $request->boolean('website_visibility') && ! $staff->isSuspended(),
        ]);

        return redirect()->route('admin.staff.edit', $staff)->with('success', "{$staff->firstName}'s account has been updated.");
    }

    public function sendPasswordLink(Request $request, User $staff)
    {
        abort_unless($this->isStaff($staff), 404);
        abort_unless($this->canManage($request->user(), $staff), 403, 'Only a SuperAdmin can reset a SuperAdmin password.');

        $status = Password::sendResetLink(['email' => $staff->email]);

        return $status === Password::RESET_LINK_SENT
            ? back()->with('success', "A password reset link has been emailed to {$staff->email}.")
            : back()->with('error', 'The reset link could not be sent: ' . __($status));
    }

    /**
     * Users holding at least one non-student role.
     */
    private function staffQuery(): Builder
    {
        return User::query()->whereHas('roles', fn ($q) => $q->where('portal', '!=', Role::PORTAL_STUDENT));
    }

    private function isStaff(User $user): bool
    {
        return $user->roles()->where('portal', '!=', Role::PORTAL_STUDENT)->exists();
    }

    /**
     * Only a SuperAdmin may manage a SuperAdmin account.
     */
    private function canManage(User $actor, User $staff): bool
    {
        return $actor->isSuperAdmin() || ! $staff->isSuperAdmin();
    }

    /**
     * Non-SuperAdmins cannot change their own roles (no self-escalation) or a SuperAdmin's roles.
     */
    private function canEditRoles(User $actor, User $staff): bool
    {
        return $actor->isSuperAdmin() || (! $staff->is($actor) && ! $staff->isSuperAdmin());
    }

    private function roleSelectionError(Collection $roles, User $actor): ?string
    {
        if ($roles->contains(fn (Role $role) => $role->portal === Role::PORTAL_STUDENT)) {
            return 'Staff accounts cannot be given a student role.';
        }

        if ($roles->contains(fn (Role $role) => $role->isSuperAdmin()) && ! $actor->isSuperAdmin()) {
            return 'Only a SuperAdmin can grant the SuperAdmin role.';
        }

        // No escalation: a non-SuperAdmin can't hand out a role carrying permissions they don't hold
        if (! $actor->isSuperAdmin()) {
            $held = $actor->permissionNames();
            $tooPowerful = $roles->first(fn (Role $role) => $role->permissions()->pluck('name')->diff($held)->isNotEmpty());
            if ($tooPowerful) {
                return "You can't grant the {$tooPowerful->role_name} role because it includes permissions you don't have.";
            }
        }

        return null;
    }

    /**
     * Roles the actor may offer: never student roles; SuperAdmin only for SuperAdmins
     * (or shown, locked, when the staff member already holds it).
     */
    private function assignableRoles(User $actor, ?User $staff = null): Collection
    {
        return Role::where('portal', '!=', Role::PORTAL_STUDENT)
            ->orderByRaw('portal = ? desc', [Role::PORTAL_ADMIN])
            ->orderBy('role_name')
            ->get()
            ->filter(fn (Role $role) => ! $role->isSuperAdmin() || $actor->isSuperAdmin() || $staff?->roles->contains('id', $role->id))
            ->values();
    }

    /**
     * Course names keyed by role id, from role_courses.
     */
    private function coursesByRole(): Collection
    {
        $courseNames = TrainingObjective::pluck('objective', 'id');

        return RoleCourse::get(['role_id', 'course_id'])
            ->groupBy('role_id')
            ->map(fn ($rows) => $rows->pluck('course_id')->map(fn ($id) => $courseNames[$id] ?? null)->filter()->unique()->values());
    }
}

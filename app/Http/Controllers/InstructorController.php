<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class InstructorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (Auth::user()->hasPermission('read_suspend_user_account')) {
            $instructors = User::whereHas('roles', function ($query) {
                        $query->where('roles.id', '!=', 10);
                    })
                    ->paginate(15);
        } else {
            $instructors = User::where('user_active', 1)
                    ->whereHas('roles', function ($query) {
                        $query->where('roles.id', '!=', 2);
                        $query->where('roles.id', '!=', 10);
                    })
                    ->paginate(15);
        }

        $instructors_count = $instructors->count();
        $roles = Role::where('id', '!=', 10)->orderBy('role_name', 'asc')->get();

        return view('pages/instructor/index', compact('instructors', 'instructors_count', 'roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'firstName' => 'required|string|max:255',
            'middleName' => 'nullable|string|max:255',
            'lastName' => 'required|string|max:255',
            'gender' => ['required', 'string', 'in:Male,Female'],
            'mobileNumber' => ['required', 'string', 'max:17', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'userRole' => 'required|array',
            'userRole.*' => 'exists:roles,id',
            'profile_photo' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
        ]);

        $roles = Role::whereIn('id', $request->input('userRole'))->get();

        if ($roles->contains(fn ($role) => $role->portal === Role::PORTAL_STUDENT)) {
            return redirect()->back()->withInput()->with('error', 'Staff accounts cannot be given the Student role.');
        }

        if ($roles->contains(fn ($role) => $role->isSuperAdmin()) && ! Auth::user()->isSuperAdmin()) {
            abort(403, 'Only a SuperAdmin can create another SuperAdmin.');
        }

        // A unique temporary password, shown once to the admin, replaces the old shared default.
        $temporaryPassword = Str::password(12, symbols: false);

        $filePath = null;

        if ($request->hasFile('profile_photo')) {
            $filePath = $request->file('profile_photo')->store('users', 'public');
        }

        // Create the user without role_id
        $user = User::create([
            'firstName' => $request->input('firstName'),
            'middleName' => $request->input('middleName'),
            'lastName' => $request->input('lastName'),
            'gender' => $request->input('gender'),
            'mobileNumber' => $request->input('mobileNumber'),
            'email' => $request->input('email'),
            'password' => Hash::make($temporaryPassword),
            'profile_photo_path' => $filePath,
            'user_active' => 1,
        ]);

        // Attach roles using the pivot table
        $user->roles()->attach($request->input('userRole'));

        // Best effort: also email a link to choose their own password.
        try {
            Password::sendResetLink(['email' => $user->email]);
        } catch (\Throwable $e) {
            report($e);
        }

        return redirect()->back()->with('success', "Account created for {$user->firstName}. Temporary password: {$temporaryPassword} — share it securely; they should change it after signing in.");
    }

    /**
     * Display the specified resource.
     */
    public function show(Instructor $instructor)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Instructor $instructor)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Instructor $instructor)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Instructor $instructor)
    {
        //
    }
}

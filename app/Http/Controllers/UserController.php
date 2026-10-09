<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Subscription;
use App\Models\Role;

class UserController extends Controller
{
    public function indexTabs()
    {
        $users = User::paginate(8);
        return view('pages/community/users-tabs', compact('users'));  
    }

    public function indexTiles()
    {
        // if (!Auth::user()->hasPermission('read_users_page')) {
        //     return redirect()->back()->with('error', 'Permission denied!');
        // }

        if (Auth::user()->hasPermission('read_suspend_user_account')) {
            $users = User::whereHas('roles', function ($query) {
                        $query->where('roles.id', 10);
                    })
                    ->paginate(15);
        } else {
            $users = User::where('user_active', 1)
                    ->whereHas('roles', function ($query) {
                        $query->where('roles.id', 10);
                    })
                    ->paginate(15);
        }
        
        return view('pages/community/users-tiles', compact('users'));
    }

    public function userProfile() {
        $userInfo = Auth::user();

        return view('pages/community/profile', compact('userInfo'));
    }    

    /**
     * Suspend an account, or lift an existing suspension.
     */
    public function deactivate($id)
    {
        $user = User::findOrFail($id);
        $actor = Auth::user();

        if ($user->is($actor)) {
            return redirect()->back()->with('error', 'You cannot suspend your own account.');
        }

        if ($user->isSuperAdmin() && ! $actor->isSuperAdmin()) {
            abort(403, 'Only a SuperAdmin can suspend a SuperAdmin account.');
        }

        if ($user->isSuspended()) {
            // Students only regain portal access if they have paid
            $user->update([
                'suspended_at' => null,
                'user_active' => ! $user->isStudent() || $user->hasCompletedPayment(),
            ]);

            return redirect()->back()->with('success', 'Account reactivated.');
        }

        $user->update([
            'suspended_at' => now(),
            'user_active' => false,
            'website_visibility' => false,
        ]);

        return redirect()->back()->with('success', 'Account suspended. The user can no longer sign in.');
    }

    public function verifyAccount($id)
    {
        $user = User::findOrFail($id);

        $user->forceFill(['email_verified_at' => $user->email_verified_at ?? now()])->save();

        return redirect()->back()->with('success', 'Email address marked as verified.');
    }
}

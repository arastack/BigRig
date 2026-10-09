<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use App\Models\ForumPost;
use App\Models\ForumComment;
use App\Models\EnrolmentBatch;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasProfilePhoto, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'enrolment_batch_id',
        'firstName',
        'middleName',
        'lastName',
        'gender',
        'mobileNumber',
        'email',
        'email_verified_at',
        'password',
        'profile_photo_path',
        'user_active',
        'suspended_at',
        'website_visibility'
    ];

    /**
     * Per-request cache of permission names, so menus and gates don't re-query.
     */
    protected ?array $cachedPermissionNames = null;

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'suspended_at' => 'datetime',
        'user_active' => 'boolean',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Relationship: One-to-One with Medicals
     */
    public function medical()
    {
        return $this->hasOne(Medical::class);
    }

    /**
     * Relationship: One-to-Many with Subscriptions
     */
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    // public function role()
    // {
    //     return $this->belongsTo(Role::class);
    // }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * Roles, loaded once per request.
     */
    protected function loadedRoles()
    {
        if (! $this->relationLoaded('roles')) {
            $this->load('roles');
        }

        return $this->roles;
    }

    public function permissionNames(): array
    {
        if ($this->cachedPermissionNames === null) {
            $this->cachedPermissionNames = Permission::query()
                ->join('role_permission', 'permissions.id', '=', 'role_permission.permission_id')
                ->whereIn('role_permission.role_id', $this->loadedRoles()->pluck('id'))
                ->distinct()
                ->pluck('permissions.name')
                ->all();
        }

        return $this->cachedPermissionNames;
    }

    public function isSuperAdmin(): bool
    {
        return $this->loadedRoles()->contains(fn ($role) => strcasecmp($role->role_name, Role::SUPER_ADMIN) === 0);
    }

    public function hasPermission($permission): bool
    {
        return $this->isSuperAdmin() || in_array($permission, $this->permissionNames(), true);
    }

    public function hasAnyPermission(array $permissions): bool
    {
        return collect($permissions)->contains(fn ($permission) => $this->hasPermission($permission));
    }

    public function hasRole($role): bool
    {
        $roles = $this->loadedRoles()->pluck('role_name')->map(fn ($r) => strtolower($r));
        $wanted = array_map('strtolower', (array) $role);

        return $roles->intersect($wanted)->isNotEmpty();
    }

    /**
     * The portal this user lands in: admin outranks instructor, which outranks student.
     */
    public function portal(): ?string
    {
        $portals = $this->loadedRoles()->pluck('portal');

        foreach ([Role::PORTAL_ADMIN, Role::PORTAL_INSTRUCTOR, Role::PORTAL_STUDENT] as $portal) {
            if ($portals->contains($portal)) {
                return $portal;
            }
        }

        return null;
    }

    public function isStudent(): bool
    {
        return $this->portal() === Role::PORTAL_STUDENT;
    }

    public function isInstructor(): bool
    {
        return $this->loadedRoles()->contains('portal', Role::PORTAL_INSTRUCTOR);
    }

    public function isStaff(): bool
    {
        return in_array($this->portal(), [Role::PORTAL_ADMIN, Role::PORTAL_INSTRUCTOR], true);
    }

    public function isSuspended(): bool
    {
        return $this->suspended_at !== null;
    }

    public function hasCompletedPayment(): bool
    {
        return $this->subscriptions()->where('payment_status', 'completed')->exists();
    }

    public function getFullNameAttribute(): string
    {
        return trim(collect([$this->firstName, $this->middleName, $this->lastName])->filter()->implode(' '));
    }

    public function forumPosts()
    {
        return $this->hasMany(ForumPost::class);
    }

    public function forumComments()
    {
        return $this->hasMany(ForumComment::class);
    }

    public function enrolmentBatch()
    {
        return $this->belongsTo(EnrolmentBatch::class, 'enrolment_batch_id');
    }

    /**
     * Check if user is a teaching instructor (not admin/management)
     */
    public function isTeachingInstructor()
    {
        return $this->isInstructor();
    }

    /**
     * Check if user has administrative role
     */
    public function isAdministrative()
    {
        return $this->portal() === Role::PORTAL_ADMIN;
    }

    /**
     * Instructor distributions relationship
     */
    public function instructorDistributions()
    {
        return $this->hasMany(StudentInstructorDistribution::class, 'instructor_id');
    }
}

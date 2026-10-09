<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    public const SUPER_ADMIN = 'SuperAdmin';
    public const STUDENT_ROLE_ID = 10;

    public const PORTAL_ADMIN = 'admin';
    public const PORTAL_INSTRUCTOR = 'instructor';
    public const PORTAL_STUDENT = 'student';

    public const PORTALS = [
        self::PORTAL_ADMIN => 'Administration',
        self::PORTAL_INSTRUCTOR => 'Instructor portal',
        self::PORTAL_STUDENT => 'Student portal',
    ];

    protected $fillable = ['role_name', 'role_description', 'portal'];

    public function isSuperAdmin(): bool
    {
        return strcasecmp($this->role_name, self::SUPER_ADMIN) === 0;
    }

    /**
     * Roles the system depends on and which must never be deleted.
     */
    public function isProtected(): bool
    {
        return $this->isSuperAdmin() || $this->id === self::STUDENT_ROLE_ID;
    }

    protected $table = 'roles';

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permission');
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function rolePermissions()
    {
        return $this->hasMany(RolePermission::class, 'role_id');
    }

}


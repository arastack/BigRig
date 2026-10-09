<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * New permission for the Admissions settings screen; granted to roles that already manage applicants.
 */
return new class extends Migration
{
    public function up(): void
    {
        $now = now();
        DB::table('permissions')->insertOrIgnore(['name' => 'manage_admission_settings', 'created_at' => $now, 'updated_at' => $now]);
        $id = DB::table('permissions')->where('name', 'manage_admission_settings')->value('id');

        $roles = DB::table('role_permission')
            ->join('permissions', 'permissions.id', '=', 'role_permission.permission_id')
            ->where('permissions.name', 'update_applicants')
            ->pluck('role_permission.role_id');

        foreach ($roles as $roleId) {
            DB::table('role_permission')->insertOrIgnore(['role_id' => $roleId, 'permission_id' => $id, 'created_at' => $now, 'updated_at' => $now]);
        }
    }

    public function down(): void
    {
        DB::table('permissions')->where('name', 'manage_admission_settings')->delete();
    }
};

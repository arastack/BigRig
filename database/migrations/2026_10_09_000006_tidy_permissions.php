<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Chats and the forum are now permission-controlled. Every existing role keeps
 * access (so nothing changes for users), and permissions that never controlled
 * anything are removed.
 */
return new class extends Migration
{
    private const OBSOLETE = ['read_calendar', 'read_management_navigation', 'delete_instructors', 'update_forum'];

    public function up(): void
    {
        $now = now();
        $catalogue = collect(config('permissions.groups'))->flatMap(fn ($group) => array_keys($group));
        foreach ($catalogue as $name) {
            DB::table('permissions')->insertOrIgnore(['name' => $name, 'created_at' => $now, 'updated_at' => $now]);
        }

        $everyone = DB::table('permissions')->whereIn('name', config('permissions.everyone'))->pluck('id');
        foreach (DB::table('roles')->pluck('id') as $roleId) {
            foreach ($everyone as $permissionId) {
                DB::table('role_permission')->insertOrIgnore(['role_id' => $roleId, 'permission_id' => $permissionId, 'created_at' => $now, 'updated_at' => $now]);
            }
        }

        // Admin and Lead Instructor moderate the forum
        $moderate = DB::table('permissions')->where('name', 'delete_forum')->value('id');
        foreach (DB::table('roles')->whereIn(DB::raw('LOWER(role_name)'), ['admin', 'lead instructor'])->pluck('id') as $roleId) {
            DB::table('role_permission')->insertOrIgnore(['role_id' => $roleId, 'permission_id' => $moderate, 'created_at' => $now, 'updated_at' => $now]);
        }

        $obsolete = DB::table('permissions')->whereIn('name', self::OBSOLETE)->pluck('id');
        DB::table('role_permission')->whereIn('permission_id', $obsolete)->delete();
        DB::table('permissions')->whereIn('id', $obsolete)->delete();
    }

    public function down(): void
    {
        // Access granted here is intentionally kept.
    }
};

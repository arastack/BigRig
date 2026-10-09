<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Which portal a role belongs to replaces hard-coded role-name lists in code.
        if (! Schema::hasColumn('roles', 'portal')) {
            Schema::table('roles', function (Blueprint $table) {
                $table->string('portal', 20)->default('admin')->after('role_description');
            });
        }

        DB::table('roles')->whereRaw('LOWER(role_name) = ?', ['student'])->update(['portal' => 'student']);
        DB::table('roles')->whereRaw('LOWER(role_name) LIKE ?', ['%instructor%'])->update(['portal' => 'instructor']);

        // Suspension is distinct from "not yet active": unpaid applicants are inactive but must still log in to pay.
        if (! Schema::hasColumn('users', 'suspended_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->timestamp('suspended_at')->nullable()->after('user_active');
            });
        }

        $this->cleanPivot('role_user', 'user_id', 'role_id', 'users');
        $this->cleanPivot('role_permission', 'role_id', 'permission_id', 'roles');

        // Register every catalogued permission.
        $now = now();
        $catalogue = collect(config('permissions.groups'))->flatMap(fn ($group) => array_keys($group));
        foreach ($catalogue as $name) {
            DB::table('permissions')->insertOrIgnore(['name' => $name, 'created_at' => $now, 'updated_at' => $now]);
        }
        $permissionIds = DB::table('permissions')->pluck('id', 'name');

        // Roles that previously held every permission keep holding every permission.
        $fullRoles = DB::table('role_permission')->select('role_id')->groupBy('role_id')
            ->havingRaw('COUNT(DISTINCT permission_id) >= ?', [39])->pluck('role_id');
        foreach ($fullRoles as $roleId) {
            $this->grant($roleId, $permissionIds->values()->all());
        }

        // Seed sensible defaults for roles that have no permissions at all.
        foreach (config('permissions.defaults') as $roleName => $rules) {
            $role = DB::table('roles')->whereRaw('LOWER(role_name) = ?', [strtolower($roleName)])->first();
            if (! $role || DB::table('role_permission')->where('role_id', $role->id)->exists()) {
                continue;
            }

            $names = in_array('*', $rules) ? $catalogue->all() : [];
            foreach ($rules as $rule) {
                if (str_starts_with($rule, '!')) {
                    $names = array_diff($names, [substr($rule, 1)]);
                } elseif ($rule !== '*') {
                    $names[] = $rule;
                }
            }

            $this->grant($role->id, $permissionIds->only($names)->values()->all());
        }
    }

    public function down(): void
    {
        Schema::table('users', fn (Blueprint $table) => $table->dropColumn('suspended_at'));
        Schema::table('roles', fn (Blueprint $table) => $table->dropColumn('portal'));
    }

    /**
     * Remove duplicate and orphaned rows, then enforce uniqueness.
     */
    private function cleanPivot(string $table, string $ownerKey, string $otherKey, string $ownerTable): void
    {
        DB::table($table)->whereNotIn($ownerKey, DB::table($ownerTable)->select('id'))->delete();

        $keep = DB::table($table)->selectRaw('MIN(id) as id')->groupBy($ownerKey, $otherKey)->pluck('id');
        DB::table($table)->whereNotIn('id', $keep)->delete();

        $index = "{$table}_{$ownerKey}_{$otherKey}_unique";
        $exists = collect(DB::select("SHOW INDEX FROM `{$table}`"))->contains(fn ($row) => $row->Key_name === $index);
        if (! $exists) {
            Schema::table($table, fn (Blueprint $t) => $t->unique([$ownerKey, $otherKey], $index));
        }
    }

    private function grant(int $roleId, array $permissionIds): void
    {
        $now = now();
        foreach ($permissionIds as $permissionId) {
            DB::table('role_permission')->insertOrIgnore([
                'role_id' => $roleId, 'permission_id' => $permissionId, 'created_at' => $now, 'updated_at' => $now,
            ]);
        }
    }
};

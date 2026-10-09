<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        $superRole = Role::create([
            'role_name' => 'SuperAdmin',
            'role_description' => 'Organization owner with unrestricted access to all system features, settings, and user permissions.'
        ]);

        $itRole = Role::create([
            'role_name' => 'IT Consultant',
            'role_description' => 'Technical advisor responsible for managing, maintaining, and improving the institution\'s digital infrastructure and software systems.'
        ]);

        $adminRole = Role::create([
            'role_name' => 'Admin',
            'role_description' => 'Responsible for managing operations and administrative tasks on behalf of the organization owner, with access to critical backend settings.'
        ]);

        $leadRole = Role::create([
            'role_name' => 'Lead Instructor',
            'role_description' => 'Head of all training instructors, oversees curriculum implementation and ensures instructor compliance with international training standards.'
        ]);

        $mvRole = Role::create([
            'role_name' => 'MV Instructor',
            'role_description' => 'Motor Vehicle Instructor responsible for training students on operating light vehicles in accordance with road safety regulations.'
        ]);

        $cmvRole = Role::create([
            'role_name' => 'CMV Instructor',
            'role_description' => 'Commercial Motor Vehicle Instructor who trains students on operating trucks and buses professionally, focusing on highway and cargo transport rules.'
        ]);

        $forkliftRole = Role::create([
            'role_name' => 'Forklift Instructor',
            'role_description' => 'Certified instructor tasked with teaching safe and efficient forklift operation for warehouse and logistics environments.'
        ]);

        $defRole = Role::create([
            'role_name' => 'Defensive Driving Instructor',
            'role_description' => 'Instructor who educates students on advanced driving techniques to anticipate and avoid road hazards, ensuring maximum safety.'
        ]);

        $safetyRole = Role::create([
            'role_name' => 'Safety & Compliance Instructor',
            'role_description' => 'Ensures students and instructors adhere to international driving laws, transportation safety standards, and compliance protocols.'
        ]);

        $studentRole = Role::create([
            'role_name' => 'Student',
            'role_description' => 'Individuals enrolled in one or more training programs provided by the driving school, across various vehicle categories.'
        ]);


        // Portal each role signs in to
        foreach ([$superRole, $itRole, $adminRole] as $role) {
            $role->update(['portal' => Role::PORTAL_ADMIN]);
        }
        foreach ([$leadRole, $mvRole, $cmvRole, $forkliftRole, $defRole, $safetyRole] as $role) {
            $role->update(['portal' => Role::PORTAL_INSTRUCTOR]);
        }
        $studentRole->update(['portal' => Role::PORTAL_STUDENT]);

        // Every permission in the catalogue (config/permissions.php)
        $catalogue = collect(config('permissions.groups'))->flatMap(fn ($group) => array_keys($group));
        $permissions = $catalogue->mapWithKeys(fn ($name) => [$name => Permission::firstOrCreate(['name' => $name])->id]);

        // SuperAdmin passes every check anyway; IT Consultant gets everything explicitly
        $superRole->permissions()->sync($permissions->values());
        $itRole->permissions()->sync($permissions->values());

        // Everyone starts with chat and forum access
        $everyone = $permissions->only(config('permissions.everyone'))->values();
        foreach (Role::all() as $role) {
            $role->permissions()->syncWithoutDetaching($everyone);
        }

        // Sensible starting points for management roles
        foreach (config('permissions.defaults') as $roleName => $rules) {
            $role = Role::where('role_name', $roleName)->first();
            if (! $role) {
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

            $role->permissions()->syncWithoutDetaching($permissions->only($names)->values());
        }
    }
}

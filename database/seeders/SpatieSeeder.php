<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class SpatieSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'manager',
            'member',
        ];

        $permissions = [
            'create project',
            'edit project',
            'delete project',
            'view project',
            'create task',
            'edit task',
            'delete task',
            'view task',
        ];

        foreach ($roles as $role) {
            Role::create([
                'name' => $role,
                'guard_name' => 'web',
            ]);
        }

        foreach ($permissions as $permission) {
            Permission::create([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        $manager = Role::findByName('manager');

        $manager->givePermissionTo([
            'create project',
            'edit project',
            'delete project',
            'view project',
            'create task',
            'edit task',
            'delete task',
            'view task',
        ]);

        $member = Role::findByName('member');

        $member->givePermissionTo([
            'view project',
            'view task',
            'edit task',
        ]);
    }
}
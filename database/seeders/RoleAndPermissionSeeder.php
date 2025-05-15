<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Create default permissions
        $permissions = [
            'create_intern',
            'add_task',
            'view_tasks',
            'edit_tasks',
            'delete_tasks',
            'manage_users',
            'manage_roles',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create default roles with their permissions
        $roles = [
            'super_admin' => $permissions,
            'admin' => ['create_intern', 'add_task', 'view_tasks', 'edit_tasks', 'delete_tasks'],
            'manager' => ['add_task', 'view_tasks', 'edit_tasks'],
            'intern' => ['view_tasks'],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            
            $permissionModels = Permission::whereIn('name', $rolePermissions)->get();
            $role->permissions()->sync($permissionModels);
        }
    }
} 
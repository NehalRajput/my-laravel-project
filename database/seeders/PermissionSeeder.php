<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        // Task related permissions
        $taskPermissions = [
            'create-task',
            'edit-task',
            'delete-task',
            'view-task',
            'assign-task',
        ];

        // User management permissions
        $userPermissions = [
            'create-intern',
            'edit-intern',
            'delete-intern',
            'view-intern',
            
            'create-admin',
            'edit-admin',
            'delete-admin',
            'view-admin',
            
            'create-manager',
            'edit-manager',
            'delete-manager',
            'view-manager',
        ];

        // System permissions
        $systemPermissions = [
            'view-dashboard',
            'manage-permissions',
            'view-reports',
            'manage-settings',
        ];

        $allPermissions = array_merge($taskPermissions, $userPermissions, $systemPermissions);

        foreach ($allPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
    }
} 
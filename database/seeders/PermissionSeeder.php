<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            ['permission' => 'manage_interns'],
            ['permission' => 'manage_tasks'],
            ['permission' => 'view_reports'],
            ['permission' => 'manage_admins'],
            ['permission' => 'assign_tasks'],
            ['permission' => 'view_tasks'],
            ['permission' => 'edit_tasks'],
            ['permission' => 'delete_tasks'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate($permission);
        }
    }
} 
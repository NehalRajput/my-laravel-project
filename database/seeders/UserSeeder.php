<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create intern role if it doesn't exist
        $internRole = Role::firstOrCreate(['name' => 'intern']);

        $users = [
            [
                'name' => 'John Intern',
                'email' => 'john@example.com',
                'password' => Hash::make('password123'),
                'role_id' => $internRole->id,
            ],
            [
                'name' => 'Sarah Intern',
                'email' => 'sarah@example.com',
                'password' => Hash::make('password123'),
                'role_id' => $internRole->id,
            ],
            [
                'name' => 'Mike Intern',
                'email' => 'mike@example.com',
                'password' => Hash::make('password123'),
                'role_id' => $internRole->id,
            ]
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}

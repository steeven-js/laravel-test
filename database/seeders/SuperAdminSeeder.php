<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['email' => 'super@admin.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password123'),
            ]
        );

        // Assigner le rôle super_admin si présent
        $role = \App\Models\UserRole::where('name', 'super_admin')->first();
        if ($role) {
            $user->user_role_id = $role->id;
            $user->save();
        }
    }
}



<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\UserRole;
use Illuminate\Database\Seeder;

class UserRoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'super_admin',
                'display_name' => 'Super Administrateur',
                'description' => 'Accès complet à toute l’interface d’administration.',
                'permissions' => [],
                'is_active' => true,
            ],
            [
                'name' => 'admin',
                'display_name' => 'Administrateur',
                'description' => 'Accès d’administration standard.',
                'permissions' => [],
                'is_active' => true,
            ],
        ];

        foreach ($roles as $data) {
            UserRole::updateOrCreate(
                ['name' => $data['name']],
                $data
            );
        }
    }
}

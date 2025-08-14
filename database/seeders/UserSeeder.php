<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Super Admin',
                'email' => 'super@admin.com',
                'password' => 'password123',
                'telephone' => '01 23 45 67 89',
                'ville' => 'Paris',
                'adresse' => '123 Rue de la Paix',
                'code_postal' => '75001',
                'pays' => 'France',
                'role' => 'super_admin',
            ],
            [
                'name' => 'Admin Principal',
                'email' => 'admin@admin.com',
                'password' => 'password123',
                'telephone' => '01 23 45 67 90',
                'ville' => 'Lyon',
                'adresse' => '456 Avenue des Champs',
                'code_postal' => '69001',
                'pays' => 'France',
                'role' => 'admin',
            ],
            [
                'name' => 'Manager Commercial',
                'email' => 'manager@admin.com',
                'password' => 'password123',
                'telephone' => '01 23 45 67 91',
                'ville' => 'Marseille',
                'adresse' => '789 Boulevard du Port',
                'code_postal' => '13001',
                'pays' => 'France',
                'role' => 'manager',
            ],
            [
                'name' => 'Commercial Senior',
                'email' => 'commercial@admin.com',
                'password' => 'password123',
                'telephone' => '01 23 45 67 92',
                'ville' => 'Bordeaux',
                'adresse' => '321 Quai des Chartrons',
                'code_postal' => '33000',
                'pays' => 'France',
                'role' => 'commercial',
            ],
            [
                'name' => 'Commercial Junior',
                'email' => 'commercial2@admin.com',
                'password' => 'password123',
                'telephone' => '01 23 45 67 93',
                'ville' => 'Toulouse',
                'adresse' => '654 Rue du Capitole',
                'code_postal' => '31000',
                'pays' => 'France',
                'role' => 'commercial',
            ],
            [
                'name' => 'Support Niveau 1',
                'email' => 'support@admin.com',
                'password' => 'password123',
                'telephone' => '01 23 45 67 94',
                'ville' => 'Nantes',
                'adresse' => '987 Place du Commerce',
                'code_postal' => '44000',
                'pays' => 'France',
                'role' => 'support',
            ],
            [
                'name' => 'Support Niveau 2',
                'email' => 'support2@admin.com',
                'password' => 'password123',
                'telephone' => '01 23 45 67 95',
                'ville' => 'Strasbourg',
                'adresse' => '147 Rue de la Cathédrale',
                'code_postal' => '67000',
                'pays' => 'France',
                'role' => 'support',
            ],
            [
                'name' => 'Lecteur Consultant',
                'email' => 'viewer@admin.com',
                'password' => 'password123',
                'telephone' => '01 23 45 67 96',
                'ville' => 'Nice',
                'adresse' => '258 Promenade des Anglais',
                'code_postal' => '06000',
                'pays' => 'France',
                'role' => 'viewer',
            ],
            [
                'name' => 'Admin RH',
                'email' => 'admin2@admin.com',
                'password' => 'password123',
                'telephone' => '01 23 45 67 97',
                'ville' => 'Lille',
                'adresse' => '369 Rue de la Monnaie',
                'code_postal' => '59000',
                'pays' => 'France',
                'role' => 'admin',
            ],
            [
                'name' => 'Manager Projets',
                'email' => 'manager2@admin.com',
                'password' => 'password123',
                'telephone' => '01 23 45 67 98',
                'ville' => 'Rennes',
                'adresse' => '741 Place de la Mairie',
                'code_postal' => '35000',
                'pays' => 'France',
                'role' => 'manager',
            ],
        ];

        foreach ($users as $userData) {
            $role = UserRole::where('name', $userData['role'])->first();

            if (! $role) {
                $this->command->warn("Rôle '{$userData['role']}' non trouvé pour l'utilisateur {$userData['email']}");

                continue;
            }

            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make($userData['password']),
                    'telephone' => $userData['telephone'],
                    'ville' => $userData['ville'],
                    'adresse' => $userData['adresse'],
                    'code_postal' => $userData['code_postal'],
                    'pays' => $userData['pays'],
                    'user_role_id' => $role->id,
                ]
            );

            $this->command->info("Utilisateur créé/mis à jour : {$user->name} ({$user->email}) - Rôle : {$role->display_name}");
        }
    }
}

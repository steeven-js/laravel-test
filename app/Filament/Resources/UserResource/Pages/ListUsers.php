<?php

declare(strict_types=1);

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\User;
use App\Models\UserRole;
use Faker\Factory as FakerFactory;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected static ?string $breadcrumb = 'Utilisateurs';

    protected static ?string $title = 'Utilisateurs';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Créer'),
            Actions\Action::make('generateFakeAdmins')
                ->label('Générer des admins factices')
                ->icon('heroicon-o-sparkles')
                ->visible(fn (): bool => Auth::user()?->userRole?->name === 'super_admin')
                ->form([
                    Forms\Components\TextInput::make('count')
                        ->label('Quantité')
                        ->numeric()
                        ->minValue(1)
                        ->maxValue(50)
                        ->default(5)
                        ->required(),
                    Forms\Components\TextInput::make('password')
                        ->label('Mot de passe')
                        ->password()
                        ->default('password')
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $count = (int) ($data['count'] ?? 0);
                    if ($count < 1) {
                        Notification::make()
                            ->title('Quantité invalide')
                            ->danger()
                            ->send();

                        return;
                    }

                    $adminRole = UserRole::firstWhere('name', 'admin');
                    if (! $adminRole) {
                        Notification::make()
                            ->title("Le rôle 'admin' est introuvable")
                            ->danger()
                            ->send();

                        return;
                    }

                    $faker = FakerFactory::create('fr_FR');

                    for ($i = 0; $i < $count; $i++) {
                        $firstName = $faker->firstName();
                        $lastName = $faker->lastName();
                        $displayName = trim($firstName . ' ' . $lastName);

                        $localPart = Str::of($firstName)->ascii()->lower() . '.' . Str::of($lastName)->ascii()->lower();
                        $domainBase = Str::of($lastName)->ascii()->lower()->replace(' ', '-');
                        $domainSuffix = $faker->randomElement(['conseil', 'services', 'digital', 'tech', 'group', 'solutions']);
                        $domain = $domainBase . '-' . $domainSuffix . '.fr';

                        $email = (string) ($localPart . '@' . $domain);
                        if (User::where('email', $email)->exists()) {
                            $email = (string) ($localPart . '.' . random_int(10, 99) . '@' . $domain);
                        }

                        User::create([
                            'name' => $displayName,
                            'email' => $email,
                            'password' => (string) $data['password'], // cast string, hash via cast
                            'user_role_id' => $adminRole->id,
                        ]);
                    }

                    Notification::make()
                        ->title($count . ' admins factices créés')
                        ->success()
                        ->send();
                })
                ->requiresConfirmation(),
        ];
    }
}

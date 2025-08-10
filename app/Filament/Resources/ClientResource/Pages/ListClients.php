<?php

declare(strict_types=1);

namespace App\Filament\Resources\ClientResource\Pages;

use App\Filament\Resources\ClientResource;
use App\Filament\Widgets\clients\ClientsStats;
use App\Models\Client;
use App\Models\Entreprise;
use Faker\Factory as FakerFactory;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListClients extends ListRecords
{
    protected static string $resource = ClientResource::class;

    protected static ?string $breadcrumb = 'Liste';

    public function getTitle(): string
    {
        return static::getResource()::getPluralModelLabel();
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Nouveau'),
            Actions\Action::make('generateFakeClients')
                ->label('Générer des clients factices')
                ->icon('heroicon-o-user-group')
                ->visible(fn (): bool => Auth::user()?->userRole?->name === 'super_admin')
                ->form([
                    Forms\Components\TextInput::make('count')
                        ->label('Quantité')
                        ->numeric()
                        ->minValue(1)
                        ->maxValue(100)
                        ->default(20)
                        ->required(),
                    Forms\Components\Select::make('withEntreprise')
                        ->label('Associer à une entreprise existante ?')
                        ->options(['oui' => 'Oui', 'non' => 'Non'])
                        ->default('oui'),
                ])
                ->action(function (array $data): void {
                    $count = (int) ($data['count'] ?? 0);
                    if ($count < 1) {
                        Notification::make()->title('Quantité invalide')->danger()->send();

                        return;
                    }

                    $faker = FakerFactory::create(config('app.faker_locale', 'fr_FR'));
                    $entrepriseIds = Entreprise::query()->pluck('id')->all();

                    for ($i = 0; $i < $count; $i++) {
                        Client::create([
                            'nom' => $faker->lastName(),
                            'prenom' => $faker->firstName(),
                            'email' => $faker->unique()->safeEmail(),
                            'telephone' => '+33' . ltrim(preg_replace('/\D+/', '', $faker->phoneNumber()), '0'),
                            'adresse' => $faker->streetAddress(),
                            'ville' => $faker->city(),
                            'code_postal' => $faker->postcode(),
                            'pays' => 'FR',
                            'actif' => true,
                            'notes' => $faker->sentence(10),
                            'entreprise_id' => ($data['withEntreprise'] ?? 'oui') === 'oui' && ! empty($entrepriseIds)
                                ? $faker->randomElement($entrepriseIds)
                                : null,
                        ]);
                    }

                    Notification::make()->title($count . ' clients factices créés')->success()->send();
                })
                ->requiresConfirmation(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ClientsStats::class,
        ];
    }
}

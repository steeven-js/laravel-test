<?php

declare(strict_types=1);

namespace App\Filament\Resources\EntrepriseResource\Pages;

use App\Filament\Resources\EntrepriseResource;
use App\Models\Entreprise;
use Faker\Factory as FakerFactory;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ListEntreprises extends ListRecords
{
    protected static string $resource = EntrepriseResource::class;

    protected static ?string $breadcrumb = 'Liste';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Nouvelle'),
            Actions\Action::make('generateFakeEntreprises')
                ->label('Générer des entreprises factices')
                ->icon('heroicon-o-building-office')
                ->visible(fn (): bool => Auth::user()?->userRole?->name === 'super_admin')
                ->form([
                    Forms\Components\TextInput::make('count')
                        ->label('Quantité')
                        ->numeric()
                        ->minValue(1)
                        ->maxValue(50)
                        ->default(10)
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $count = (int) ($data['count'] ?? 0);
                    if ($count < 1) {
                        Notification::make()->title('Quantité invalide')->danger()->send();

                        return;
                    }

                    $faker = FakerFactory::create('fr_FR');

                    for ($i = 0; $i < $count; $i++) {
                        $legal = $faker->randomElement(['SARL', 'SAS', 'SASU', 'EURL', 'SCI', 'SCOP', 'EI', 'Association']);
                        $baseName = $faker->lastName();
                        if ($faker->boolean(40)) {
                            $baseName .= ' et ' . $faker->lastName();
                        }
                        $prefix = $faker->boolean(60)
                            ? $faker->randomElement(['Boulangerie', 'Boucherie', 'Pharmacie', 'Menuiserie', 'Garage', 'Plomberie', 'Électricité', 'Informatique', 'Conseil', 'Formation', 'Restauration', 'Café', 'Coiffure', 'Immobilier']) . ' '
                            : '';
                        $companyFull = trim($legal . ' ' . $prefix . $baseName);

                        $domain = Str::slug($baseName, '-') . '.fr';
                        $email = 'contact@' . $domain;
                        $site = 'https://www.' . $domain;

                        Entreprise::create([
                            'nom' => $companyFull,
                            'nom_commercial' => Str::title($faker->words(2, true)),
                            'siret' => $faker->numerify('##############'), // 14 chiffres
                            'siren' => $faker->numerify('#########'), // 9 chiffres
                            'secteur_activite' => $faker->randomElement(['Informatique', 'Conseil', 'Formation', 'Industrie', 'Commerce', 'Services']),
                            'adresse' => $faker->streetAddress(),
                            'ville' => $faker->city(),
                            'code_postal' => $faker->postcode(),
                            'pays' => 'France',
                            'telephone' => $faker->phoneNumber(),
                            'email' => $email,
                            'site_web' => $site,
                            'actif' => true,
                            'notes' => $faker->sentence(8),
                        ]);
                    }

                    Notification::make()->title($count . ' entreprises factices créées')->success()->send();
                })
                ->requiresConfirmation(),
        ];
    }
}

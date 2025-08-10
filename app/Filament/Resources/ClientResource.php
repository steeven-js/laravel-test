<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\ClientResource\Pages;
use App\Models\Client;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Parfaitementweb\FilamentCountryField\Forms\Components\Country;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

class ClientResource extends Resource
{
    protected static ?string $modelLabel = 'Client';

    protected static ?string $pluralModelLabel = 'Clients';

    protected static ?string $navigationLabel = 'Clients';

    protected static ?string $pluralNavigationLabel = 'Clients';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static ?string $model = Client::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'CRM';

    protected static ?int $navigationSort = 10;

    public static function getModelLabel(): string
    {
        return 'Client';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Clients';
    }

    public static function getNavigationLabel(): string
    {
        return 'Clients';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Identité du client')
                    ->description('Nom, email et contacts du client')
                    ->icon('heroicon-o-user-group')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('nom')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('prenom')
                                    ->maxLength(255),
                            ]),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('email')
                                    ->email()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true)
                                    ->helperText('Doit être unique si renseigné.'),
                                PhoneInput::make('telephone')
                                    ->label('Téléphone')
                                    ->defaultCountry('FR')
                                    ->formatAsYouType(true)
                                    ->displayNumberFormat(\Ysfkaya\FilamentPhoneInput\PhoneInputNumberType::NATIONAL)
                                    ->inputNumberFormat(\Ysfkaya\FilamentPhoneInput\PhoneInputNumberType::E164),
                            ]),
                    ]),
                Forms\Components\Section::make('Adresse')
                    ->description('Adresse postale et pays')
                    ->icon('heroicon-o-map-pin')
                    ->schema([
                        Forms\Components\Textarea::make('adresse')
                            ->columnSpanFull(),
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('ville')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('code_postal')
                                    ->maxLength(255),
                                Country::make('pays')
                                    ->label('Pays')
                                    ->default('FR'),
                            ]),
                    ]),
                Forms\Components\Section::make('Paramètres')
                    ->description('Statut et entreprise rattachée')
                    ->icon('heroicon-o-adjustments-horizontal')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Toggle::make('actif')->required(),
                                Forms\Components\Select::make('entreprise_id')
                                    ->relationship('entreprise', 'nom')
                                    ->searchable()
                                    ->preload()
                                    ->label('Entreprise'),
                            ]),
                        Forms\Components\Textarea::make('notes')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(static::getEloquentQuery()->whereNull('deleted_at'))
            ->recordUrl(null)
            ->recordAction('view')
            ->columns([
                Tables\Columns\TextColumn::make('nom')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('prenom')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('telephone')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('ville')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('code_postal')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('pays')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('actif')
                    ->boolean()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('entreprise.nom')
                    ->label('Entreprise')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('actif')->boolean(),
                Tables\Filters\SelectFilter::make('entreprise_id')
                    ->relationship('entreprise', 'nom')
                    ->label('Entreprise'),
            ])
            ->searchPlaceholder('Rechercher...')
            ->emptyStateIcon('heroicon-o-user-group')
            ->emptyStateHeading('Aucun client')
            ->emptyStateDescription('Ajoutez votre premier client pour commencer.')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()->label('Nouveau client'),
            ])
            ->emptyStateIcon('heroicon-o-user-group')
            ->emptyStateHeading('Aucun client')
            ->emptyStateDescription('Ajoutez votre premier client pour commencer.')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()->label('Nouveau client'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->modal()
                    ->url(null)
                    ->modalCancelActionLabel('Fermer')
                    ->infolist([
                        Infolists\Components\Section::make('Informations personnelles')
                            ->description('Détails du profil client')
                            ->icon('heroicon-o-user')
                            ->schema([
                                Infolists\Components\TextEntry::make('nom')
                                    ->label('Nom'),
                                Infolists\Components\TextEntry::make('prenom')
                                    ->label('Prénom'),
                                Infolists\Components\TextEntry::make('email')
                                    ->label('Email'),
                                Infolists\Components\TextEntry::make('telephone')
                                    ->label('Téléphone'),
                            ]),
                        Infolists\Components\Section::make('Adresse')
                            ->description('Coordonnées géographiques')
                            ->icon('heroicon-o-map-pin')
                            ->schema([
                                Infolists\Components\TextEntry::make('adresse')
                                    ->label('Adresse'),
                                Infolists\Components\TextEntry::make('ville')
                                    ->label('Ville'),
                                Infolists\Components\TextEntry::make('code_postal')
                                    ->label('Code postal'),
                                Infolists\Components\TextEntry::make('pays')
                                    ->label('Pays'),
                            ]),
                        Infolists\Components\Section::make('Entreprise et statut')
                            ->description('Informations professionnelles')
                            ->icon('heroicon-o-building-office')
                            ->schema([
                                Infolists\Components\TextEntry::make('entreprise.nom')
                                    ->label('Entreprise'),
                                Infolists\Components\IconEntry::make('actif')
                                    ->label('Statut')
                                    ->boolean(),
                                Infolists\Components\TextEntry::make('notes')
                                    ->label('Notes')
                                    ->markdown(),
                            ]),
                        Infolists\Components\Section::make('Informations système')
                            ->description('Métadonnées techniques')
                            ->icon('heroicon-o-cog')
                            ->schema([
                                Infolists\Components\TextEntry::make('created_at')
                                    ->label('Créé le')
                                    ->dateTime(),
                                Infolists\Components\TextEntry::make('updated_at')
                                    ->label('Modifié le')
                                    ->dateTime(),
                            ]),
                    ]),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClients::route('/'),
            'create' => Pages\CreateClient::route('/create'),
            'view' => Pages\ViewClient::route('/{record}'),
            'edit' => Pages\EditClient::route('/{record}/edit'),
        ];
    }
}

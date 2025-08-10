<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\MadiniaResource\Pages;
use App\Models\Madinia;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Parfaitementweb\FilamentCountryField\Forms\Components\Country;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

class MadiniaResource extends Resource
{
    protected static ?string $modelLabel = 'Madinia';

    protected static ?string $pluralModelLabel = 'Madinia';

    protected static ?string $navigationLabel = 'Madinia';

    protected static ?string $pluralNavigationLabel = 'Madinia';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static ?string $model = Madinia::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?string $navigationGroup = 'Réglages';

    protected static ?int $navigationSort = 10;

    public static function getModelLabel(): string
    {
        return 'Paramètres Madinia';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Paramètres Madinia';
    }

    public static function getNavigationLabel(): string
    {
        return 'Madinia';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Société')
                    ->description('Données d’entreprise et contact principal')
                    ->icon('heroicon-o-building-storefront')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('name')->required()->maxLength(255)->default('Madin.IA'),
                                Forms\Components\Select::make('contact_principal_id')->relationship('contactPrincipal', 'name')->searchable()->preload()->label('Contact principal'),
                            ]),
                        Forms\Components\Grid::make(3)
                            ->schema([
                                PhoneInput::make('telephone')
                                    ->label('Téléphone')
                                    ->defaultCountry('FR')
                                    ->formatAsYouType(true)
                                    ->displayNumberFormat(\Ysfkaya\FilamentPhoneInput\PhoneInputNumberType::NATIONAL)
                                    ->inputNumberFormat(\Ysfkaya\FilamentPhoneInput\PhoneInputNumberType::E164),
                                Forms\Components\TextInput::make('email')->email()->maxLength(255),
                                Forms\Components\TextInput::make('site_web')->maxLength(255),
                            ]),
                    ]),
                Forms\Components\Section::make('Informations légales')
                    ->description('SIRET, NDA, pays et adresse')
                    ->icon('heroicon-o-scale')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('siret')->maxLength(255),
                                Forms\Components\TextInput::make('numero_nda')->maxLength(255),
                                Country::make('pays')->required()->label('Pays')->default('FR'),
                            ]),
                        Forms\Components\Textarea::make('adresse')->columnSpanFull(),
                        Forms\Components\Textarea::make('description')->columnSpanFull(),
                    ]),
                Forms\Components\Section::make('Réseaux & Banque')
                    ->description('Réseaux sociaux et informations bancaires')
                    ->icon('heroicon-o-banknotes')
                    ->schema([
                        Forms\Components\KeyValue::make('reseaux_sociaux')->label('Réseaux sociaux (JSON)')->columnSpanFull()->keyLabel('Réseau')->valueLabel('Lien')->addButtonLabel('Ajouter un réseau')->reorderable()->nullable(),
                        Forms\Components\Grid::make(4)
                            ->schema([
                                Forms\Components\TextInput::make('nom_compte_bancaire')->maxLength(255),
                                Forms\Components\TextInput::make('nom_banque')->maxLength(255),
                                Forms\Components\TextInput::make('numero_compte')->maxLength(255),
                                Forms\Components\TextInput::make('iban_bic_swift')->maxLength(255),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('contactPrincipal.name')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('telephone')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('site_web')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('siret')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('numero_nda')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('pays')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('nom_compte_bancaire')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('nom_banque')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('numero_compte')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('iban_bic_swift')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                //
            ])
            ->searchPlaceholder('Rechercher...')
            ->emptyStateIcon('heroicon-o-building-storefront')
            ->emptyStateHeading('Aucun paramètre Madinia')
            ->emptyStateDescription('Ajoutez votre première configuration pour commencer.')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()->label('Nouvelle configuration'),
            ])
            ->actions([
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
            'index' => Pages\ListMadinias::route('/'),
            'create' => Pages\CreateMadinia::route('/create'),
            'edit' => Pages\EditMadinia::route('/{record}/edit'),
        ];
    }
}

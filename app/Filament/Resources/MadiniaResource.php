<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MadiniaResource\Pages;
use App\Filament\Resources\MadiniaResource\RelationManagers;
use App\Models\Madinia;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MadiniaResource extends Resource
{
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
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->default('Madin.IA'),
                Forms\Components\Select::make('contact_principal_id')
                    ->relationship('contactPrincipal', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Contact principal'),
                Forms\Components\TextInput::make('telephone')
                    ->tel()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->maxLength(255),
                Forms\Components\TextInput::make('site_web')
                    ->maxLength(255),
                Forms\Components\TextInput::make('siret')
                    ->maxLength(255),
                Forms\Components\TextInput::make('numero_nda')
                    ->maxLength(255),
                Forms\Components\TextInput::make('pays')
                    ->required()
                    ->maxLength(255)
                    ->default('France'),
                Forms\Components\Textarea::make('adresse')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
                Forms\Components\KeyValue::make('reseaux_sociaux')
                    ->label('Réseaux sociaux (JSON)')
                    ->columnSpanFull()
                    ->keyLabel('Réseau')
                    ->valueLabel('Lien')
                    ->addButtonLabel('Ajouter un réseau')
                    ->reorderable()
                    ->nullable(),
                Forms\Components\TextInput::make('nom_compte_bancaire')
                    ->maxLength(255),
                Forms\Components\TextInput::make('nom_banque')
                    ->maxLength(255),
                Forms\Components\TextInput::make('numero_compte')
                    ->maxLength(255),
                Forms\Components\TextInput::make('iban_bic_swift')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('contactPrincipal.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('telephone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('site_web')
                    ->searchable(),
                Tables\Columns\TextColumn::make('siret')
                    ->searchable(),
                Tables\Columns\TextColumn::make('numero_nda')
                    ->searchable(),
                Tables\Columns\TextColumn::make('pays')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nom_compte_bancaire')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nom_banque')
                    ->searchable(),
                Tables\Columns\TextColumn::make('numero_compte')
                    ->searchable(),
                Tables\Columns\TextColumn::make('iban_bic_swift')
                    ->searchable(),
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

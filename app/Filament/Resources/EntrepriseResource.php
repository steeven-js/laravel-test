<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EntrepriseResource\Pages;
use App\Filament\Resources\EntrepriseResource\RelationManagers;
use App\Models\Entreprise;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EntrepriseResource extends Resource
{
    protected static ?string $model = Entreprise::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    protected static ?string $navigationGroup = 'CRM';
    protected static ?int $navigationSort = 20;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nom')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('nom_commercial')
                    ->maxLength(255),
                Forms\Components\TextInput::make('siret')
                    ->maxLength(255),
                Forms\Components\TextInput::make('siren')
                    ->maxLength(255),
                Forms\Components\TextInput::make('secteur_activite')
                    ->maxLength(255),
                Forms\Components\Select::make('secteur_activite_id')
                    ->label("Secteur d'activité (référence)")
                    ->relationship('secteurActivite', 'libelle')
                    ->searchable()
                    ->preload(),
                Forms\Components\Textarea::make('adresse')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('ville')
                    ->maxLength(255),
                Forms\Components\TextInput::make('code_postal')
                    ->maxLength(255),
                Forms\Components\TextInput::make('pays')
                    ->maxLength(255)
                    ->default('France'),
                Forms\Components\TextInput::make('telephone')
                    ->tel()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->maxLength(255),
                Forms\Components\TextInput::make('site_web')
                    ->maxLength(255),
                Forms\Components\Toggle::make('actif')
                    ->required(),
                Forms\Components\Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nom')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nom_commercial')
                    ->searchable(),
                Tables\Columns\TextColumn::make('siret')
                    ->searchable(),
                Tables\Columns\TextColumn::make('siren')
                    ->searchable(),
                Tables\Columns\TextColumn::make('secteur_activite')
                    ->searchable(),
                Tables\Columns\TextColumn::make('secteurActivite.libelle')
                    ->label("Secteur (réf)")
                    ->searchable(),
                Tables\Columns\TextColumn::make('ville')
                    ->searchable(),
                Tables\Columns\TextColumn::make('code_postal')
                    ->searchable(),
                Tables\Columns\TextColumn::make('pays')
                    ->searchable(),
                Tables\Columns\TextColumn::make('telephone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('site_web')
                    ->searchable(),
                Tables\Columns\IconColumn::make('actif')
                    ->boolean(),
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
            'index' => Pages\ListEntreprises::route('/'),
            'create' => Pages\CreateEntreprise::route('/create'),
            'edit' => Pages\EditEntreprise::route('/{record}/edit'),
        ];
    }
}

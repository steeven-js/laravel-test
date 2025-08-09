<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceResource\Pages;
use App\Filament\Resources\ServiceResource\RelationManagers;
use App\Models\Service;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';
    protected static ?string $navigationGroup = 'Référentiels';
    protected static ?int $navigationSort = 10;

    public static function getModelLabel(): string
    {
        return 'Service';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Services';
    }

    public static function getNavigationLabel(): string
    {
        return 'Services';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nom')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('code')
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->helperText('Code unique du service (optionnel).'),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('prix_ht')
                    ->numeric()
                    ->inputMode('decimal')
                    ->prefix('€')
                    ->helperText('Prix unitaire hors taxes.'),
                Forms\Components\TextInput::make('qte_defaut')
                    ->numeric()
                    ->default(1)
                    ->minValue(1),
                Forms\Components\Select::make('unite')
                    ->required()
                    ->options([
                        'heure' => 'Heure',
                        'journee' => 'Journée',
                        'semaine' => 'Semaine',
                        'mois' => 'Mois',
                        'unite' => 'Unité',
                        'forfait' => 'Forfait',
                        'licence' => 'Licence',
                    ])
                    ->default('heure'),
                Forms\Components\Toggle::make('actif'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nom')
                    ->searchable(),
                Tables\Columns\TextColumn::make('code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('prix_ht')
                    ->money('EUR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('qte_defaut')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('unite')
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
                Tables\Filters\TernaryFilter::make('actif')
                    ->boolean(),
                Tables\Filters\SelectFilter::make('unite')
                    ->options([
                        'heure' => 'Heure',
                        'journee' => 'Journée',
                        'semaine' => 'Semaine',
                        'mois' => 'Mois',
                        'unite' => 'Unité',
                        'forfait' => 'Forfait',
                        'licence' => 'Licence',
                    ]),
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
            'index' => Pages\ListServices::route('/'),
            'create' => Pages\CreateService::route('/create'),
            'edit' => Pages\EditService::route('/{record}/edit'),
        ];
    }
}

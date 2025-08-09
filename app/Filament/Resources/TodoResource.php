<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\TodoResource\Pages;
use App\Models\Todo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TodoResource extends Resource
{
    protected static ?string $model = Todo::class;

    protected static ?string $navigationIcon = 'heroicon-o-check-circle';

    protected static ?string $navigationGroup = 'Support';

    protected static ?int $navigationSort = 20;

    public static function getModelLabel(): string
    {
        return 'Tâche';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Tâches';
    }

    public static function getNavigationLabel(): string
    {
        return 'Tâches';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Tâche')
                    ->description('Titre et description')
                    ->icon('heroicon-o-check-circle')
                    ->schema([
                        Forms\Components\TextInput::make('titre')->required()->maxLength(255),
                        Forms\Components\Textarea::make('description')->columnSpanFull(),
                    ]),
                Forms\Components\Section::make('Paramètres')
                    ->description('État, ordre et priorité')
                    ->icon('heroicon-o-adjustments-horizontal')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Toggle::make('termine')->required(),
                                Forms\Components\TextInput::make('ordre')->required()->numeric()->minValue(0)->default(0),
                                Forms\Components\Select::make('priorite')->label('Priorité')->required()->options([
                                    'faible' => 'Faible',
                                    'normale' => 'Normale',
                                    'haute' => 'Haute',
                                    'critique' => 'Critique',
                                ])->default('normale'),
                            ]),
                        Forms\Components\DatePicker::make('date_echeance'),
                    ]),
                Forms\Components\Section::make('Liens')
                    ->description('Client concerné et créateur')
                    ->icon('heroicon-o-link')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('client_id')->label('Client')->relationship('client', 'nom')->searchable()->preload()->required(),
                                Forms\Components\Select::make('user_id')->label('Créateur')->relationship('user', 'name')->searchable()->preload()->required(),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(null)
            ->recordAction('view')
            ->columns([
                Tables\Columns\TextColumn::make('titre')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\IconColumn::make('termine')
                    ->boolean()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('ordre')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('priorite')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('date_echeance')
                    ->date()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('client.nom')
                    ->label('Client')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Créateur')
                    ->sortable()
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
                Tables\Filters\TernaryFilter::make('termine')->label('Terminé')->boolean(),
                Tables\Filters\SelectFilter::make('priorite')->options([
                    'faible' => 'Faible',
                    'normale' => 'Normale',
                    'haute' => 'Haute',
                    'critique' => 'Critique',
                ]),
                Tables\Filters\SelectFilter::make('client_id')->relationship('client', 'nom')->label('Client'),
                Tables\Filters\SelectFilter::make('user_id')->relationship('user', 'name')->label('Créateur'),
            ])
            ->searchPlaceholder('Rechercher...')
            ->emptyStateIcon('heroicon-o-check-circle')
            ->emptyStateHeading('Aucune tâche')
            ->emptyStateDescription('Ajoutez votre première tâche pour commencer.')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()->label('Nouvelle tâche'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->infolist([
                        Infolists\Components\Section::make('Tâche')
                            ->description('Titre et description')
                            ->icon('heroicon-o-check-circle')
                            ->schema([
                                Infolists\Components\TextEntry::make('titre')
                                    ->label('Titre'),
                                Infolists\Components\TextEntry::make('description')
                                    ->label('Description')
                                    ->markdown(),
                            ]),
                        Infolists\Components\Section::make('Paramètres')
                            ->description('État, ordre et priorité')
                            ->icon('heroicon-o-adjustments-horizontal')
                            ->schema([
                                Infolists\Components\IconEntry::make('termine')
                                    ->label('Terminé')
                                    ->boolean(),
                                Infolists\Components\TextEntry::make('ordre')
                                    ->label('Ordre'),
                                Infolists\Components\TextEntry::make('priorite')
                                    ->label('Priorité'),
                                Infolists\Components\TextEntry::make('date_echeance')
                                    ->label('Date d\'échéance')
                                    ->date(),
                            ]),
                        Infolists\Components\Section::make('Liens')
                            ->description('Client concerné et créateur')
                            ->icon('heroicon-o-link')
                            ->schema([
                                Infolists\Components\TextEntry::make('client.nom')
                                    ->label('Client'),
                                Infolists\Components\TextEntry::make('user.name')
                                    ->label('Créateur'),
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
            'index' => Pages\ListTodos::route('/'),
            'create' => Pages\CreateTodo::route('/create'),
            'edit' => Pages\EditTodo::route('/{record}/edit'),
        ];
    }
}

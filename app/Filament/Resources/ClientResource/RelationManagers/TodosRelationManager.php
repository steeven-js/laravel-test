<?php

declare(strict_types=1);

namespace App\Filament\Resources\ClientResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class TodosRelationManager extends RelationManager
{
    protected static string $relationship = 'todos';

    protected static ?string $title = 'Tâches';

    public function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordUrl(null)
            ->recordAction('view')
            ->reorderable('ordre')
            ->defaultSort('ordre', 'asc')
            ->columns([
                Tables\Columns\TextColumn::make('ordre')
                    ->label('#')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('titre')
                    ->label('Tâche')
                    ->searchable()
                    ->weight('medium')
                    ->description(fn ($record) => $record->description ? Str::limit($record->description, 60) : null),
                Tables\Columns\IconColumn::make('termine')
                    ->label('')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->size('lg')
                    ->action(function ($record) {
                        $record->update(['termine' => ! $record->termine]);
                    }),
                Tables\Columns\TextColumn::make('priorite')
                    ->label('Priorité')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'critique' => 'danger',
                        'haute' => 'warning',
                        'normale' => 'info',
                        'faible' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'critique' => '🔥 Critique',
                        'haute' => '⚡ Haute',
                        'normale' => '📋 Normale',
                        'faible' => '💤 Faible',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('date_echeance')
                    ->label('Échéance')
                    ->date('d/m/Y')
                    ->sortable()
                    ->color(fn ($record) => $record->date_echeance && $record->date_echeance->isPast() && ! $record->termine ? 'danger' : 'default')
                    ->description(fn ($record) => $record->date_echeance ? $record->date_echeance->diffForHumans() : null),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Assigné à')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('termine')
                    ->label('Terminé')
                    ->boolean()
                    ->trueLabel('Terminées')
                    ->falseLabel('En cours')
                    ->placeholder('Toutes'),
                Tables\Filters\SelectFilter::make('priorite')
                    ->label('Priorité')
                    ->options([
                        'critique' => 'Critique',
                        'haute' => 'Haute',
                        'normale' => 'Normale',
                        'faible' => 'Faible',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Nouvelle tâche')
                    ->modalHeading('Nouvelle tâche')
                    ->form([
                        \Filament\Forms\Components\TextInput::make('titre')
                            ->label('Titre')
                            ->required()
                            ->maxLength(255),
                        \Filament\Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->rows(3)
                            ->maxLength(1000),
                        \Filament\Forms\Components\Select::make('priorite')
                            ->label('Priorité')
                            ->options([
                                'faible' => 'Faible',
                                'normale' => 'Normale',
                                'haute' => 'Haute',
                                'critique' => 'Critique',
                            ])
                            ->default('normale')
                            ->required(),
                        \Filament\Forms\Components\DatePicker::make('date_echeance')
                            ->label('Date d\'échéance')
                            ->minDate(now()),
                        \Filament\Forms\Components\Select::make('user_id')
                            ->label('Assigné à')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload(),
                    ])
                    ->mutateFormDataUsing(function (array $data): array {
                        // Définir l'ordre automatiquement (dernière position)
                        $maxOrder = $this->getOwnerRecord()->todos()->max('ordre') ?? 0;
                        $data['ordre'] = $maxOrder + 1;
                        $data['termine'] = false;

                        return $data;
                    })
                    ->createAnother(false),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->infolist([
                            Infolists\Components\Section::make('Informations générales')
                                ->description('Détails de la tâche')
                                ->icon('heroicon-o-check-circle')
                                ->schema([
                                    Infolists\Components\Grid::make(2)
                                        ->schema([
                                            Infolists\Components\TextEntry::make('titre')
                                                ->label('Titre'),
                                            Infolists\Components\TextEntry::make('ordre')
                                                ->label('Ordre'),
                                            Infolists\Components\TextEntry::make('priorite')
                                                ->label('Priorité'),
                                            Infolists\Components\IconEntry::make('termine')
                                                ->label('Terminé')
                                                ->boolean(),
                                        ]),
                                    Infolists\Components\TextEntry::make('description')
                                        ->label('Description')
                                        ->columnSpanFull(),
                                ]),
                            Infolists\Components\Section::make('Planning')
                                ->description('Dates et échéances')
                                ->icon('heroicon-o-calendar')
                                ->schema([
                                    Infolists\Components\Grid::make(2)
                                        ->schema([
                                            Infolists\Components\TextEntry::make('date_echeance')
                                                ->label('Date d\'échéance')
                                                ->date('d/m/Y'),
                                            Infolists\Components\TextEntry::make('created_at')
                                                ->label('Créé le')
                                                ->dateTime('d/m/Y H:i'),
                                        ]),
                                ]),
                            Infolists\Components\Section::make('Responsabilités')
                                ->description('Personnes impliquées')
                                ->icon('heroicon-o-user-group')
                                ->schema([
                                    Infolists\Components\Grid::make(2)
                                        ->schema([
                                            Infolists\Components\TextEntry::make('user.name')
                                                ->label('Assigné à'),
                                            Infolists\Components\TextEntry::make('updated_at')
                                                ->label('Modifié le')
                                                ->dateTime('d/m/Y H:i'),
                                        ]),
                                ]),
                        ]),
                    Tables\Actions\EditAction::make()
                        ->label('Modifier')
                        ->form([
                            \Filament\Forms\Components\TextInput::make('titre')
                                ->label('Titre')
                                ->required()
                                ->maxLength(255),
                            \Filament\Forms\Components\Textarea::make('description')
                                ->label('Description')
                                ->rows(3)
                                ->maxLength(1000),
                            \Filament\Forms\Components\Select::make('priorite')
                                ->label('Priorité')
                                ->options([
                                    'faible' => 'Faible',
                                    'normale' => 'Normale',
                                    'haute' => 'Haute',
                                    'critique' => 'Critique',
                                ])
                                ->required(),
                            \Filament\Forms\Components\DatePicker::make('date_echeance')
                                ->label('Date d\'échéance')
                                ->minDate(now()),
                            \Filament\Forms\Components\Select::make('user_id')
                                ->label('Assigné à')
                                ->relationship('user', 'name')
                                ->searchable()
                                ->preload(),
                            \Filament\Forms\Components\Toggle::make('termine')
                                ->label('Marquer comme terminée')
                                ->default(false),
                        ]),
                    Tables\Actions\DeleteAction::make()->label('Supprimer'),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('markAsDone')
                        ->label('Marquer comme terminées')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                $record->update(['termine' => true]);
                            });
                        })
                        ->requiresConfirmation(),
                    Tables\Actions\BulkAction::make('markAsUndone')
                        ->label('Marquer comme non terminées')
                        ->icon('heroicon-o-x-circle')
                        ->color('gray')
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                $record->update(['termine' => false]);
                            });
                        })
                        ->requiresConfirmation(),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Aucune tâche')
            ->emptyStateDescription('Créez votre première tâche pour commencer.')
            ->emptyStateIcon('heroicon-o-check-circle')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Créer une tâche'),
            ]);
    }
}

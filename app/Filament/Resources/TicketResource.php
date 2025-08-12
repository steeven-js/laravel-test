<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\TicketResource\Pages;
use App\Models\Ticket;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TicketResource extends Resource
{
    protected static ?string $modelLabel = 'Ticket';

    protected static ?string $pluralModelLabel = 'Tickets';

    protected static ?string $navigationLabel = 'Tickets';

    protected static ?string $pluralNavigationLabel = 'Tickets';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static ?string $model = Ticket::class;

    protected static ?string $navigationIcon = 'heroicon-o-lifebuoy';

    protected static ?string $navigationGroup = 'Support';

    protected static ?int $navigationSort = 10;

    public static function getModelLabel(): string
    {
        return 'Ticket';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Tickets';
    }

    public static function getNavigationLabel(): string
    {
        return 'Tickets';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Ticket')
                    ->description('Informations principales du ticket')
                    ->icon('heroicon-o-lifebuoy')
                    ->schema([
                        Forms\Components\TextInput::make('titre')->required()->maxLength(255),
                        Forms\Components\Textarea::make('description')->required()->columnSpanFull(),
                    ]),
                Forms\Components\Section::make('Classification')
                    ->description('Priorité, statut et type')
                    ->icon('heroicon-o-flag')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('priorite')->label('Priorité')->required()->options([
                                    'faible' => 'Faible',
                                    'normale' => 'Normale',
                                    'haute' => 'Haute',
                                    'critique' => 'Critique',
                                ])->default('normale'),
                                Forms\Components\Select::make('statut')->label('Statut')->required()->options([
                                    'ouvert' => 'Ouvert',
                                    'en_cours' => 'En cours',
                                    'resolu' => 'Résolu',
                                    'ferme' => 'Fermé',
                                ])->default('ouvert'),
                                Forms\Components\Select::make('type')->label('Type')->required()->options([
                                    'bug' => 'Bug',
                                    'demande' => 'Demande',
                                    'incident' => 'Incident',
                                    'question' => 'Question',
                                    'autre' => 'Autre',
                                ])->default('incident'),
                            ]),
                    ]),
                Forms\Components\Section::make('Attribution')
                    ->description('Client, assignation et créateur')
                    ->icon('heroicon-o-user-plus')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('client_id')->label('Client')->relationship('client', 'nom')->searchable()->preload()->required(),
                                Forms\Components\Select::make('user_id')->label('Assigné à')->relationship('user', 'name')->searchable()->preload()->required(),
                                Forms\Components\Select::make('created_by')->label('Créé par')->relationship('creator', 'name')->searchable()->preload()->required(),
                            ]),
                    ]),
                Forms\Components\Section::make('Suivi & temps')
                    ->description('Dates, temps et visibilité')
                    ->icon('heroicon-o-clock')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DateTimePicker::make('date_resolution'),
                                Forms\Components\DateTimePicker::make('date_echeance'),
                            ]),
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('temps_estime')->numeric()->minValue(0)->suffix('h'),
                                Forms\Components\TextInput::make('temps_passe')->required()->numeric()->minValue(0)->default(0)->suffix('h'),
                                Forms\Components\TextInput::make('progression')->required()->numeric()->minValue(0)->maxValue(100)->step(1)->default(0)->suffix('%'),
                            ]),
                        Forms\Components\Toggle::make('visible_client')->required(),
                    ]),
                Forms\Components\Section::make('Résolution')
                    ->description('Notes internes et solution')
                    ->icon('heroicon-o-pencil-square')
                    ->schema([
                        Forms\Components\Textarea::make('notes_internes')->columnSpanFull(),
                        Forms\Components\Textarea::make('solution')->columnSpanFull(),
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
                Tables\Columns\TextColumn::make('priorite')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('statut')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('type')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('client.nom')
                    ->label('Client')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Assigné à')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('creator.name')
                    ->label('Créé par')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('date_resolution')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('date_echeance')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('temps_estime')
                    ->suffix('h')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('temps_passe')
                    ->suffix('h')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('progression')
                    ->suffix('%')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\IconColumn::make('visible_client')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('priorite')->options([
                    'faible' => 'Faible',
                    'normale' => 'Normale',
                    'haute' => 'Haute',
                    'critique' => 'Critique',
                ]),
                Tables\Filters\SelectFilter::make('statut')->options([
                    'ouvert' => 'Ouvert',
                    'en_cours' => 'En cours',
                    'resolu' => 'Résolu',
                    'ferme' => 'Fermé',
                ]),
                Tables\Filters\SelectFilter::make('type')->options([
                    'bug' => 'Bug',
                    'demande' => 'Demande',
                    'incident' => 'Incident',
                    'question' => 'Question',
                    'autre' => 'Autre',
                ]),
                Tables\Filters\TernaryFilter::make('visible_client')->label('Visible client')->boolean(),
                Tables\Filters\SelectFilter::make('client_id')->relationship('client', 'nom')->label('Client'),
                Tables\Filters\SelectFilter::make('user_id')->relationship('user', 'name')->label('Assigné à'),
            ])
            ->searchPlaceholder('Rechercher...')
            ->emptyStateIcon('heroicon-o-lifebuoy')
            ->emptyStateHeading('Aucun ticket')
            ->emptyStateDescription('Ajoutez votre premier ticket pour commencer.')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()->label('Nouveau ticket'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->modal()
                        ->url(null)
                        ->modalCancelActionLabel('Fermer')
                        ->infolist([
                            Infolists\Components\Section::make('Ticket')
                                ->description('Informations principales du ticket')
                                ->icon('heroicon-o-lifebuoy')
                                ->schema([
                                    Infolists\Components\TextEntry::make('titre')
                                        ->label('Titre'),
                                    Infolists\Components\TextEntry::make('description')
                                        ->label('Description')
                                        ->markdown()
                                        ->columnSpanFull(),
                                ]),
                            Infolists\Components\Section::make('Classification')
                                ->description('Priorité, statut et type')
                                ->icon('heroicon-o-flag')
                                ->schema([
                                    Infolists\Components\Grid::make(3)
                                        ->schema([
                                            Infolists\Components\TextEntry::make('priorite')
                                                ->label('Priorité'),
                                            Infolists\Components\TextEntry::make('statut')
                                                ->label('Statut'),
                                            Infolists\Components\TextEntry::make('type')
                                                ->label('Type'),
                                        ]),
                                ]),
                            Infolists\Components\Section::make('Attribution')
                                ->description('Client, assignation et créateur')
                                ->icon('heroicon-o-user-plus')
                                ->schema([
                                    Infolists\Components\Grid::make(3)
                                        ->schema([
                                            Infolists\Components\TextEntry::make('client.nom')
                                                ->label('Client'),
                                            Infolists\Components\TextEntry::make('user.name')
                                                ->label('Assigné à'),
                                            Infolists\Components\TextEntry::make('creator.name')
                                                ->label('Créé par'),
                                        ]),
                                ]),
                            Infolists\Components\Section::make('Suivi & temps')
                                ->description('Dates, temps et visibilité')
                                ->icon('heroicon-o-clock')
                                ->schema([
                                    Infolists\Components\Grid::make(2)
                                        ->schema([
                                            Infolists\Components\TextEntry::make('date_resolution')
                                                ->label('Date de résolution')
                                                ->dateTime('d/m/Y H:i'),
                                            Infolists\Components\TextEntry::make('date_echeance')
                                                ->label('Date d\'échéance')
                                                ->dateTime('d/m/Y H:i'),
                                            Infolists\Components\TextEntry::make('temps_estime')
                                                ->label('Temps estimé')
                                                ->suffix('h'),
                                            Infolists\Components\TextEntry::make('temps_passe')
                                                ->label('Temps passé')
                                                ->suffix('h'),
                                            Infolists\Components\TextEntry::make('progression')
                                                ->label('Progression')
                                                ->suffix('%'),
                                            Infolists\Components\IconEntry::make('visible_client')
                                                ->label('Visible client')
                                                ->boolean(),
                                        ]),
                                ]),
                            Infolists\Components\Section::make('Informations système')
                                ->description('Métadonnées techniques')
                                ->icon('heroicon-o-cog')
                                ->schema([
                                    Infolists\Components\Grid::make(2)
                                        ->schema([
                                            Infolists\Components\TextEntry::make('created_at')
                                                ->label('Créé le')
                                                ->dateTime('d/m/Y H:i'),
                                            Infolists\Components\TextEntry::make('updated_at')
                                                ->label('Modifié le')
                                                ->dateTime('d/m/Y H:i'),
                                        ]),
                                ]),
                        ]),
                    Tables\Actions\EditAction::make(),
                ]),
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
            'index' => Pages\ListTickets::route('/'),
            'create' => Pages\CreateTicket::route('/create'),
            'edit' => Pages\EditTicket::route('/{record}/edit'),
        ];
    }
}

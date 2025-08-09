<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TicketResource\Pages;
use App\Filament\Resources\TicketResource\RelationManagers;
use App\Models\Ticket;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TicketResource extends Resource
{
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
                Forms\Components\TextInput::make('titre')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Select::make('priorite')
                    ->label('Priorité')
                    ->required()
                    ->options([
                        'faible' => 'Faible',
                        'normale' => 'Normale',
                        'haute' => 'Haute',
                        'critique' => 'Critique',
                    ])
                    ->default('normale'),
                Forms\Components\Select::make('statut')
                    ->label('Statut')
                    ->required()
                    ->options([
                        'ouvert' => 'Ouvert',
                        'en_cours' => 'En cours',
                        'resolu' => 'Résolu',
                        'ferme' => 'Fermé',
                    ])
                    ->default('ouvert'),
                Forms\Components\Select::make('type')
                    ->label('Type')
                    ->required()
                    ->options([
                        'bug' => 'Bug',
                        'demande' => 'Demande',
                        'incident' => 'Incident',
                        'question' => 'Question',
                        'autre' => 'Autre',
                    ])
                    ->default('incident'),
                Forms\Components\Select::make('client_id')
                    ->label('Client')
                    ->relationship('client', 'nom')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('user_id')
                    ->label('Assigné à')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('created_by')
                    ->label('Créé par')
                    ->relationship('creator', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Textarea::make('notes_internes')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('solution')
                    ->columnSpanFull(),
                Forms\Components\DateTimePicker::make('date_resolution'),
                Forms\Components\DateTimePicker::make('date_echeance'),
                Forms\Components\TextInput::make('temps_estime')
                    ->numeric()
                    ->minValue(0)
                    ->suffix('h'),
                Forms\Components\TextInput::make('temps_passe')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->default(0)
                    ->suffix('h'),
                Forms\Components\TextInput::make('progression')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100)
                    ->step(1)
                    ->default(0)
                    ->suffix('%'),
                Forms\Components\Toggle::make('visible_client')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('titre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('priorite')
                    ->searchable(),
                Tables\Columns\TextColumn::make('statut')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('client.nom')
                    ->label('Client')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Assigné à')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('creator.name')
                    ->label('Créé par')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('date_resolution')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date_echeance')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('temps_estime')
                    ->suffix('h')
                    ->sortable(),
                Tables\Columns\TextColumn::make('temps_passe')
                    ->suffix('h')
                    ->sortable(),
                Tables\Columns\TextColumn::make('progression')
                    ->suffix('%')
                    ->sortable(),
                Tables\Columns\IconColumn::make('visible_client')
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
            'index' => Pages\ListTickets::route('/'),
            'create' => Pages\CreateTicket::route('/create'),
            'edit' => Pages\EditTicket::route('/{record}/edit'),
        ];
    }
}

<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TodoResource\Pages;
use App\Filament\Resources\TodoResource\RelationManagers;
use App\Models\Todo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TodoResource extends Resource
{
    protected static ?string $model = Todo::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('titre')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('termine')
                    ->required(),
                Forms\Components\TextInput::make('ordre')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->default(0),
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
                Forms\Components\DatePicker::make('date_echeance'),
                Forms\Components\Select::make('client_id')
                    ->label('Client')
                    ->relationship('client', 'nom')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('user_id')
                    ->label('Créateur')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('titre')
                    ->searchable(),
                Tables\Columns\IconColumn::make('termine')
                    ->boolean(),
                Tables\Columns\TextColumn::make('ordre')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('priorite')
                    ->searchable(),
                Tables\Columns\TextColumn::make('date_echeance')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('client.nom')
                    ->label('Client')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Créateur')
                    ->sortable()
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
            'index' => Pages\ListTodos::route('/'),
            'create' => Pages\CreateTodo::route('/create'),
            'edit' => Pages\EditTodo::route('/{record}/edit'),
        ];
    }
}

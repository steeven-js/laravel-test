<?php

declare(strict_types=1);

namespace App\Filament\Resources\Settings;

use App\Models\NumeroSequence;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class NumeroSequenceResource extends Resource
{
    protected static ?string $model = NumeroSequence::class;

    protected static ?string $navigationIcon = 'heroicon-o-hashtag';

    protected static ?string $navigationGroup = 'Réglages';

    protected static ?string $navigationLabel = 'Compteurs devis/factures';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Grid::make(3)->schema([
                Forms\Components\Select::make('type')
                    ->options(['devis' => 'Devis', 'facture' => 'Facture'])
                    ->required(),
                Forms\Components\TextInput::make('year')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(99)
                    ->default((int) now()->format('y'))
                    ->required(),
                Forms\Components\TextInput::make('next_number')
                    ->label('Prochain numéro')
                    ->numeric()
                    ->minValue(1)
                    ->required(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('type')->label('Type')->badge(),
                Tables\Columns\TextColumn::make('year')->label('Année'),
                Tables\Columns\TextColumn::make('next_number')->label('Prochain numéro'),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->since(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageNumeroSequences::route('/'),
        ];
    }
}

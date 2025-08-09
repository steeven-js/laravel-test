<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SecteurActiviteResource\Pages;
use App\Filament\Resources\SecteurActiviteResource\RelationManagers;
use App\Models\SecteurActivite;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SecteurActiviteResource extends Resource
{
    protected static ?string $model = SecteurActivite::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Référentiels';
    protected static ?int $navigationSort = 20;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('code')
                    ->required()
                    ->maxLength(10),
                Forms\Components\TextInput::make('libelle')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('division')
                    ->maxLength(2),
                Forms\Components\TextInput::make('section')
                    ->maxLength(1),
                Forms\Components\Toggle::make('actif')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('libelle')
                    ->searchable(),
                Tables\Columns\TextColumn::make('division')
                    ->searchable(),
                Tables\Columns\TextColumn::make('section')
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
            'index' => Pages\ListSecteurActivites::route('/'),
            'create' => Pages\CreateSecteurActivite::route('/create'),
            'edit' => Pages\EditSecteurActivite::route('/{record}/edit'),
        ];
    }
}

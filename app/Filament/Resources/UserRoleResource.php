<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\UserRoleResource\Pages;
use App\Models\UserRole;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class UserRoleResource extends Resource
{
    protected static ?string $model = UserRole::class;

    protected static ?string $navigationIcon = 'heroicon-o-key';

    protected static ?string $navigationGroup = 'Administration';

    protected static ?int $navigationSort = 10;

    public static function getModelLabel(): string
    {
        return 'Rôle';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Rôles';
    }

    public static function getNavigationLabel(): string
    {
        return 'Rôles';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Rôle')
                    ->description('Identifiants et permissions')
                    ->icon('heroicon-o-key')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('name')->required()->maxLength(255),
                                Forms\Components\TextInput::make('display_name')->required()->maxLength(255),
                            ]),
                        Forms\Components\Textarea::make('description')->columnSpanFull(),
                        Forms\Components\KeyValue::make('permissions')->label('Permissions (JSON)')->reorderable()->keyLabel('Clé')->valueLabel('Valeur')->addButtonLabel('Ajouter une permission')->nullable()->columnSpanFull(),
                        Forms\Components\Toggle::make('is_active')->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(null)
            ->recordAction('view')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('display_name')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->toggleable(),
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
            ->searchPlaceholder('Rechercher...')
            ->emptyStateIcon('heroicon-o-key')
            ->emptyStateHeading('Aucun rôle')
            ->emptyStateDescription('Ajoutez votre premier rôle pour commencer.')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()->label('Nouveau rôle'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->infolist([
                        Infolists\Components\Section::make('Rôle')
                            ->description('Identifiants et permissions')
                            ->icon('heroicon-o-key')
                            ->schema([
                                Infolists\Components\TextEntry::make('name')
                                    ->label('Nom'),
                                Infolists\Components\TextEntry::make('display_name')
                                    ->label('Nom d\'affichage'),
                                Infolists\Components\TextEntry::make('description')
                                    ->label('Description')
                                    ->markdown(),
                                Infolists\Components\TextEntry::make('permissions')
                                    ->label('Permissions')
                                    ->json(),
                                Infolists\Components\IconEntry::make('is_active')
                                    ->label('Actif')
                                    ->boolean(),
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
            'index' => Pages\ListUserRoles::route('/'),
            'create' => Pages\CreateUserRole::route('/create'),
            'edit' => Pages\EditUserRole::route('/{record}/edit'),
        ];
    }
}

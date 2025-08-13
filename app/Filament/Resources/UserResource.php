<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Parfaitementweb\FilamentCountryField\Forms\Components\Country;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Ysfkaya\FilamentPhoneInput\Tables\PhoneColumn;

class UserResource extends Resource
{
    use \App\Filament\Resources\Traits\HasHistoriqueResource;

    protected static ?string $modelLabel = 'Utilisateur';

    protected static ?string $pluralModelLabel = 'Utilisateurs';

    protected static ?string $navigationLabel = 'Utilisateurs';

    protected static ?string $pluralNavigationLabel = 'Utilisateurs';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    protected static ?string $navigationGroup = 'Administration';

    protected static ?int $navigationSort = 20;

    public static function getModelLabel(): string
    {
        return 'Utilisateur';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Utilisateurs';
    }

    public static function getNavigationLabel(): string
    {
        return 'Utilisateurs';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Profil utilisateur')
                    ->description('Informations, coordonnées et rôle de l’utilisateur')
                    ->icon('heroicon-o-user')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nom')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('email')
                                    ->label('Email')
                                    ->email()
                                    ->required()
                                    ->maxLength(255),
                                PhoneInput::make('telephone')
                                    ->label('Téléphone')
                                    ->defaultCountry('FR')
                                    ->formatAsYouType(true)
                                    ->displayNumberFormat(\Ysfkaya\FilamentPhoneInput\PhoneInputNumberType::NATIONAL)
                                    ->inputNumberFormat(\Ysfkaya\FilamentPhoneInput\PhoneInputNumberType::E164)
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('ville')
                                    ->label('Ville')
                                    ->maxLength(255),
                            ]),
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('adresse')
                                    ->label('Adresse')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('code_postal')
                                    ->label('Code postal')
                                    ->maxLength(255),
                                Country::make('pays')
                                    ->label('Pays')
                                    ->default('FR'),
                            ]),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('avatar')
                                    ->label('Avatar')
                                    ->maxLength(255),
                                Forms\Components\DateTimePicker::make('email_verified_at')->label('Email vérifié le'),
                            ]),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('password')
                                    ->label('Mot de passe')
                                    ->password()
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\Select::make('user_role_id')
                                    ->label('Rôle')
                                    ->relationship('userRole', 'display_name'),
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
                Tables\Columns\TextColumn::make('name')
                    ->label('Nom')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(),
                PhoneColumn::make('telephone')
                    ->label('Téléphone')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('ville')
                    ->label('Ville')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('adresse')
                    ->label('Adresse')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('code_postal')
                    ->label('Code postal')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('pays')
                    ->label('Pays')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('avatar')
                    ->label('Avatar')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('email_verified_at')
                    ->label('Email vérifié le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('userRole.display_name')
                    ->label('Rôle')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Mis à jour le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->searchPlaceholder('Rechercher...')
            ->emptyStateIcon('heroicon-o-user-circle')
            ->emptyStateHeading('Aucun utilisateur')
            ->emptyStateDescription('Ajoutez votre premier utilisateur pour commencer.')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()->label('Nouvel utilisateur'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->label('Aperçu')
                        ->modalHeading('Aperçu de l\'utilisateur')
                        ->modalSubmitAction(false)
                        ->modalCancelActionLabel('Fermer')
                        ->infolist([
                            Infolists\Components\Section::make('Informations')
                                ->schema([
                                    Infolists\Components\Grid::make(2)
                                        ->schema([
                                            Infolists\Components\TextEntry::make('name')->label('Nom'),
                                            Infolists\Components\TextEntry::make('email')->label('Email'),
                                            Infolists\Components\TextEntry::make('telephone')->label('Téléphone')->placeholder('—'),
                                            Infolists\Components\TextEntry::make('ville')->label('Ville')->placeholder('—'),
                                            Infolists\Components\TextEntry::make('adresse')->label('Adresse')->placeholder('—'),
                                            Infolists\Components\TextEntry::make('code_postal')->label('Code postal')->placeholder('—'),
                                            Infolists\Components\TextEntry::make('pays')->label('Pays')->placeholder('—'),
                                            Infolists\Components\TextEntry::make('userRole.display_name')->label('Rôle'),
                                            Infolists\Components\TextEntry::make('email_verified_at')->label('Email vérifié le')->dateTime('d/m/Y H:i')->placeholder('Non vérifié'),
                                            Infolists\Components\TextEntry::make('created_at')->label('Créé le')->dateTime('d/m/Y H:i'),
                                            Infolists\Components\TextEntry::make('updated_at')->label('Mis à jour le')->dateTime('d/m/Y H:i'),
                                        ]),
                                ]),
                        ]),
                    Tables\Actions\EditAction::make()->label('Modifier'),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('Supprimer la sélection'),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    protected static function getDefaultRelations(): array
    {
        return [
        ];
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}

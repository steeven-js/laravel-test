<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\UserRoleResource\Pages;
use App\Models\User;
use App\Models\UserRole;
use App\Services\PermissionService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class UserRoleResource extends Resource
{
    use \App\Filament\Resources\Traits\HasHistoriqueResource;

    protected static ?string $model = UserRole::class;

    protected static ?string $navigationIcon = 'heroicon-o-key';

    protected static ?string $navigationGroup = 'Administration';

    protected static ?string $navigationLabel = 'Rôles et permissions';

    public static function getPluralModelLabel(): string
    {
        return 'Rôles et permissions';
    }

    public static function canViewAny(): bool
    {
        $user = Auth::user();

        return $user && ($user instanceof User) && ($user->canManageRoles() || $user->isSuperAdmin());
    }

    public static function canCreate(): bool
    {
        $user = Auth::user();

        return $user && ($user instanceof User) && ($user->canManageRoles() || $user->isSuperAdmin());
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations du rôle')
                    ->description('Identifiants et description du rôle')
                    ->icon('heroicon-o-key')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nom technique')
                                    ->required()
                                    ->maxLength(255)
                                    ->helperText('Nom unique utilisé dans le code (ex: super_admin)')
                                    ->disabled(fn ($record) => $record && $record->name === 'super_admin'),
                                Forms\Components\TextInput::make('display_name')
                                    ->label("Nom d'affichage")
                                    ->required()
                                    ->maxLength(255)
                                    ->helperText('Nom affiché dans l\'interface (ex: Super administrateur)'),
                            ]),
                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->rows(3)
                            ->helperText('Description détaillée du rôle et de ses responsabilités')
                            ->columnSpanFull(),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Rôle actif')
                            ->helperText('Désactiver pour empêcher l\'attribution de ce rôle')
                            ->required(),
                    ]),

                Forms\Components\Section::make('Permissions')
                    ->description('Configurez les permissions pour ce rôle')
                    ->icon('heroicon-o-shield-check')
                    ->schema([
                        \App\Filament\Forms\Components\PermissionManager::make('permissions')
                            ->label('')
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nom technique')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'super_admin' => 'danger',
                        'admin' => 'warning',
                        'manager' => 'info',
                        'commercial' => 'success',
                        'support' => 'primary',
                        'viewer' => 'gray',
                        default => 'secondary',
                    }),
                Tables\Columns\TextColumn::make('display_name')
                    ->label("Nom d'affichage")
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Description')
                    ->limit(50)
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Actif')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('users_count')
                    ->label('Utilisateurs')
                    ->counts('users')
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Modifié le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Rôle actif')
                    ->boolean(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Nouveau rôle')
                    ->icon('heroicon-o-plus'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->label('Aperçu')
                        ->modal()
                        ->url(null)
                        ->modalCancelActionLabel('Fermer')
                        ->icon('heroicon-o-eye')
                        ->infolist([
                            Infolists\Components\Section::make('Informations du rôle')
                                ->description('Détails du rôle')
                                ->icon('heroicon-o-key')
                                ->schema([
                                    Infolists\Components\Grid::make(2)
                                        ->schema([
                                            Infolists\Components\TextEntry::make('name')
                                                ->label('Nom technique')
                                                ->badge()
                                                ->color(fn (string $state): string => match ($state) {
                                                    'super_admin' => 'danger',
                                                    'admin' => 'warning',
                                                    'manager' => 'info',
                                                    'commercial' => 'success',
                                                    'support' => 'primary',
                                                    'viewer' => 'gray',
                                                    default => 'secondary',
                                                }),
                                            Infolists\Components\TextEntry::make('display_name')
                                                ->label("Nom d'affichage"),
                                            Infolists\Components\IconEntry::make('is_active')
                                                ->label('Actif')
                                                ->boolean(),
                                        ]),
                                    Infolists\Components\TextEntry::make('description')
                                        ->label('Description')
                                        ->markdown()
                                        ->columnSpanFull(),
                                ]),
                            Infolists\Components\Section::make('Permissions')
                                ->description('Permissions accordées à ce rôle')
                                ->icon('heroicon-o-shield-check')
                                ->schema([
                                    Infolists\Components\TextEntry::make('permissions')
                                        ->label('')
                                        ->formatStateUsing(function ($state) {
                                            if (! $state) {
                                                return 'Aucune permission';
                                            }

                                            $permissions = PermissionService::getAllAvailablePermissions();
                                            $output = [];

                                            foreach ($state as $resource => $actions) {
                                                if (isset($permissions[$resource])) {
                                                    $resourcePermissions = [];
                                                    foreach ($actions as $action) {
                                                        if (isset($permissions[$resource][$action])) {
                                                            $resourcePermissions[] = $permissions[$resource][$action];
                                                        }
                                                    }
                                                    if (! empty($resourcePermissions)) {
                                                        $output[] = '**' . ucfirst($resource) . '** : ' . implode(', ', $resourcePermissions);
                                                    }
                                                }
                                            }

                                            return empty($output) ? 'Aucune permission' : implode("\n", $output);
                                        })
                                        ->markdown()
                                        ->columnSpanFull(),
                                ]),
                            Infolists\Components\Section::make('Utilisateurs')
                                ->description('Utilisateurs ayant ce rôle')
                                ->icon('heroicon-o-users')
                                ->schema([
                                    Infolists\Components\TextEntry::make('users_count')
                                        ->label('Nombre d\'utilisateurs')
                                        ->formatStateUsing(fn ($state) => $state ?? 0),
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
                    Tables\Actions\EditAction::make()
                        ->label('Modifier')
                        ->icon('heroicon-o-pencil'),
                    Tables\Actions\DeleteAction::make()
                        ->label('Supprimer')
                        ->icon('heroicon-o-trash')
                        ->visible(fn (UserRole $record) => $record->name !== 'super_admin'),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Supprimer les rôles sélectionnés')
                        ->deselectRecordsAfterCompletion(),
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
            'index' => Pages\ListUserRoles::route('/'),
            'create' => Pages\CreateUserRole::route('/create'),
            'edit' => Pages\EditUserRole::route('/{record}/edit'),
            'manage-permissions' => Pages\ManagePermissions::route('/manage-permissions'),
        ];
    }
}

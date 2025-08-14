<?php

declare(strict_types=1);

namespace App\Filament\Resources\UserRoleResource\Pages;

use App\Filament\Resources\UserRoleResource;
use App\Models\UserRole;
use App\Services\PermissionService;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;

class ManagePermissions extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static string $resource = UserRoleResource::class;

    protected static string $view = 'filament.resources.user-role-resource.pages.manage-permissions';

    public static function getNavigationLabel(): string
    {
        return 'Gérer les permissions';
    }

    public function getTitle(): string
    {
        return 'Gestion des permissions';
    }

    public static function getSlug(): string
    {
        return 'manage-permissions';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(UserRole::query())
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
                Tables\Columns\TextColumn::make('permissions_summary')
                    ->label('Permissions')
                    ->formatStateUsing(function (UserRole $record) {
                        $permissions = $record->permissions ?? [];
                        $totalPermissions = 0;

                        foreach ($permissions as $resource => $actions) {
                            if (is_array($actions)) {
                                $totalPermissions += count($actions);
                            }
                        }

                        return $totalPermissions . ' permission(s)';
                    }),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Actif')
                    ->boolean()
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('edit_permissions')
                    ->label('Modifier les permissions')
                    ->icon('heroicon-o-pencil')
                    ->form([
                        Forms\Components\Section::make('Permissions pour ' . fn (UserRole $record) => $record->display_name)
                            ->description('Configurez les permissions pour ce rôle')
                            ->schema([
                                \App\Filament\Forms\Components\PermissionManager::make('permissions')
                                    ->label('')
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->action(function (array $data, UserRole $record) {
                        $record->update([
                            'permissions' => PermissionService::formatPermissionsForDatabase($data['permissions'] ?? []),
                        ]);

                        Notification::make()
                            ->title('Permissions mises à jour')
                            ->body('Les permissions du rôle ' . $record->display_name . ' ont été mises à jour.')
                            ->success()
                            ->send();
                    })
                    ->modalWidth('7xl'),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('copy_permissions')
                    ->label('Copier les permissions')
                    ->icon('heroicon-o-clipboard-document')
                    ->form([
                        Forms\Components\Select::make('source_role_id')
                            ->label('Rôle source')
                            ->options(UserRole::pluck('display_name', 'id'))
                            ->required()
                            ->searchable(),
                    ])
                    ->action(function (array $data, $records) {
                        $sourceRole = UserRole::find($data['source_role_id']);

                        if (! $sourceRole) {
                            Notification::make()
                                ->title('Erreur')
                                ->body('Rôle source non trouvé.')
                                ->danger()
                                ->send();

                            return;
                        }

                        $updatedCount = 0;
                        foreach ($records as $record) {
                            if ($record->name !== 'super_admin') {
                                $record->update([
                                    'permissions' => $sourceRole->permissions,
                                ]);
                                $updatedCount++;
                            }
                        }

                        Notification::make()
                            ->title('Permissions copiées')
                            ->body("Les permissions du rôle '{$sourceRole->display_name}' ont été copiées vers {$updatedCount} rôle(s).")
                            ->success()
                            ->send();
                    }),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('reset_all_permissions')
                ->label('Réinitialiser toutes les permissions')
                ->icon('heroicon-o-arrow-path')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Réinitialiser toutes les permissions')
                ->modalDescription('Cette action va supprimer toutes les permissions personnalisées et restaurer les permissions par défaut. Cette action ne peut pas être annulée.')
                ->modalSubmitActionLabel('Oui, réinitialiser')
                ->action(function () {
                    // Réinitialiser les permissions selon les rôles par défaut
                    $defaultPermissions = [
                        'super_admin' => [
                            'users' => ['view', 'create', 'edit', 'delete', 'manage_roles'],
                            'clients' => ['view', 'create', 'edit', 'delete', 'export'],
                            'devis' => ['view', 'create', 'edit', 'delete', 'send', 'export', 'transform_to_facture'],
                            'factures' => ['view', 'create', 'edit', 'delete', 'send', 'export'],
                            'opportunities' => ['view', 'create', 'edit', 'delete', 'export'],
                            'tickets' => ['view', 'create', 'edit', 'delete', 'assign', 'export'],
                            'todos' => ['view', 'create', 'edit', 'delete', 'assign', 'export'],
                            'services' => ['view', 'create', 'edit', 'delete', 'import_csv'],
                            'entreprises' => ['view', 'create', 'edit', 'delete'],
                            'secteurs_activite' => ['view', 'create', 'edit', 'delete'],
                            'settings' => ['view', 'edit'],
                            'historique' => ['view', 'export'],
                            'dashboard' => ['view_all_stats'],
                            'generation' => ['generate_test_data'],
                        ],
                        'admin' => [
                            'users' => ['view', 'create', 'edit'],
                            'clients' => ['view', 'create', 'edit', 'delete', 'export'],
                            'devis' => ['view', 'create', 'edit', 'delete', 'send', 'export', 'transform_to_facture'],
                            'factures' => ['view', 'create', 'edit', 'delete', 'send', 'export'],
                            'opportunities' => ['view', 'create', 'edit', 'delete', 'export'],
                            'tickets' => ['view', 'create', 'edit', 'delete', 'assign', 'export'],
                            'todos' => ['view', 'create', 'edit', 'delete', 'assign', 'export'],
                            'services' => ['view', 'create', 'edit', 'delete'],
                            'entreprises' => ['view', 'create', 'edit', 'delete'],
                            'secteurs_activite' => ['view', 'create', 'edit', 'delete'],
                            'settings' => ['view'],
                            'historique' => ['view'],
                            'dashboard' => ['view_all_stats'],
                        ],
                        'manager' => [
                            'clients' => ['view', 'create', 'edit', 'export'],
                            'devis' => ['view', 'create', 'edit', 'send', 'export', 'transform_to_facture'],
                            'factures' => ['view', 'create', 'edit', 'send', 'export'],
                            'opportunities' => ['view', 'create', 'edit', 'export'],
                            'tickets' => ['view', 'create', 'edit', 'assign'],
                            'todos' => ['view', 'create', 'edit', 'assign'],
                            'services' => ['view'],
                            'entreprises' => ['view', 'create', 'edit'],
                            'secteurs_activite' => ['view'],
                            'historique' => ['view'],
                            'dashboard' => ['view_own_stats'],
                        ],
                        'commercial' => [
                            'clients' => ['view', 'create', 'edit'],
                            'devis' => ['view', 'create', 'edit', 'send'],
                            'opportunities' => ['view', 'create', 'edit'],
                            'tickets' => ['view', 'create'],
                            'todos' => ['view', 'create'],
                            'services' => ['view'],
                            'entreprises' => ['view'],
                            'secteurs_activite' => ['view'],
                            'dashboard' => ['view_own_stats'],
                        ],
                        'support' => [
                            'clients' => ['view'],
                            'tickets' => ['view', 'create', 'edit', 'assign'],
                            'todos' => ['view', 'create', 'edit', 'assign'],
                            'services' => ['view'],
                            'dashboard' => ['view_own_stats'],
                        ],
                        'viewer' => [
                            'clients' => ['view'],
                            'devis' => ['view'],
                            'factures' => ['view'],
                            'opportunities' => ['view'],
                            'tickets' => ['view'],
                            'todos' => ['view'],
                            'services' => ['view'],
                            'entreprises' => ['view'],
                            'secteurs_activite' => ['view'],
                            'dashboard' => ['view_own_stats'],
                        ],
                    ];

                    foreach ($defaultPermissions as $roleName => $permissions) {
                        $role = UserRole::where('name', $roleName)->first();
                        if ($role) {
                            $role->update(['permissions' => $permissions]);
                        }
                    }

                    Notification::make()
                        ->title('Permissions réinitialisées')
                        ->body('Toutes les permissions ont été réinitialisées aux valeurs par défaut.')
                        ->success()
                        ->send();
                }),
        ];
    }
}

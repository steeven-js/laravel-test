<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Models\User;
use App\Models\UserRole;
use App\Services\PermissionService;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class PermissionsOverview extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static string $view = 'filament.pages.permissions-overview';

    protected static ?string $navigationLabel = 'Vue d\'ensemble des permissions';

    protected static ?string $title = 'Vue d\'ensemble des permissions';

    protected static ?string $navigationGroup = 'Administration';

    protected static ?int $navigationSort = 30;

    public static function canAccess(): bool
    {
        $user = Auth::user();

        return $user && ($user instanceof User) && $user->isSuperAdmin();
    }

    public function mount(): void
    {
        abort_unless(static::canAccess(), 403);
    }

    public function getViewData(): array
    {
        $permissionService = new PermissionService;
        $allPermissions = $permissionService->getAllAvailablePermissions();

        // Récupérer tous les utilisateurs avec leurs rôles
        $users = User::with('userRole')->get();

        // Récupérer tous les rôles
        $roles = UserRole::all();

        // Organiser les permissions par ressource
        $permissionsByResource = $permissionService->getPermissionsByResource();

        return [
            'users' => $users,
            'roles' => $roles,
            'allPermissions' => $allPermissions,
            'permissionsByResource' => $permissionsByResource,
        ];
    }
}

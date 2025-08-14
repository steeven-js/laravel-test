<?php

declare(strict_types=1);

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use \App\Models\Traits\HasHistorique, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'telephone',
        'ville',
        'adresse',
        'code_postal',
        'pays',
        'avatar',
        'user_role_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function userRole(): BelongsTo
    {
        return $this->belongsTo(UserRole::class);
    }

    public function isSuperAdmin(): bool
    {
        return $this->userRole?->name === 'super_admin';
    }

    /**
     * Vérifier si l'utilisateur a une permission spécifique
     */
    public function hasPermission(string $resource, string $action): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        $permissions = $this->userRole?->permissions ?? [];

        return in_array($action, $permissions[$resource] ?? []);
    }

    /**
     * Vérifier si l'utilisateur peut voir une ressource
     */
    public function canView(string $resource): bool
    {
        return $this->hasPermission($resource, 'view');
    }

    /**
     * Vérifier si l'utilisateur peut créer une ressource
     */
    public function canCreate(string $resource): bool
    {
        return $this->hasPermission($resource, 'create');
    }

    /**
     * Vérifier si l'utilisateur peut modifier une ressource
     */
    public function canEdit(string $resource): bool
    {
        return $this->hasPermission($resource, 'edit');
    }

    /**
     * Vérifier si l'utilisateur peut supprimer une ressource
     */
    public function canDelete(string $resource): bool
    {
        return $this->hasPermission($resource, 'delete');
    }

    /**
     * Vérifier si l'utilisateur peut exporter une ressource
     */
    public function canExport(string $resource): bool
    {
        return $this->hasPermission($resource, 'export');
    }

    /**
     * Vérifier si l'utilisateur peut envoyer des devis/factures
     */
    public function canSend(string $resource): bool
    {
        return $this->hasPermission($resource, 'send');
    }

    /**
     * Vérifier si l'utilisateur peut transformer un devis en facture
     */
    public function canTransformDevisToFacture(): bool
    {
        return $this->hasPermission('devis', 'transform_to_facture');
    }

    /**
     * Vérifier si l'utilisateur peut assigner des tickets/tâches
     */
    public function canAssign(string $resource): bool
    {
        return $this->hasPermission($resource, 'assign');
    }

    /**
     * Vérifier si l'utilisateur peut gérer les rôles
     */
    public function canManageRoles(): bool
    {
        return $this->hasPermission('users', 'manage_roles');
    }

    /**
     * Vérifier si l'utilisateur peut générer des données de test
     */
    public function canGenerateTestData(): bool
    {
        return $this->hasPermission('generation', 'generate_test_data');
    }

    /**
     * Vérifier si l'utilisateur peut voir toutes les statistiques
     */
    public function canViewAllStats(): bool
    {
        return $this->hasPermission('dashboard', 'view_all_stats');
    }

    /**
     * Obtenir toutes les permissions de l'utilisateur
     */
    public function getAllPermissions(): array
    {
        if ($this->isSuperAdmin()) {
            return ['*' => ['*']];
        }

        return $this->userRole?->permissions ?? [];
    }

    /**
     * Obtenir le nom d'affichage du rôle
     */
    public function getRoleDisplayName(): string
    {
        return $this->userRole?->display_name ?? 'Aucun rôle';
    }
}

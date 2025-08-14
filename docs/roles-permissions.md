# Rôles & Permissions – Guide Complet

## Vue d'ensemble

Le Dashboard Madinia utilise un système de permissions basé sur les rôles (RBAC - Role-Based Access Control) pour gérer les accès aux différentes fonctionnalités. Chaque utilisateur se voit attribuer un rôle qui détermine ses permissions dans l'application.

## Architecture technique

### Modèle de données

- **Table**: `user_roles`
- **Champ permissions**: JSON stocké en base de données
- **Relation**: `User` → `UserRole` (one-to-many)

### Format des données

#### En base de données (JSON)
```json
{
  "clients": ["view", "create", "edit", "delete", "export"],
  "devis": ["view", "create", "edit", "delete", "send", "export", "transform_to_facture"],
  "factures": ["view", "create", "edit", "delete", "send", "export"]
}
```

#### Dans le formulaire Filament (état imbriqué)
```json
{
  "clients": {
    "view": true,
    "create": true,
    "edit": true,
    "delete": true,
    "export": true
  },
  "devis": {
    "view": true,
    "create": true,
    "edit": true,
    "delete": true,
    "send": true,
    "export": true,
    "transform_to_facture": true
  }
}
```

## Ressources et actions disponibles

### Clients
- `view` - Voir les clients
- `create` - Créer des clients
- `edit` - Modifier les clients
- `delete` - Supprimer les clients
- `export` - Exporter les clients

### Devis
- `view` - Voir les devis
- `create` - Créer des devis
- `edit` - Modifier les devis
- `delete` - Supprimer les devis
- `send` - Envoyer les devis
- `export` - Exporter les devis
- `transform_to_facture` - Transformer en facture

### Factures
- `view` - Voir les factures
- `create` - Créer des factures
- `edit` - Modifier les factures
- `delete` - Supprimer les factures
- `send` - Envoyer les factures
- `export` - Exporter les factures

### Opportunités
- `view` - Voir les opportunités
- `create` - Créer des opportunités
- `edit` - Modifier les opportunités
- `delete` - Supprimer les opportunités
- `export` - Exporter les opportunités

### Tickets
- `view` - Voir les tickets
- `create` - Créer des tickets
- `edit` - Modifier les tickets
- `delete` - Supprimer les tickets
- `assign` - Assigner les tickets
- `export` - Exporter les tickets

### Tâches
- `view` - Voir les tâches
- `create` - Créer des tâches
- `edit` - Modifier les tâches
- `delete` - Supprimer les tâches
- `assign` - Assigner les tâches
- `export` - Exporter les tâches

### Utilisateurs
- `view` - Voir les utilisateurs
- `create` - Créer des utilisateurs
- `edit` - Modifier les utilisateurs
- `delete` - Supprimer les utilisateurs
- `manage_roles` - Gérer les rôles

### Services
- `view` - Voir les services
- `create` - Créer des services
- `edit` - Modifier les services
- `delete` - Supprimer les services
- `import_csv` - Importer depuis CSV

### Entreprises
- `view` - Voir les entreprises
- `create` - Créer des entreprises
- `edit` - Modifier les entreprises
- `delete` - Supprimer les entreprises

### Secteurs d'activité
- `view` - Voir les secteurs
- `create` - Créer des secteurs
- `edit` - Modifier les secteurs
- `delete` - Supprimer les secteurs

### Paramètres
- `view` - Voir les paramètres
- `edit` - Modifier les paramètres

### Historique
- `view` - Voir l'historique
- `export` - Exporter l'historique

### Tableau de bord
- `view_all_stats` - Voir toutes les statistiques
- `view_own_stats` - Voir ses propres statistiques

### Génération de données
- `generate_test_data` - Générer des données de test

## Rôles par défaut

### Super Administrateur (`super_admin`)
**Accès complet à toutes les fonctionnalités**

```json
{
  "users": ["view", "create", "edit", "delete", "manage_roles"],
  "clients": ["view", "create", "edit", "delete", "export"],
  "devis": ["view", "create", "edit", "delete", "send", "export", "transform_to_facture"],
  "factures": ["view", "create", "edit", "delete", "send", "export"],
  "opportunities": ["view", "create", "edit", "delete", "export"],
  "tickets": ["view", "create", "edit", "delete", "assign", "export"],
  "todos": ["view", "create", "edit", "delete", "assign", "export"],
  "services": ["view", "create", "edit", "delete", "import_csv"],
  "entreprises": ["view", "create", "edit", "delete"],
  "secteurs_activite": ["view", "create", "edit", "delete"],
  "settings": ["view", "edit"],
  "historique": ["view", "export"],
  "dashboard": ["view_all_stats"],
  "generation": ["generate_test_data"]
}
```

### Administrateur (`admin`)
**Gestion des utilisateurs et données principales**

```json
{
  "users": ["view", "create", "edit"],
  "clients": ["view", "create", "edit", "delete", "export"],
  "devis": ["view", "create", "edit", "delete", "send", "export", "transform_to_facture"],
  "factures": ["view", "create", "edit", "delete", "send", "export"],
  "opportunities": ["view", "create", "edit", "delete", "export"],
  "tickets": ["view", "create", "edit", "delete", "assign", "export"],
  "todos": ["view", "create", "edit", "delete", "assign", "export"],
  "services": ["view", "create", "edit", "delete"],
  "entreprises": ["view", "create", "edit", "delete"],
  "secteurs_activite": ["view", "create", "edit", "delete"],
  "settings": ["view"],
  "historique": ["view"],
  "dashboard": ["view_all_stats"]
}
```

### Manager (`manager`)
**Gestion des clients, devis et factures**

```json
{
  "clients": ["view", "create", "edit", "export"],
  "devis": ["view", "create", "edit", "send", "export", "transform_to_facture"],
  "factures": ["view", "create", "edit", "send", "export"],
  "opportunities": ["view", "create", "edit", "export"],
  "tickets": ["view", "create", "edit", "assign"],
  "todos": ["view", "create", "edit", "assign"],
  "services": ["view"],
  "entreprises": ["view", "create", "edit"],
  "secteurs_activite": ["view"],
  "historique": ["view"],
  "dashboard": ["view_own_stats"]
}
```

### Commercial (`commercial`)
**Gestion commerciale et relation client**

```json
{
  "clients": ["view", "create", "edit"],
  "devis": ["view", "create", "edit", "send"],
  "opportunities": ["view", "create", "edit"],
  "tickets": ["view", "create"],
  "todos": ["view", "create"],
  "services": ["view"],
  "entreprises": ["view"],
  "secteurs_activite": ["view"],
  "dashboard": ["view_own_stats"]
}
```

### Support (`support`)
**Gestion du support client**

```json
{
  "clients": ["view"],
  "tickets": ["view", "create", "edit", "assign"],
  "todos": ["view", "create", "edit", "assign"],
  "services": ["view"],
  "dashboard": ["view_own_stats"]
}
```

### Lecteur (`viewer`)
**Accès en lecture seule**

```json
{
  "clients": ["view"],
  "devis": ["view"],
  "factures": ["view"],
  "opportunities": ["view"],
  "tickets": ["view"],
  "todos": ["view"],
  "services": ["view"],
  "entreprises": ["view"],
  "secteurs_activite": ["view"],
  "dashboard": ["view_own_stats"]
}
```

## Interface d'administration

### Accès à la gestion des rôles
- **URL**: `/admin/user-roles`
- **Accès**: `Auth::user()->canManageRoles() || Auth::user()->isSuperAdmin()`

### Édition d'un rôle
- **URL**: `/admin/user-roles/{id}/edit`
- **Fonctionnalités**:
  - Modification des informations du rôle (nom, description, statut)
  - Configuration des permissions via cases à cocher
  - Navigation entre les rôles (boutons Précédent/Suivant)
  - Titre dynamique affichant le nom du rôle

### Page de gestion des permissions
- **URL**: `/admin/user-roles/manage-permissions`
- **Fonctionnalités**:
  - Vue d'ensemble de tous les rôles
  - Modification rapide des permissions par rôle
  - Copie de permissions entre rôles
  - Réinitialisation des permissions par défaut

## Services utilitaires

### PermissionService

#### `formatPermissionsForDatabase(array $formState): array`
Transforme l'état du formulaire Filament vers le format JSON de la base de données.

```php
// Entrée (état formulaire)
[
  'clients' => ['view' => true, 'create' => false, 'edit' => true]
]

// Sortie (format BD)
[
  'clients' => ['view', 'edit']
]
```

#### `formatPermissionsForForm(array $databasePermissions): array`
Transforme le JSON de la base de données vers l'état attendu par Filament.

```php
// Entrée (format BD)
[
  'clients' => ['view', 'edit']
]

// Sortie (état formulaire)
[
  'clients' => ['view' => true, 'edit' => true]
]
```

#### `getAllAvailablePermissions(): array`
Retourne la liste complète des permissions avec leurs libellés.

#### `getPermissionsByResource(): array`
Retourne les permissions groupées par ressource.

#### `permissionExists(string $resource, string $action): bool`
Vérifie si une permission existe.

#### `getPermissionLabel(string $resource, string $action): ?string`
Retourne le libellé d'une permission.

## Intégration dans l'application

### Vérification des permissions
```php
// Dans un contrôleur ou middleware
if (Auth::user()->can('clients.view')) {
    // Accès autorisé
}

// Vérification multiple
if (Auth::user()->can(['clients.view', 'clients.edit'])) {
    // Accès autorisé
}
```

### Middleware de permissions
```php
// Dans routes/web.php
Route::middleware(['auth', 'permission:clients.view'])->group(function () {
    Route::get('/clients', [ClientController::class, 'index']);
});
```

### Dans les vues Blade
```php
@can('clients.create')
    <a href="{{ route('clients.create') }}" class="btn btn-primary">
        Nouveau client
    </a>
@endcan
```

## Bonnes pratiques

### Sécurité
1. **Toujours vérifier les permissions** avant d'accorder l'accès à une ressource
2. **Utiliser le middleware** de permissions pour les routes sensibles
3. **Ne jamais faire confiance** aux données côté client

### Maintenance
1. **Éditer les permissions via l'interface** pour éviter les incohérences
2. **Conserver le rôle super_admin** avec tous les accès
3. **Documenter les nouvelles permissions** lors de l'ajout de fonctionnalités

### Évolution du système
1. **Ajouter les nouvelles actions** dans `PermissionService::getAllAvailablePermissions()`
2. **Mettre à jour le formulaire** d'édition des rôles
3. **Ajuster les rôles par défaut** si nécessaire
4. **Tester les permissions** après modification

## Dépannage

### Problèmes courants

#### Les permissions ne s'affichent pas dans le formulaire
- Vérifier que `PermissionService::formatPermissionsForForm()` fonctionne correctement
- S'assurer que le composant `PermissionManager` est bien configuré

#### Les permissions ne sont pas sauvegardées
- Vérifier que `PermissionService::formatPermissionsForDatabase()` fonctionne
- Contrôler que le champ `permissions` est dans le `$fillable` du modèle `UserRole`

#### Erreur d'accès refusé
- Vérifier que l'utilisateur a le bon rôle
- Contrôler que les permissions sont bien définies pour le rôle
- S'assurer que la vérification de permissions est en place

### Commandes utiles
```bash
# Réinitialiser les permissions par défaut
php artisan user-roles:reset-permissions

# Vérifier les permissions d'un utilisateur
php artisan user:check-permissions {user_id}

# Lister tous les rôles et leurs permissions
php artisan user-roles:list
```

## Évolutions futures

### Fonctionnalités prévues
- [ ] Permissions granulaires par enregistrement
- [ ] Permissions temporaires avec expiration
- [ ] Audit des modifications de permissions
- [ ] Import/export des configurations de rôles
- [ ] Interface de gestion des permissions par équipe

### Améliorations techniques
- [ ] Cache des permissions pour améliorer les performances
- [ ] API REST pour la gestion des permissions
- [ ] Intégration avec des systèmes d'authentification externes
- [ ] Support des permissions conditionnelles

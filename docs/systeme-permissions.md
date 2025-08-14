# Système de Permissions et Rôles

Ce document décrit le système de permissions et rôles implémenté dans l'application Laravel 12 avec Filament.

## 🎯 Vue d'ensemble

Le système de permissions est basé sur des **rôles utilisateur** avec des **permissions granulaires** stockées en JSON dans la base de données. Chaque utilisateur a un rôle qui définit ses droits d'accès aux différentes fonctionnalités.

## 👥 Rôles disponibles

### 1. Super administrateur (`super_admin`)
- **Accès complet** à toutes les fonctionnalités
- **Gestion des utilisateurs** et des rôles
- **Génération de données de test**
- **Toutes les statistiques**

### 2. Administrateur (`admin`)
- **Gestion des utilisateurs** (sans gestion des rôles)
- **Accès complet** aux données principales
- **Statistiques complètes**
- **Pas de génération de données de test**

### 3. Manager (`manager`)
- **Gestion des clients, devis, factures**
- **Gestion des opportunités**
- **Assignation de tickets et tâches**
- **Statistiques personnelles**

### 4. Commercial (`commercial`)
- **Gestion des clients et devis**
- **Création d'opportunités**
- **Création de tickets et tâches**
- **Statistiques personnelles**

### 5. Support (`support`)
- **Consultation des clients**
- **Gestion des tickets et tâches**
- **Assignation de tickets**
- **Statistiques personnelles**

### 6. Lecteur (`viewer`)
- **Accès en lecture seule**
- **Consultation de toutes les données**
- **Statistiques personnelles**

## 🔐 Permissions par ressource

### Clients (`clients`)
- `view` : Voir les clients
- `create` : Créer un client
- `edit` : Modifier un client
- `delete` : Supprimer un client
- `export` : Exporter les données

### Devis (`devis`)
- `view` : Voir les devis
- `create` : Créer un devis
- `edit` : Modifier un devis
- `delete` : Supprimer un devis
- `send` : Envoyer un devis
- `export` : Exporter les devis
- `transform_to_facture` : Transformer en facture

### Factures (`factures`)
- `view` : Voir les factures
- `create` : Créer une facture
- `edit` : Modifier une facture
- `delete` : Supprimer une facture
- `send` : Envoyer une facture
- `export` : Exporter les factures

### Opportunités (`opportunities`)
- `view` : Voir les opportunités
- `create` : Créer une opportunité
- `edit` : Modifier une opportunité
- `delete` : Supprimer une opportunité
- `export` : Exporter les opportunités

### Tickets (`tickets`)
- `view` : Voir les tickets
- `create` : Créer un ticket
- `edit` : Modifier un ticket
- `delete` : Supprimer un ticket
- `assign` : Assigner un ticket
- `export` : Exporter les tickets

### Tâches (`todos`)
- `view` : Voir les tâches
- `create` : Créer une tâche
- `edit` : Modifier une tâche
- `delete` : Supprimer une tâche
- `assign` : Assigner une tâche
- `export` : Exporter les tâches

### Utilisateurs (`users`)
- `view` : Voir les utilisateurs
- `create` : Créer un utilisateur
- `edit` : Modifier un utilisateur
- `delete` : Supprimer un utilisateur
- `manage_roles` : Gérer les rôles

### Services (`services`)
- `view` : Voir les services
- `create` : Créer un service
- `edit` : Modifier un service
- `delete` : Supprimer un service
- `import_csv` : Importer depuis CSV

### Entreprises (`entreprises`)
- `view` : Voir les entreprises
- `create` : Créer une entreprise
- `edit` : Modifier une entreprise
- `delete` : Supprimer une entreprise

### Secteurs d'activité (`secteurs_activite`)
- `view` : Voir les secteurs
- `create` : Créer un secteur
- `edit` : Modifier un secteur
- `delete` : Supprimer un secteur

### Paramètres (`settings`)
- `view` : Voir les paramètres
- `edit` : Modifier les paramètres

### Historique (`historique`)
- `view` : Voir l'historique
- `export` : Exporter l'historique

### Tableau de bord (`dashboard`)
- `view_all_stats` : Voir toutes les statistiques
- `view_own_stats` : Voir ses propres statistiques

### Génération (`generation`)
- `generate_test_data` : Générer des données de test

## 🛠️ Utilisation dans le code

### Vérification des permissions dans les modèles

```php
// Dans un modèle User
$user = Auth::user();

// Vérifier une permission spécifique
if ($user->hasPermission('clients', 'create')) {
    // L'utilisateur peut créer des clients
}

// Méthodes helper
if ($user->canView('devis')) {
    // L'utilisateur peut voir les devis
}

if ($user->canCreate('factures')) {
    // L'utilisateur peut créer des factures
}

if ($user->canDelete('users')) {
    // L'utilisateur peut supprimer des utilisateurs
}
```

### Utilisation dans les ressources Filament

```php
// Dans une ressource Filament
use App\Filament\Resources\Traits\HasPermissions;

class ClientResource extends Resource
{
    use HasPermissions;

    public static function table(Table $table): Table
    {
        return $table
            ->actions([
                // Actions conditionnelles basées sur les permissions
                ...static::configureRowActions(),
            ])
            ->headerActions([
                ...static::configureBaseActions(),
            ]);
    }
}
```

### Vérification dans les contrôleurs

```php
// Dans un contrôleur
public function store(Request $request)
{
    $user = Auth::user();
    
    if (!$user->canCreate('clients')) {
        abort(403, 'Accès non autorisé');
    }
    
    // Logique de création...
}
```

### Vérification dans les vues Blade

```php
{{-- Dans une vue Blade --}}
@if(auth()->user()->canView('devis'))
    <a href="{{ route('devis.index') }}">Voir les devis</a>
@endif

@if(auth()->user()->canCreate('factures'))
    <a href="{{ route('factures.create') }}">Nouvelle facture</a>
@endif
```

## 🚀 Utilisateurs de test

Le système inclut des utilisateurs de test avec différents rôles :

| Email | Nom | Rôle | Mot de passe |
|-------|-----|------|--------------|
| `super@admin.com` | Super Admin | Super administrateur | `password123` |
| `admin@admin.com` | Admin Principal | Administrateur | `password123` |
| `manager@admin.com` | Manager Commercial | Manager | `password123` |
| `commercial@admin.com` | Commercial Senior | Commercial | `password123` |
| `commercial2@admin.com` | Commercial Junior | Commercial | `password123` |
| `support@admin.com` | Support Niveau 1 | Support | `password123` |
| `support2@admin.com` | Support Niveau 2 | Support | `password123` |
| `viewer@admin.com` | Lecteur Consultant | Lecteur | `password123` |
| `admin2@admin.com` | Admin RH | Administrateur | `password123` |
| `manager2@admin.com` | Manager Projets | Manager | `password123` |

## 🧪 Tests

Pour tester le système de permissions :

```bash
# Exécuter le seeder de test
php artisan db:seed --class=PermissionTestSeeder

# Ou exécuter tous les seeders
php artisan db:seed
```

## 📝 Ajout de nouvelles permissions

### 1. Ajouter la permission dans le seeder de rôles

```php
// Dans UserRoleSeeder.php
'permissions' => [
    'nouvelle_ressource' => ['view', 'create', 'edit', 'delete'],
],
```

### 2. Ajouter les méthodes helper dans le modèle User

```php
// Dans User.php
public function canManageNouvelleRessource(): bool
{
    return $this->hasPermission('nouvelle_ressource', 'manage');
}
```

### 3. Utiliser dans les ressources Filament

```php
// Dans la ressource
if (static::canManageNouvelleRessource()) {
    // Logique spécifique
}
```

## 🔒 Sécurité

- **Vérification côté serveur** : Toutes les permissions sont vérifiées côté serveur
- **Pas de permissions côté client** : Les permissions ne sont jamais exposées au client
- **Fallback sécurisé** : En cas d'erreur, l'accès est refusé par défaut
- **Audit trail** : Toutes les actions sont enregistrées dans l'historique

## 📊 Monitoring

Le système permet de :
- **Suivre les permissions** de chaque utilisateur
- **Auditer les accès** via l'historique
- **Gérer les rôles** de manière centralisée
- **Tester les permissions** avec le seeder de test

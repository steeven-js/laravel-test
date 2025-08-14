# Masquage de Navigation selon les Permissions

## Vue d'ensemble

Le Dashboard Madinia implémente un système de masquage intelligent de la navigation qui cache automatiquement les pages et groupes de navigation non autorisés aux utilisateurs selon leurs permissions.

## Fonctionnement

### Principe de base

- **Super administrateur** : Voit tous les éléments de navigation
- **Autres utilisateurs** : Ne voient que les éléments pour lesquels ils ont la permission `view`
- **Groupes vides** : Les groupes ne contenant aucun élément autorisé sont automatiquement masqués

### Logique de filtrage

Le système vérifie les permissions au niveau de chaque ressource :

```php
// Exemple pour le groupe CRM
if ($user->canView('clients')) {
    $crmItems[] = ...ClientResource::getNavigationItems();
}
if ($user->canView('entreprises')) {
    $crmItems[] = ...EntrepriseResource::getNavigationItems();
}
if ($user->canView('opportunities')) {
    $crmItems[] = ...OpportunityResource::getNavigationItems();
}

// Le groupe n'est créé que s'il contient des éléments
if (!empty($crmItems)) {
    $groups[] = NavigationGroup::make('CRM')->items($crmItems);
}
```

## Éléments toujours visibles

### Dashboard
- **Toujours visible** pour tous les utilisateurs connectés
- **Icône** : `heroicon-o-home`
- **URL** : `/admin`

## Groupes de navigation

### 📊 CRM
**Permissions requises** : `clients.view`, `entreprises.view`, `opportunities.view`

**Ressources** :
- Clients (`clients.view`)
- Entreprises (`entreprises.view`)
- Opportunités (`opportunities.view`)

**Exemple d'affichage** :
- **Manager** : Voir tous les éléments
- **Commercial** : Voir Clients et Opportunités
- **Support** : Aucun élément (groupe masqué)

### 💰 Ventes
**Permissions requises** : `devis.view`, `factures.view`

**Ressources** :
- Devis (`devis.view`)
- Factures (`factures.view`)

**Exemple d'affichage** :
- **Manager** : Voir tous les éléments
- **Commercial** : Voir seulement Devis
- **Support** : Aucun élément (groupe masqué)

### 📧 Communication
**Permissions requises** : `emailtemplates.view`, `clientemails.view`, `notifications.view`

**Ressources** :
- Templates d'emails (`emailtemplates.view`)
- Emails clients (`clientemails.view`)
- Notifications (`notifications.view`)

### 📚 Référentiels
**Permissions requises** : `services.view`, `secteursactivite.view`

**Ressources** :
- Services (`services.view`)
- Secteurs d'activité (`secteursactivite.view`)

### 🛠️ Support
**Permissions requises** : `tickets.view`, `todos.view`

**Ressources** :
- Tickets (`tickets.view`)
- Tâches (`todos.view`)

### ⚙️ Réglages
**Permissions requises** : `madinia.view`, `settings.view`

**Ressources** :
- Paramètres Madinia (`madinia.view`)
- Numéros de séquence (`settings.view`)

### 🔐 Administration
**Permissions requises** : `userroles.view` OU `users.manage_roles`, `users.view`

**Ressources** :
- Rôles et permissions (`userroles.view` OU `users.manage_roles`)
- Utilisateurs (`users.view`)

## Exemples par rôle

### Super administrateur
```
✅ Dashboard
📊 CRM (Clients, Entreprises, Opportunités)
💰 Ventes (Devis, Factures)
📧 Communication (Templates, Emails, Notifications)
📚 Référentiels (Services, Secteurs)
🛠️ Support (Tickets, Tâches)
⚙️ Réglages (Madinia, Numéros)
🔐 Administration (Rôles, Utilisateurs)
```

### Manager
```
✅ Dashboard
📊 CRM (Clients, Entreprises, Opportunités)
💰 Ventes (Devis, Factures)
📚 Référentiels (Services)
🛠️ Support (Tickets, Tâches)
⚙️ Réglages (Madinia)
```

### Commercial
```
✅ Dashboard
📊 CRM (Clients, Opportunités)
💰 Ventes (Devis)
📚 Référentiels (Services)
```

### Support
```
✅ Dashboard
🛠️ Support (Tickets, Tâches)
📚 Référentiels (Services)
```

### Lecteur
```
✅ Dashboard
📊 CRM (Clients)
💰 Ventes (Devis, Factures)
📚 Référentiels (Services, Entreprises, Secteurs)
🛠️ Support (Tickets, Tâches)
```

## Implémentation technique

### Fichier principal
`app/Providers/Filament/AdminPanelProvider.php`

### Méthodes clés

#### `buildFullNavigation()`
- Navigation complète pour les super administrateurs
- Tous les groupes et éléments visibles

#### `buildFilteredNavigation()`
- Navigation filtrée selon les permissions
- Vérification de chaque ressource avec `$user->canView()`
- Création conditionnelle des groupes

### Vérification des permissions
```php
// Dans le modèle User
public function canView(string $resource): bool
{
    return $this->hasPermission($resource, 'view');
}

public function hasPermission(string $resource, string $action): bool
{
    if ($this->isSuperAdmin()) {
        return true;
    }

    $permissions = $this->userRole?->permissions ?? [];
    return in_array($action, $permissions[$resource] ?? []);
}
```

## Tests et validation

### Commande de test
```bash
# Tester un utilisateur spécifique
php artisan navigation:test-permissions --user-id=1

# Tester un rôle spécifique
php artisan navigation:test-permissions --role=commercial

# Tester le premier utilisateur disponible
php artisan navigation:test-permissions
```

### Exemple de sortie
```
🧭 Test du système de masquage de navigation selon les permissions

👤 Utilisateur testé : Commercial Senior (commercial@admin.com)
🔑 Rôle : Commercial

📋 Éléments de navigation visibles :

✅ Dashboard (toujours visible)

📊 Groupe CRM :
   ✅ Clients
   ✅ Opportunités

💰 Groupe Ventes :
   ✅ Devis

📚 Groupe Référentiels :
   ✅ Services

🎯 Résumé :
   • Éléments visibles : 5
   • Groupes visibles : 4
   • Statut : Utilisateur avec permissions filtrées
```

## Avantages

### Sécurité
- **Masquage automatique** des éléments non autorisés
- **Pas d'accès direct** aux URLs des ressources masquées
- **Cohérence** entre interface et permissions

### Expérience utilisateur
- **Interface épurée** sans éléments inutiles
- **Navigation intuitive** selon les droits
- **Réduction de la confusion** pour les utilisateurs

### Maintenance
- **Configuration centralisée** dans les rôles
- **Évolution automatique** lors des changements de permissions
- **Tests automatisés** disponibles

## Bonnes pratiques

### Configuration des rôles
1. **Définir clairement** les permissions de chaque rôle
2. **Tester la navigation** après modification des permissions
3. **Documenter** les accès par rôle

### Développement
1. **Toujours vérifier** les permissions côté serveur
2. **Utiliser la commande de test** pour valider
3. **Maintenir la cohérence** entre permissions et navigation

### Évolution
1. **Ajouter les nouvelles ressources** dans le filtrage
2. **Tester avec différents rôles** lors des ajouts
3. **Mettre à jour la documentation** des permissions

## Dépannage

### Problèmes courants

#### Un élément n'apparaît pas
- Vérifier que l'utilisateur a la permission `view` pour la ressource
- Contrôler que le rôle est bien assigné
- Utiliser la commande de test pour diagnostiquer

#### Un groupe apparaît vide
- Vérifier les permissions des ressources du groupe
- S'assurer que les ressources sont bien configurées
- Contrôler la logique de création du groupe

#### Erreur de navigation
- Vérifier que l'utilisateur est bien connecté
- Contrôler que le rôle existe et est actif
- S'assurer que les permissions sont bien formatées

### Commandes utiles
```bash
# Tester la navigation
php artisan navigation:test-permissions

# Vérifier les permissions d'un utilisateur
php artisan user:check-permissions {user_id}

# Lister les rôles et leurs permissions
php artisan user-roles:list
```

## Évolutions futures

### Fonctionnalités prévues
- [ ] Permissions granulaires par enregistrement
- [ ] Navigation dynamique selon le contexte
- [ ] Personnalisation de l'ordre des éléments
- [ ] Masquage conditionnel selon l'heure/date

### Améliorations techniques
- [ ] Cache des permissions pour les performances
- [ ] API pour la gestion de la navigation
- [ ] Interface de configuration visuelle
- [ ] Audit des accès à la navigation

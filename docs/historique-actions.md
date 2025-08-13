# Système d'Historique des Actions

Ce document explique comment utiliser le système d'historique des actions dans votre application Laravel avec Filament.

## Vue d'ensemble

Le système d'historique permet de tracer automatiquement toutes les actions effectuées sur vos modèles :
- Création d'enregistrements
- Modification d'enregistrements
- Suppression d'enregistrements
- Actions personnalisées (changement de statut, envoi d'email, etc.)

## Installation et Configuration

### 1. Configuration

Le fichier de configuration `config/historique.php` contient tous les paramètres du système :

```php
// Modèles à traquer
'models' => [
    'App\Models\Client',
    'App\Models\Entreprise',
    // ... autres modèles
],

// Actions à traquer
'actions' => [
    'creation' => ['label' => 'Création', 'color' => 'success'],
    'modification' => ['label' => 'Modification', 'color' => 'primary'],
    // ... autres actions
],
```

### 2. Ajout automatique aux modèles

Exécutez la commande suivante pour ajouter le trait `HasHistorique` à tous les modèles configurés :

```bash
php artisan historique:add-to-models
```

### 3. Ajout automatique aux resources Filament

Exécutez la commande suivante pour ajouter le trait `HasHistoriqueResource` à tous les resources :

```bash
php artisan historique:add-to-resources
```

## Utilisation

### Dans les modèles

Une fois le trait ajouté, l'historique est automatiquement enregistré pour :

- **Créations** : Lorsqu'un nouvel enregistrement est créé
- **Modifications** : Lorsqu'un enregistrement est modifié
- **Suppressions** : Lorsqu'un enregistrement est supprimé

#### Actions personnalisées

Vous pouvez également enregistrer des actions personnalisées :

```php
// Dans votre modèle ou contrôleur
$client->enregistrerChangementStatut('actif', 'inactif', 'Client désactivé par l\'administrateur');

$client->enregistrerActionPersonnalisee(
    'envoi_email',
    'Email de bienvenue envoyé',
    'Email de bienvenue envoyé au client'
);
```

### Dans Filament

#### Affichage de l'historique

L'historique est automatiquement affiché dans un onglet "Historique des actions" pour chaque ressource.

#### Colonnes affichées

- **Action** : Type d'action avec badge coloré et icône
- **Titre** : Titre de l'action
- **Description** : Description détaillée
- **Par** : Utilisateur qui a effectué l'action
- **Date** : Date et heure de l'action
- **IP** : Adresse IP (masquée par défaut)

#### Détails des actions

Cliquez sur "Voir les détails" pour afficher :

- **Informations générales** : Action, date, utilisateur, titre, description
- **Données JSON** : 
  - **Avant** : État des données avant modification
  - **Après** : État des données après modification
  - **Informations supplémentaires** : Données contextuelles
- **Contexte technique** : IP, User Agent

#### Filtres disponibles

- **Type d'action** : Filtrer par type d'action (création, modification, etc.)
- **Recherche** : Recherche dans tous les champs

## Personnalisation

### Ajouter de nouveaux types d'actions

Modifiez le fichier `config/historique.php` :

```php
'actions' => [
    // ... actions existantes
    'nouvelle_action' => [
        'label' => 'Nouvelle Action',
        'color' => 'warning',
        'icon' => 'heroicon-o-star',
    ],
],
```

### Exclure des champs de l'historique

Ajoutez des champs à exclure dans la configuration :

```php
'exclude_fields' => [
    'updated_at',
    'created_at',
    'password',
    // ... autres champs
],
```

### Limiter le nombre d'historiques

```php
'max_records_per_entity' => 500, // Limiter à 500 enregistrements par entité
```

## Structure de la base de données

### Table `historique`

- `id` : Identifiant unique
- `entite_type` : Classe du modèle (ex: App\Models\Client)
- `entite_id` : ID de l'enregistrement
- `action` : Type d'action
- `titre` : Titre de l'action
- `description` : Description détaillée
- `donnees_avant` : Données avant modification (JSON)
- `donnees_apres` : Données après modification (JSON)
- `donnees_supplementaires` : Données contextuelles (JSON)
- `user_id` : ID de l'utilisateur
- `user_nom` : Nom de l'utilisateur
- `user_email` : Email de l'utilisateur
- `ip_address` : Adresse IP
- `user_agent` : User Agent
- `created_at` : Date de création

## Exemples d'utilisation

### Suivi des modifications de clients

```php
// L'historique est automatiquement créé lors de la modification
$client->update([
    'email' => 'nouveau@email.com',
    'telephone' => '0123456789'
]);

// Résultat dans l'historique :
// - Action : modification
// - Données avant : {"email": "ancien@email.com", "telephone": "0987654321"}
// - Données après : {"email": "nouveau@email.com", "telephone": "0123456789"}
```

### Suivi des changements de statut

```php
// Enregistrer un changement de statut avec raison
$devis->enregistrerChangementStatut(
    'en_attente',
    'accepte',
    'Devis accepté par le client via email'
);
```

### Suivi des actions personnalisées

```php
// Enregistrer une action personnalisée
$facture->enregistrerActionPersonnalisee(
    'paiement_stripe',
    'Paiement Stripe reçu',
    'Paiement de ' . $facture->montant_ttc . '€ reçu via Stripe',
    [
        'stripe_payment_intent_id' => 'pi_1234567890',
        'montant' => $facture->montant_ttc,
        'devise' => 'EUR'
    ]
);
```

## Maintenance

### Nettoyage automatique

Vous pouvez créer une tâche planifiée pour nettoyer les anciens historiques :

```php
// Dans App\Console\Kernel
$schedule->command('historique:cleanup')->daily();
```

### Commande de nettoyage

```bash
php artisan historique:cleanup --days=30 --keep=100
```

## Dépannage

### L'historique ne s'affiche pas

1. Vérifiez que le trait `HasHistorique` est bien ajouté au modèle
2. Vérifiez que le trait `HasHistoriqueResource` est bien ajouté au resource
3. Vérifiez que la table `historique` existe et est accessible

### Erreurs de performance

1. Ajoutez des index sur les colonnes fréquemment utilisées
2. Limitez le nombre d'historiques conservés par entité
3. Utilisez la pagination dans Filament

### Données manquantes

1. Vérifiez que l'utilisateur est bien authentifié
2. Vérifiez les permissions d'accès à la base de données
3. Vérifiez les logs d'erreur Laravel

## Support

Pour toute question ou problème avec le système d'historique, consultez :

1. Les logs Laravel (`storage/logs/laravel.log`)
2. La documentation Filament
3. Les issues GitHub du projet

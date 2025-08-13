# 🕒 Système d'Historique des Actions - Guide Complet

Ce guide explique comment utiliser le système d'historique des actions que nous venons d'implémenter dans votre application Laravel avec Filament.

## 🎯 Vue d'ensemble

Le système d'historique permet de tracer automatiquement **toutes les actions** effectuées sur vos modèles :
- ✅ **Créations** d'enregistrements
- ✅ **Modifications** d'enregistrements  
- ✅ **Suppressions** d'enregistrements
- ✅ **Actions personnalisées** (changement de statut, envoi d'email, etc.)

## 🚀 Installation et Configuration

### 1. Vérification de l'installation

Le système est déjà installé ! Vérifiez que vous avez :

- ✅ Table `historique` dans la base de données
- ✅ Modèle `Historique` 
- ✅ Trait `HasHistorique` ajouté aux modèles
- ✅ Trait `HasHistoriqueResource` ajouté aux resources Filament

### 2. Configuration

Le fichier `config/historique.php` contient tous les paramètres :

```php
// Modèles à traquer automatiquement
'models' => [
    'App\Models\Client',
    'App\Models\Entreprise',
    'App\Models\Devis',
    // ... autres modèles
],

// Actions disponibles
'actions' => [
    'creation' => ['label' => 'Création', 'color' => 'success'],
    'modification' => ['label' => 'Modification', 'color' => 'primary'],
    'suppression' => ['label' => 'Suppression', 'color' => 'danger'],
    // ... autres actions
],
```

## 📱 Utilisation dans Filament

### Affichage automatique

L'historique s'affiche automatiquement dans un onglet **"Historique des actions"** pour chaque ressource.

### Page personnalisée

Pour un affichage détaillé comme dans votre capture d'écran, accédez à :

```
/clients/{id}/historique
```

Cette page affiche :
- 📊 **Liste chronologique** des actions
- 🔍 **Détails JSON** avant/après pour chaque modification
- 👤 **Informations utilisateur** (qui a fait quoi)
- 🕐 **Horodatage** précis de chaque action
- 💻 **Contexte technique** (IP, User Agent)

## 🛠️ Utilisation dans le code

### Actions automatiques

Une fois le trait ajouté, l'historique est **automatiquement** enregistré :

```php
// Création - historique automatique
$client = Client::create([
    'nom' => 'Dupont',
    'email' => 'dupont@example.com'
]);

// Modification - historique automatique
$client->update(['email' => 'nouveau@email.com']);

// Suppression - historique automatique
$client->delete();
```

### Actions personnalisées

Vous pouvez enregistrer des actions spécifiques :

```php
// Changement de statut
$client->enregistrerChangementStatut(
    'actif', 
    'inactif', 
    'Client désactivé par l\'administrateur'
);

// Action personnalisée
$client->enregistrerActionPersonnalisee(
    'envoi_email',
    'Email de bienvenue envoyé',
    'Email de bienvenue envoyé au client',
    ['template' => 'welcome', 'status' => 'sent']
);

// Action avec données détaillées
$client->enregistrerAction(
    'paiement_stripe',
    'Paiement reçu',
    'Paiement de 150€ reçu via Stripe',
    null, // données avant
    ['montant' => 150, 'devise' => 'EUR'], // données après
    ['stripe_id' => 'pi_123456'] // données supplémentaires
);
```

## 🎨 Personnalisation de l'affichage

### Couleurs et icônes

Chaque type d'action a sa couleur et icône :

- 🟢 **Création** : Vert avec icône document-plus
- 🔵 **Modification** : Bleu avec icône crayon
- 🔴 **Suppression** : Rouge avec icône poubelle
- 🟡 **Changement de statut** : Jaune avec icône flèche
- 🟣 **Envoi d'email** : Violet avec icône enveloppe

### Ajouter de nouveaux types

Modifiez `config/historique.php` :

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

## 🔍 Filtrage et recherche

### Dans l'interface Filament

- **Filtre par type d'action** : Création, Modification, Suppression, etc.
- **Recherche textuelle** : Dans tous les champs
- **Tri chronologique** : Plus récent en premier
- **Pagination** : Gestion des gros volumes

### Dans le code

```php
// Récupérer l'historique d'un client
$historiques = $client->historiques()
    ->where('action', 'modification')
    ->whereDate('created_at', '>=', now()->subDays(30))
    ->orderBy('created_at', 'desc')
    ->get();

// Compter les actions par type
$stats = $client->historiques()
    ->selectRaw('action, COUNT(*) as count')
    ->groupBy('action')
    ->get();
```

## 📊 Structure des données

### Table `historique`

```sql
CREATE TABLE historique (
    id BIGINT PRIMARY KEY,
    entite_type VARCHAR(255),      -- Classe du modèle
    entite_id BIGINT,              -- ID de l'enregistrement
    action VARCHAR(255),            -- Type d'action
    titre VARCHAR(255),             -- Titre de l'action
    description TEXT,               -- Description détaillée
    donnees_avant JSON,            -- Données avant modification
    donnees_apres JSON,            -- Données après modification
    donnees_supplementaires JSON,   -- Données contextuelles
    user_id BIGINT,                -- ID de l'utilisateur
    user_nom VARCHAR(255),         -- Nom de l'utilisateur
    user_email VARCHAR(255),       -- Email de l'utilisateur
    ip_address VARCHAR(45),        -- Adresse IP
    user_agent TEXT,               -- User Agent
    created_at TIMESTAMP           -- Date de création
);
```

### Exemple de données JSON

```json
// Modification d'un client
{
  "donnees_avant": {
    "email": "ancien@email.com",
    "telephone": "0123456789"
  },
  "donnees_apres": {
    "email": "nouveau@email.com", 
    "telephone": "0987654321"
  }
}
```

## 🧪 Tests

Exécutez les tests pour vérifier le bon fonctionnement :

```bash
# Test complet du système
php artisan test --filter=HistoriqueActionsTest

# Test spécifique
php artisan test --filter=it_creates_historique_on_client_creation
```

## 🔧 Maintenance

### Nettoyage automatique

Créez une tâche planifiée dans `app/Console/Kernel.php` :

```php
protected function schedule(Schedule $schedule): void
{
    // Nettoyer l'historique tous les jours
    $schedule->command('historique:cleanup')->daily();
}
```

### Commande de nettoyage

```bash
# Nettoyer l'historique de plus de 30 jours
php artisan historique:cleanup --days=30

# Garder maximum 100 enregistrements par entité
php artisan historique:cleanup --keep=100
```

## 📱 Interface utilisateur

### Affichage dans Filament

1. **Onglet "Historique des actions"** dans chaque ressource
2. **Page dédiée** `/clients/{id}/historique` pour un affichage complet
3. **Modal de détails** avec données JSON formatées
4. **Filtres et recherche** intégrés

### Fonctionnalités interactives

- 🔽 **Bouton "Voir les détails"** pour afficher/masquer les données JSON
- 🎨 **Badges colorés** pour chaque type d'action
- 📱 **Interface responsive** pour mobile et desktop
- ⚡ **Animations fluides** d'apparition/disparition

## 🚨 Dépannage

### L'historique ne s'affiche pas

1. ✅ Vérifiez que le trait `HasHistorique` est dans le modèle
2. ✅ Vérifiez que le trait `HasHistoriqueResource` est dans le resource
3. ✅ Vérifiez que la table `historique` existe
4. ✅ Vérifiez que l'utilisateur est authentifié

### Erreurs de performance

1. 📊 Ajoutez des index sur `entite_type`, `entite_id`, `created_at`
2. 🗑️ Limitez le nombre d'historiques conservés
3. 📄 Utilisez la pagination dans Filament
4. 🔍 Optimisez les requêtes avec des relations

### Données manquantes

1. 👤 Vérifiez l'authentification de l'utilisateur
2. 🔐 Vérifiez les permissions d'accès
3. 📝 Consultez les logs Laravel
4. 🗄️ Vérifiez la structure de la base de données

## 📚 Ressources additionnelles

- 📖 **Documentation Filament** : [filamentphp.com](https://filamentphp.com)
- 🎥 **Vidéos tutorielles** : [YouTube Filament](https://youtube.com/@filamentphp)
- 💬 **Support communautaire** : [Discord Filament](https://discord.gg/filament)
- 🐛 **Issues et bugs** : [GitHub Filament](https://github.com/filamentphp/filament)

## 🎉 Conclusion

Votre système d'historique des actions est maintenant **entièrement fonctionnel** ! 

Vous pouvez :
- 📊 **Suivre toutes les modifications** de vos données
- 🔍 **Auditer les changements** avec détails JSON avant/après
- 👤 **Identifier qui a fait quoi** et quand
- 📱 **Afficher l'historique** dans une interface moderne et intuitive
- 🛠️ **Personnaliser** les types d'actions et l'affichage

N'hésitez pas à adapter le système à vos besoins spécifiques ! 🚀

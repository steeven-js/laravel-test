# Seeder des Modèles d'Email

## Description

Ce seeder permet d'importer des modèles d'email prédéfinis dans la base de données. Il respecte la contrainte unique qui garantit qu'un seul template par catégorie peut être marqué comme "par défaut".

## Utilisation

### Exécution du seeder

```bash
# Exécuter le seeder via la commande Artisan
php artisan seed:email-templates

# Ou via le seeder principal
php artisan db:seed --class=EmailTemplateSeeder
```

### Fichiers de données

Le seeder utilise les fichiers suivants :
- **CSV** : `database/seeders/data/email_templates.csv` (optionnel)
- **Fallback** : Données codées en dur si le CSV n'existe pas

### Exécution via le DatabaseSeeder

Le seeder est automatiquement inclus dans le `DatabaseSeeder` et sera exécuté avec :

```bash
php artisan db:seed
```

## Modèles créés

Le seeder crée 6 modèles d'email répartis dans 4 catégories :

### 1. Envoi initial (envoi_initial)
- **Devis promotionnel** (par défaut) - Template avec offre spéciale
- **Devis concis et direct** - Template court et efficace  
- **Devis standard professionnel** - Template professionnel standard

### 2. Rappel (rappel)
- **Rappel avec offre spéciale** (par défaut) - Rappel avec offre promotionnelle

### 3. Relance (relance)
- **Suivi standard** (par défaut) - Relance bienveillante et professionnelle

### 4. Confirmation (confirmation)
- **Confirmation avec demande d'informations** (par défaut) - Confirmation avec collecte d'informations

## Contraintes respectées

### Contrainte unique sur is_default

- Un seul template par catégorie peut être marqué comme `is_default = true`
- La contrainte est gérée au niveau de la base de données (PostgreSQL)
- Le modèle `EmailTemplate` gère automatiquement la désactivation des autres templates lors de la mise à jour

### Validation des sous-catégories

Les sous-catégories sont validées selon les valeurs autorisées définies dans la migration :
- `envoi_initial`: promotionnel, concis_direct, standard_professionnel, detaille_etapes, personnalise_chaleureux
- `rappel`: rappel_offre_speciale, rappel_date_expiration, rappel_standard
- `relance`: suivi_standard, suivi_ajustements, suivi_feedback
- `confirmation`: confirmation_infos, confirmation_etapes, confirmation_standard

## Variables disponibles

Chaque template inclut les variables appropriées pour son contexte :

- `client_nom` - Nom du client
- `devis_numero` - Numéro du devis
- `devis_montant` - Montant du devis
- `devis_validite` - Date de validité du devis
- `entreprise_nom` - Nom de l'entreprise
- `contact_telephone` - Téléphone de contact
- `numero_commande` - Numéro de commande (pour les confirmations)

## Personnalisation

Pour ajouter de nouveaux templates ou modifier les existants :

1. Modifier le tableau `$templates` dans `database/seeders/EmailTemplateSeeder.php`
2. Exécuter le seeder pour mettre à jour la base de données
3. Les templates existants seront mis à jour grâce à `updateOrCreate`

## Commandes utiles

```bash
# Vérifier les templates créés
php artisan tinker --execute="App\Models\EmailTemplate::all(['name', 'category', 'is_default'])->each(function(\$t) { echo \$t->name . ' (' . \$t->category . ') - Default: ' . (\$t->is_default ? 'Oui' : 'Non') . PHP_EOL; });"

# Compter les templates par catégorie
php artisan tinker --execute="App\Models\EmailTemplate::selectRaw('category, COUNT(*) as count, SUM(CASE WHEN is_default = true THEN 1 ELSE 0 END) as defaults')->groupBy('category')->get()->each(function(\$t) { echo \$t->category . ': ' . \$t->count . ' templates, ' . \$t->defaults . ' par défaut' . PHP_EOL; });"
```

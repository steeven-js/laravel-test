# 🎨 Standards de Codage Laravel avec Pint

Ce projet utilise **Laravel Pint** pour maintenir des standards de codage cohérents et professionnels.

## 🚀 Installation et Configuration

Laravel Pint est déjà installé et configuré dans ce projet. La configuration se trouve dans `pint.json`.

## 📋 Commandes Disponibles

### Formater le code
```bash
# Formater tous les fichiers PHP
./vendor/bin/pint

# Ou utiliser la commande Composer
composer format
```

### Vérifier le formatage (sans modifier)
```bash
# Vérifier sans modifier
./vendor/bin/pint --test

# Ou utiliser la commande Composer
composer format:check
```

### Formater un fichier spécifique
```bash
./vendor/bin/pint app/Models/User.php
```

### Formater un dossier spécifique
```bash
./vendor/bin/pint app/Models/
```

## ⚙️ Configuration

Le fichier `pint.json` utilise le preset **Laravel** avec des règles personnalisées :

- **Preset Laravel** : Standards officiels Laravel
- **Array syntax** : Syntaxe courte `[]` au lieu de `array()`
- **Binary operator spaces** : Espaces autour des opérateurs
- **Concat space** : Espace autour de la concaténation
- **Declare strict types** : `declare(strict_types=1);` automatique
- **Single quotes** : Guillemets simples pour les chaînes
- **Trailing commas** : Virgules finales dans les tableaux multilignes
- **Unused imports** : Suppression automatique des imports inutilisés

## 🔧 Intégration IDE

### VS Code
Installez l'extension **PHP CS Fixer** et configurez-la pour utiliser Pint :

```json
{
    "php-cs-fixer.executablePath": "./vendor/bin/pint",
    "php-cs-fixer.config": "./pint.json",
    "php-cs-fixer.onsave": true
}
```

### PHPStorm
Configurez **PHP CS Fixer** avec :
- **Tool path** : `./vendor/bin/pint`
- **Configuration file** : `pint.json`
- **Cochez "On save"** pour formater automatiquement

## 📁 Fichiers Exclus

Les dossiers suivants sont exclus du formatage :
- `bootstrap/cache/`
- `node_modules/`
- `public/build/`
- `public/hot/`
- `public/storage/`
- `storage/`
- `vendor/`

## 🎯 Dossiers Inclus

Seuls ces dossiers sont formatés :
- `app/`
- `config/`
- `database/`
- `lang/`
- `routes/`
- `tests/`

## 🔄 Workflow Recommandé

1. **Avant chaque commit** : `composer format:check`
2. **Si des erreurs** : `composer format`
3. **Intégration continue** : Ajouter `composer format:check` dans votre pipeline CI/CD

## 📚 Ressources

- [Documentation officielle Laravel Pint](https://laravel.com/docs/pint)
- [PHP CS Fixer Rules](https://mlocati.github.io/php-cs-fixer-configurator/)
- [PSR-12 Coding Standards](https://www.php-fig.org/psr/psr-12/)

## 🚨 Résolution de Problèmes

### Erreur de configuration
Si vous obtenez une erreur de configuration, vérifiez que `pint.json` est valide.

### Fichiers non formatés
Assurez-vous que les fichiers sont dans les dossiers inclus et ont l'extension `.php`.

### Conflits de règles
En cas de conflit, le preset Laravel est prioritaire. Modifiez `pint.json` avec précaution.

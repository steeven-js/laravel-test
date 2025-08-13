# Boutons de Génération - Opportunités, Tickets et Tâches

Ce document décrit les boutons de génération ajoutés aux pages de liste des opportunités, tickets et tâches pour créer des données de test rapidement.

## 📋 Vue d'ensemble

Trois nouveaux boutons de génération ont été ajoutés aux pages de liste correspondantes :

- **Opportunités** : `Générer des opportunités factices`
- **Tickets** : `Générer des tickets factices`
- **Tâches** : `Générer des tâches factices`

## 🔐 Accès et sécurité

### Visibilité
- **Réservé aux super admins** : Seuls les utilisateurs avec le rôle `super_admin` peuvent voir ces boutons
- **Interface Filament** : Boutons intégrés dans l'en-tête des pages de liste
- **Confirmation requise** : Chaque action nécessite une confirmation avant exécution

### Permissions
```php
->visible(fn (): bool => Auth::user()?->userRole?->name === 'super_admin')
```

## 🎯 Opportunités - Bouton de génération

### Localisation
- **Page** : `/admin/opportunities`
- **Bouton** : `Générer des opportunités factices`
- **Icône** : `heroicon-o-chart-bar`

### Fonctionnalités
- **Quantité configurable** : 1 à 50 opportunités
- **Étapes variées** : Prospection, qualification, proposition, négociation, fermeture, gagnée, perdue
- **Probabilités réalistes** : Adaptées selon l'étape
- **Montants** : 5 000€ à 50 000€
- **Dates d'échéance** : Réparties sur 6 mois
- **Notes détaillées** : Contextualisées selon l'étape

### Données générées
- **Noms réalistes** : 15 types d'opportunités différents
- **Descriptions** : 5 variantes de descriptions
- **Notes** : 10 types de notes contextuelles
- **Associations** : Clients et utilisateurs existants

### Exemple d'utilisation
1. Aller sur `/admin/opportunities`
2. Cliquer sur `Générer des opportunités factices`
3. Saisir la quantité souhaitée (ex: 10)
4. Confirmer l'action
5. Voir la notification de succès

## 🎫 Tickets - Bouton de génération

### Localisation
- **Page** : `/admin/tickets`
- **Bouton** : `Générer des tickets factices`
- **Icône** : `heroicon-o-lifebuoy`

### Fonctionnalités
- **Quantité configurable** : 1 à 50 tickets
- **Types variés** : Bug, demande, incident, question, autre
- **Priorités** : Faible, normale, haute, critique
- **Statuts** : Ouvert, en cours, résolu, fermé
- **Progression intelligente** : Adaptée selon le statut
- **Temps estimé/passé** : Proportionnel à la priorité

### Données générées
- **Titres réalistes** : 16 types de tickets différents
- **Descriptions** : 10 variantes de descriptions
- **Notes internes** : 10 types de notes techniques
- **Solutions** : 9 types de solutions pour tickets résolus
- **Visibilité client** : 80% visible par défaut

### Logique intelligente
- **Progression** : 0% (ouvert) → 10-80% (en cours) → 100% (résolu/fermé)
- **Temps** : Critique (2-8h) → Haute (4-12h) → Normale (6-16h) → Faible (2-8h)
- **Échéances** : Critique (2 jours) → Haute (1 semaine) → Normale (2 semaines) → Faible (1 mois)

### Exemple d'utilisation
1. Aller sur `/admin/tickets`
2. Cliquer sur `Générer des tickets factices`
3. Saisir la quantité souhaitée (ex: 10)
4. Confirmer l'action
5. Voir la notification de succès

## ✅ Tâches - Bouton de génération

### Localisation
- **Page** : `/admin/todos`
- **Bouton** : `Générer des tâches factices`
- **Icône** : `heroicon-o-check-circle`

### Fonctionnalités
- **Quantité configurable** : 1 à 50 tâches
- **Priorités** : Faible, normale, haute, critique
- **État d'achèvement** : 30% de tâches terminées
- **Ordre séquentiel** : Numérotation automatique
- **Dates d'échéance** : Adaptées selon la priorité

### Données générées
- **Titres réalistes** : 20 types de tâches différents
- **Descriptions** : 20 variantes de descriptions
- **Types variés** : Suivi client, préparation, administratif, technique, formation

### Logique intelligente
- **Échéances** : Critique (3 jours) → Haute (1 semaine) → Normale (2 semaines) → Faible (1 mois)
- **Tâches terminées** : Dates d'échéance dans le passé
- **Ordre** : Numérotation séquentielle (1, 2, 3...)

### Exemple d'utilisation
1. Aller sur `/admin/todos`
2. Cliquer sur `Générer des tâches factices`
3. Saisir la quantité souhaitée (ex: 15)
4. Confirmer l'action
5. Voir la notification de succès

## 🔧 Configuration et personnalisation

### Modification des données
Pour modifier les données générées :
1. Éditer le fichier de page correspondant
2. Modifier les tableaux de données dans la méthode `action()`
3. Tester avec le bouton de génération

### Ajout de nouvelles données
1. Ajouter de nouvelles entrées dans les tableaux
2. Respecter la structure existante
3. Tester la génération

### Limites configurables
- **Quantité maximale** : 50 par défaut
- **Valeurs par défaut** : Opportunités (10), Tickets (10), Tâches (15)

## 📊 Statistiques de génération

| Type | Quantité par défaut | Quantité max | Données générées |
|------|-------------------|--------------|------------------|
| Opportunités | 10 | 50 | Noms, descriptions, étapes, probabilités, montants, dates, notes |
| Tickets | 10 | 50 | Titres, descriptions, types, priorités, statuts, progression, temps, solutions |
| Tâches | 15 | 50 | Titres, descriptions, priorités, état, ordre, échéances |

## 🚀 Avantages

### Pour les développeurs
- **Tests rapides** : Génération instantanée de données de test
- **Données réalistes** : Simulation d'un environnement de production
- **Couvre tous les cas** : Différents statuts, priorités et types
- **Intégré** : Fonctionne avec le système existant

### Pour les utilisateurs
- **Interface intuitive** : Boutons dans l'en-tête des pages
- **Configuration simple** : Formulaire avec quantité
- **Feedback immédiat** : Notifications de succès
- **Sécurité** : Réservé aux super admins

## 🔍 Vérification

### Après génération
1. **Vérifier les données** : Aller sur la page de liste
2. **Contrôler les associations** : Vérifier les liens clients/utilisateurs
3. **Tester les filtres** : Utiliser les filtres de statut/priorité
4. **Vérifier les relations** : Contrôler les pages de détail

### Commandes de vérification
```bash
# Vérifier le nombre d'opportunités
php artisan tinker --execute="echo 'Opportunités: ' . App\Models\Opportunity::count();"

# Vérifier le nombre de tickets
php artisan tinker --execute="echo 'Tickets: ' . App\Models\Ticket::count();"

# Vérifier le nombre de tâches
php artisan tinker --execute="echo 'Tâches: ' . App\Models\Todo::count();"
```

## 📝 Notes importantes

- **Données existantes** : Les boutons ajoutent des données sans supprimer les existantes
- **Associations** : Utilisent les clients et utilisateurs existants
- **Performance** : Optimisé pour éviter les timeouts
- **Notifications** : Envoi de notifications en base et affichage
- **Confirmation** : Protection contre les clics accidentels

# Hôtel Clair de Lune

## Contexte
Le projet **Hôtel Clair de Lune** est une application web de gestion d'une chaîne d'hôtels, permettant aux administrateurs de gérer les établissements, aux gérants de gérer les suites de leurs hôtels, et aux clients de réserver des suites en ligne. Le projet est structuré autour des User Stories suivantes :

1. **Gérer les établissements** : L'administrateur peut créer, modifier, ou supprimer les hôtels ✅
2. **Gérer les gérants** : L'administrateur peut créer, modifier, ou supprimer les gérants d'hôtels ✅
3. **Gérer les suites** : Chaque gérant peut créer, modifier, ou supprimer les suites de son propre hôtel ✅
4. **Consulter les établissements et les suites** : Les clients peuvent voir les hôtels et les suites disponibles ✅
5. **Réserver une suite** : Les clients peuvent réserver une suite en ligne.▶️
6. **Voir et annuler ses réservations** : Les clients peuvent voir et annuler leurs réservations.▶️
7. **Contacter un établissement** : Les visiteurs et clients peuvent envoyer des questions ou demandes via un formulaire de contact ✅

Le projet inclut une interface de gestion pour les administrateurs et gérants, ainsi qu'une interface publique pour les clients.

---

## Pré-requis
Avant de récupérer et installer ce projet, assurez-vous d'avoir installé les éléments suivants sur votre machine :

- **PHP** >= 8.0.2
- **Composer**
- **Symfony CLI**
- **MySQL** ou tout autre système de gestion de base de données compatible avec Symfony
- **Node.js** et **npm** (pour gérer les dépendances front-end)
- **Git** (pour la gestion des versions)

---

## Récupération du projet
Vous pouvez cloner le projet de deux façons :
- **En HTTPS** : git clone https://github.com/2024-devops-alt-dist/hotel-cj.git
- **En SSH** : git clone git@github.com:2024-devops-alt-dist/hotel-cj.git

Puis vous pouvez installer les dépendances :
```
composer install
```

### Configuration de la base de données
Ensuite créer votre fichier .env.local, copier le fichier .env et configurer les informations de connexion à la base de données (DATABASE_URL).

Puis :
```
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load
```

### Lancer le serveur :
```
symfony serve
```

--- 
### Accès BO 
Accès à l'interface backoffice : /admin

(id/password disponible dans le fichier Fixtures)

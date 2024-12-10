
# Projet Symfony - Location de livre

## Description
Ce projet est basé sur Symfony et propose une application web avec des fonctionnalités d'inscription, d'authentification et de gestion des ressources. Il inclut des opérations CRUD, la gestion des rôles utilisateurs et une structure bien organisée pour une meilleure évolutivité.

## Pré-requis
Avant de lancer le projet, assurez-vous d'avoir les outils suivants installés :
- PHP 8.1 ou supérieur
- Composer
- Symfony CLI
- Node.js et npm/yarn (pour la gestion des assets)
- Une base de données (ex. : MySQL, PostgreSQL, SQLite)

## Installation
Suivez ces étapes pour installer et exécuter le projet :

1. Clonez le projet :
   ```bash
   git clone <url_du_dépôt>
   cd tuto
   ```

2. Installez les dépendances PHP :
   ```bash
   composer install
   ```

3. Installez les dépendances JavaScript (si nécessaire) :
   ```bash
   npm install
   # ou
   yarn install
   ```

4. Configurez les variables d'environnement :

   - Modifiez les identifiants de la base de données dans `.env` :
     ```env
     DATABASE_URL="mysql://utilisateur:motdepasse@127.0.0.1:3306/nom_de_la_base"
     ```

5. Effectuez les migrations de la base de données :
   ```bash
   php bin/console doctrine:migrations:migrate
   ```

6. Lancez le serveur Symfony :
   ```bash
   symfony server:start -d
   ```

7. Accédez à l'application dans votre navigateur à l'adresse `http://127.0.0.1:8000`.

## Fonctionnalités
- Authentification et gestion des utilisateurs
- Contrôle d'accès basé sur les rôles (ex. : Admin, Utilisateur)
- Opérations CRUD pour les ressources
- Modèles dynamiques avec Twig
- Intégration avec Doctrine ORM
- Gestion des assets avec Webpack Encore

## Structure du projet
- **src/** : Contient la logique principale de l'application (contrôleurs, entités, services, etc.).
- **templates/** : Contient les modèles Twig pour les vues.
- **public/** : Contient les fichiers accessibles publiquement (CSS, JavaScript, images).
- **config/** : Contient les fichiers de configuration.
- **migrations/** : Fichiers de migration de la base de données.
- **tests/** : Tests unitaires et fonctionnels.


## Contribution
N'hésitez pas à contribuer au projet en créant des pull requests. Assurez-vous que votre code respecte les standards de style existants et inclut des tests appropriés.

## Licence
Ce projet est open-source et disponible sous la licence MIT.

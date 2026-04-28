# dev-web-formation

## Présentation

Ce projet a pour objectif de m’aider à apprendre à développer une application Web en **PHP avec connexion à une base de données MySQL**.

L’application est construite **progressivement**, étape par étape, afin de comprendre la logique d’un projet Web dynamique et de bien assimiler les bases du développement côté serveur.

Ce travail est réalisé dans un environnement local avec :

- **XAMPP**
- **PHP**
- **MySQL**
- **PDO**
- **Bootstrap**

Le projet consiste à gérer des formations en ligne avec inscription gratuite.

L’application contient maintenant :

- une page publique qui présente les formations publiées
- une page utilisateur pour s’inscrire à une formation
- une page administrateur pour gérer les formations et les inscriptions
- une base d’authentification avec rôles `user` et `admin`
- une couche d’accès aux données avec `FormationRepository`

---

## Objectifs du projet

- apprendre à créer une application PHP avec connexion à une base de données
- comprendre la structure d’un projet Web simple
- mettre en place une authentification utilisateur
- gérer des rôles utilisateur et administrateur
- remplacer progressivement les données JSON par MySQL
- centraliser les requêtes SQL dans un repository
- faire évoluer le projet progressivement
- apprendre avec l’aide de l’IA tout en comprenant chaque étape

---

## Utilisation de l’IA

L’IA est utilisée comme outil d’accompagnement pour :

- structurer le projet
- expliquer les concepts techniques
- proposer du code exemple
- améliorer progressivement l’architecture
- guider la mise en place de la sécurité de base
- aider à migrer les données JSON vers une base de données MySQL
- aider à documenter le projet dans le `README.md`

L’objectif n’est pas seulement d’obtenir du code fonctionnel, mais aussi de **comprendre les choix techniques** et de pouvoir les expliquer.

Je reste responsable du code intégré au projet.

---

## Fonctionnalités actuelles

### Authentification

L’application contient une base d’authentification utilisateur.

Fonctionnalités en place :

- inscription utilisateur
- connexion utilisateur
- déconnexion
- page de profil protégée
- redirection vers `login.php` si un utilisateur non connecté tente d’accéder à une page protégée
- mots de passe sécurisés avec `password_hash()`
- gestion de sessions PHP
- gestion de rôles avec `user` et `admin`
- protection de la page administrateur côté serveur

### Formations publiques

La page `index.php` affiche les formations disponibles.

Fonctionnalités réalisées :

- récupération des formations depuis MySQL avec `FormationRepository`
- affichage uniquement des formations avec le statut `publie`
- affichage des formations sous forme de cartes Bootstrap
- affichage des catégories sous forme de badges séparés
- affichage de la durée au format vidéo, par exemple `45:00` ou `2:00:00`
- affichage du type de contenu : `video` ou `playlist`
- bouton `Voir la formation` ouvrant une fenêtre modale Bootstrap
- bouton `Inscription` menant vers `view-user.php?formation_id=ID`
- filtres JavaScript par catégorie
- filtres JavaScript par type de contenu
- message si aucune formation ne correspond aux filtres

### Inscription utilisateur à une formation

La page `view-user.php` permet à un utilisateur connecté de s’inscrire à une formation.

Fonctionnalités réalisées :

- accès protégé par authentification
- chargement des formations publiées depuis MySQL
- préselection possible avec `formation_id` dans l’URL
- formulaire d’inscription connecté à la table `inscriptions`
- récupération du `user_id` depuis l’utilisateur connecté
- création d’une inscription avec le statut par défaut `en_attente`
- champ de commentaire utilisateur
- champ `reference_source`
- champ optionnel `details_reference`
- résumé de la formation sélectionnée
- validation serveur avant insertion

### Administration des formations

La page `view-admin.php` permet à un administrateur de gérer les formations.

Fonctionnalités réalisées :

- accès réservé au rôle `admin`
- lecture des formations depuis MySQL via `FormationRepository`
- tableau dynamique des formations
- formulaire d’ajout de formation
- formulaire d’édition de formation avec préremplissage via `?edit=ID`
- suppression d’une formation
- gestion du statut : `brouillon`, `publie`, `archive`
- recherche JavaScript dans le tableau
- tri JavaScript simple
- messages de succès après création, modification ou suppression

### Administration des inscriptions

La page `view-admin.php` contient aussi une section de gestion des inscriptions.

Fonctionnalités réalisées :

- lecture des inscriptions depuis MySQL via `FormationRepository`
- jointure avec la table `users`
- jointure avec la table `formations`
- tableau dynamique des inscriptions
- formulaire d’ajout d’inscription par l’administrateur
- formulaire d’édition avec préremplissage via `?edit_inscription=ID`
- modification du statut de l’inscription
- modification du commentaire utilisateur
- modification du commentaire administrateur
- suppression d’une inscription
- recherche JavaScript dans les inscriptions
- tri JavaScript simple

---

## Pages disponibles

- `index.php` : page d’accueil et affichage public des formations publiées
- `register.php` : page d’inscription utilisateur
- `login.php` : page de connexion
- `profile.php` : page de profil utilisateur
- `logout.php` : page de déconnexion
- `view-user.php` : page utilisateur pour s’inscrire à une formation
- `view-admin.php` : page administrateur pour gérer les formations et les inscriptions

---

## Structure actuelle du projet

```text
mon_app/
│
├── index.php
├── register.php
├── login.php
├── profile.php
├── logout.php
├── view-user.php
├── view-admin.php
│
├── config/
│   └── db.php
│
├── includes/
│   └── auth.php
│
├── src/
│   └── FormationRepository.php
│
├── assets/
│   └── style.css
│
└── partials/
    ├── _footer.php
    ├── _head.php
    ├── _libs.php
    └── _navbar.php
```

---

## Base de données actuelle

### Table `users`

La table `users` contient les comptes utilisateurs de l’application.

```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    role VARCHAR(50) NOT NULL DEFAULT 'user',
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

#### Rôle des champs

| Champ | Description |
|---|---|
| `id` | Identifiant unique de l’utilisateur |
| `nom` | Nom de l’utilisateur |
| `email` | Adresse courriel utilisée pour la connexion |
| `mot_de_passe` | Mot de passe sécurisé avec hash |
| `role` | Rôle de l’utilisateur : `user` ou `admin` |
| `date_creation` | Date de création du compte |

---

### Table `formations`

La table `formations` contient les informations principales utilisées pour afficher et gérer les formations dans l’application.

```sql
CREATE TABLE formations (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(255) NOT NULL,
    description_courte VARCHAR(255) NOT NULL,
    description_longue TEXT NOT NULL,
    audience TEXT NOT NULL,
    youtube_url VARCHAR(255) NOT NULL,
    image_url VARCHAR(255) DEFAULT NULL,
    type_contenu VARCHAR(50) NOT NULL,
    duree_minutes INT(11) NOT NULL,
    nb_videos INT(11) NOT NULL DEFAULT 1,
    categories VARCHAR(255) DEFAULT NULL,
    statut VARCHAR(50) NOT NULL DEFAULT 'brouillon',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);
```

#### Rôle des champs

| Champ | Description |
|---|---|
| `id` | Identifiant unique de la formation |
| `titre` | Nom de la formation affiché dans l’interface |
| `description_courte` | Résumé court utilisé dans les cartes ou les listes |
| `description_longue` | Description détaillée affichée dans la fenêtre modale |
| `audience` | Public cible de la formation |
| `youtube_url` | Lien vers la vidéo ou le contenu YouTube |
| `image_url` | Image associée à la formation, optionnelle |
| `type_contenu` | Type de contenu : `video` ou `playlist` |
| `duree_minutes` | Durée totale de la formation en minutes |
| `nb_videos` | Nombre de vidéos dans la formation |
| `categories` | Catégories associées à la formation, séparées par virgule |
| `statut` | État de la formation : `brouillon`, `publie` ou `archive` |
| `created_at` | Date de création de la formation |

Cette table remplace progressivement les données qui étaient auparavant stockées dans un fichier JSON.

---

### Table `inscriptions`

La table `inscriptions` permet de gérer les inscriptions des utilisateurs aux formations.

Elle sert de lien entre un utilisateur inscrit dans la table `users` et une formation disponible dans la table `formations`.

```sql
CREATE TABLE inscriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    formation_id INT NOT NULL,
    commentaire TEXT NULL,
    reference_source VARCHAR(50) NOT NULL,
    details_reference VARCHAR(255) NULL,
    statut VARCHAR(50) NOT NULL DEFAULT 'en_attente',
    commentaire_admin TEXT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    INDEX (user_id),
    INDEX (formation_id)
);
```

#### Rôle des champs

| Champ | Description |
|---|---|
| `id` | Identifiant unique de l’inscription |
| `user_id` | Identifiant de l’utilisateur inscrit |
| `formation_id` | Identifiant de la formation choisie |
| `commentaire` | Commentaire laissé par l’utilisateur lors de l’inscription |
| `reference_source` | Source par laquelle l’utilisateur a connu la formation |
| `details_reference` | Détail optionnel sur la source de référence |
| `statut` | État de l’inscription : `en_attente`, `confirmee` ou `annulee` |
| `commentaire_admin` | Note interne ajoutée par l’administrateur |
| `created_at` | Date de création de l’inscription |

---

## Données de démonstration

Le projet utilise des formations de démonstration adaptées à la structure de la table `formations`.

Exemples de formations utilisées :

- Bootstrap v2 - pages efficaces
- Marketing - offre claire
- Productivité - plan 7 jours
- SEO - bases solides
- Emailing - newsletter simple
- Bootstrap avancé

Ces données servent à tester :

- l’affichage des cartes sur `index.php`
- les filtres par catégorie
- les filtres par type de contenu
- les fenêtres modales de détail
- le formulaire d’inscription
- le tableau administrateur

---

## Couche d’accès aux données

Le fichier `src/FormationRepository.php` centralise les requêtes SQL liées aux formations, aux utilisateurs et aux inscriptions.

Cette classe reçoit une connexion `PDO` dans son constructeur.

### Méthodes principales pour les formations

- `all()` : retourne toutes les formations pour l’administration
- `allPublished()` : retourne seulement les formations publiées
- `find($id)` : retourne une formation par son identifiant
- `create($data)` : crée une formation
- `update($id, $data)` : modifie une formation
- `delete($id)` : supprime une formation
- `save($data)` : crée ou modifie selon la présence d’un identifiant

### Méthodes principales pour les inscriptions

- `allUsers()` : retourne les utilisateurs disponibles pour les inscriptions
- `allInscriptions()` : retourne les inscriptions avec les informations utilisateur et formation
- `findInscription($id)` : retourne une inscription par son identifiant
- `createInscription($data)` : crée une inscription
- `updateInscription($id, $data)` : modifie une inscription
- `deleteInscription($id)` : supprime une inscription
- `saveInscription($data)` : crée ou modifie selon la présence d’un identifiant

### Validation serveur

Le repository contient aussi une validation serveur simple.

Pour les formations :

- le titre est obligatoire
- la description courte est obligatoire
- la description longue est obligatoire
- l’audience est obligatoire
- l’URL YouTube est obligatoire et doit être valide
- le type de contenu doit être `video` ou `playlist`
- la durée doit être supérieure à 0
- le nombre de vidéos doit être supérieur à 0
- le statut doit être `brouillon`, `publie` ou `archive`

Pour les inscriptions :

- l’utilisateur est obligatoire
- la formation est obligatoire
- la source de référence est obligatoire
- le statut doit être `en_attente`, `confirmee` ou `annulee`

---

## Logique générale du projet

### Utilisateur non connecté

- peut accéder à l’accueil
- peut s’inscrire
- peut se connecter
- est redirigé vers `login.php` s’il tente d’accéder à une page protégée

### Utilisateur connecté

- peut accéder à son profil
- peut se déconnecter
- peut consulter les formations publiées
- peut accéder à `view-user.php`
- peut s’inscrire à une formation
- son inscription est enregistrée avec le statut `en_attente`

### Administrateur

- possède le rôle `admin`
- peut accéder à une page d’administration réservée
- peut gérer les formations
- peut créer, modifier ou supprimer une formation
- peut publier, archiver ou garder une formation en brouillon
- peut consulter les inscriptions
- peut créer, modifier ou supprimer une inscription
- peut modifier le statut d’une inscription
- peut ajouter un commentaire administrateur

---

## Migration JSON vers base de données

Au départ, les formations étaient stockées dans un fichier JSON.

La migration est maintenant amorcée :

- les données de formation sont structurées dans la table `formations`
- la page `index.php` lit les formations depuis MySQL
- le repository remplace progressivement la lecture directe du JSON
- l’administration permet de gérer les formations depuis une interface Web
- les inscriptions sont enregistrées dans la table `inscriptions`

Le JSON n’est plus la source principale pour les formations.

---

## Approche pédagogique

Ce projet me permet d’apprendre à :

- structurer une application PHP
- séparer la connexion BD, l’authentification, les pages et les requêtes SQL
- comprendre la différence entre authentification et autorisation
- protéger des pages selon la session utilisateur
- mettre en place une base de sécurité correcte
- remplacer des données statiques ou JSON par MySQL
- utiliser PDO et des requêtes préparées
- créer une couche repository simple
- afficher des données dynamiques dans une interface Bootstrap
- gérer un formulaire d’ajout et d’édition
- gérer des inscriptions liées à des utilisateurs et à des formations
- faire évoluer une application étape par étape

---

## Prochaines améliorations possibles

- empêcher une double inscription à la même formation pour le même utilisateur
- ajouter des clés étrangères entre `users`, `formations` et `inscriptions`
- améliorer les messages d’erreur et de validation
- ajouter une page détail dédiée pour une formation
- ajouter une pagination ou un filtrage côté serveur
- améliorer l’interface administrateur
- ajouter un champ `updated_at` si le suivi des modifications devient nécessaire

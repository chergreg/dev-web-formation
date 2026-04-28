# dev-web-formation

## Présentation

Ce projet a pour objectif de m’aider à apprendre à développer une application Web en **PHP avec connexion à une base de données MySQL**.

L’application est construite **progressivement**, étape par étape, afin de comprendre la logique d’un projet Web dynamique et de bien assimiler les bases du développement côté serveur.

Ce travail est réalisé dans un environnement local avec :

- **XAMPP**
- **PHP**
- **MySQL**

Gestion de formations avec inscription (gratuite)

---

## Objectifs du projet

- apprendre à créer une application PHP avec connexion à une base de données
- comprendre la structure d’un projet Web simple
- mettre en place une authentification utilisateur
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

L’objectif n’est pas seulement d’obtenir du code fonctionnel, mais aussi de **comprendre les choix techniques** et de pouvoir les expliquer.

Je reste responsable du code intégré au projet.

---

## Fonctionnalités actuelles

L’application contient actuellement une première base d’authentification utilisateur.

### Pages disponibles

- `index.php` : page d’accueil
- `register.php` : page d’inscription
- `login.php` : page de connexion
- `profile.php` : page de profil utilisateur
- `logout.php` : page de déconnexion
- `view-admin.php` : gestion admin des formations et des inscriptions

### Sécurité actuellement en place

- redirection vers `login.php` si un utilisateur non connecté tente d’accéder à une page protégée
- connexion à la base de données via un fichier dédié
- séparation minimale de la logique d’authentification

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
├── view-admin.php
├── config/
│   └── db.php
├── includes/
│   └── auth.php
├── assets/
│   └── style.css
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
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

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

#### Rôle des principaux champs

- `titre` : nom de la formation affiché dans l’interface
- `description_courte` : résumé court utilisé dans les cartes ou les listes
- `description_longue` : description détaillée de la formation
- `audience` : public cible de la formation
- `youtube_url` : lien vers la vidéo ou le contenu YouTube
- `image_url` : image associée à la formation, optionnelle
- `type_contenu` : type de contenu, par exemple vidéo ou playlist
- `duree_minutes` : durée totale de la formation en minutes
- `nb_videos` : nombre de vidéos dans la formation
- `categories` : catégories associées à la formation (liste séparée par virgile)
- `statut` : état de la formation, par exemple `brouillon` ou `publie`
- `created_at` : date de création de la formation

Cette table remplace progressivement les données qui étaient auparavant stockées dans un fichier JSON. L’objectif est de rendre les formations dynamiques et administrables depuis la base de données.

---

### Éléments divers

- ajout d’une colonne `role` dans la table `users`
- gestion de deux rôles :
  - `user`
  - `admin`
- redirection après connexion selon le rôle
- création d’une page administrateur protégée
- vérification des droits côté serveur
- sécurisation des mots de passe avec hash
- validation serveur des formulaires
- amélioration de la gestion des sessions

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

### Administrateur

- possède le rôle `admin`
- peut accéder à une page d’administration réservée
- aura accès à des fonctionnalités supplémentaires de gestion

---

## Approche pédagogique

Ce projet me permet d’apprendre à :

- structurer une application PHP
- séparer la connexion BD, l’authentification et les pages
- comprendre la différence entre authentification et autorisation
- protéger des pages selon la session utilisateur
- mettre en place une base de sécurité correcte
- faire évoluer une application étape par étape

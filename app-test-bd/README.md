# Mon App PHP - Authentification avec MySQL

## Présentation

Ce projet a pour objectif de m’aider à apprendre à développer une application Web en **PHP avec connexion à une base de données MySQL**.

L’application est construite **progressivement**, étape par étape, afin de comprendre la logique d’un projet Web dynamique et de bien assimiler les bases du développement côté serveur.

Ce travail est réalisé dans un environnement local avec :

- **XAMPP**
- **PHP**
- **MySQL**

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
├── config/
│   └── db.php
├── includes/
│   └── auth.php
└── assets/
    └── style.css
```

---

## Base de données actuelle

Table principale utilisée :

```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

---

## Évolution prévue du projet

Le projet évolue maintenant vers une **authentification complète** avec gestion des rôles et amélioration de la sécurité.

### Éléments prévus

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

### Sécurité visée

- `password_hash()`
- `password_verify()`
- requêtes préparées avec PDO
- contrôle d’accès par session
- contrôle d’accès par rôle
- échappement des données affichées avec `htmlspecialchars()`

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

---

## Résumé

Ce projet est une application PHP/MySQL en cours d’évolution, développée dans un objectif d’apprentissage.

Il repose actuellement sur une base simple :

- inscription
- connexion
- profil
- déconnexion
- protection minimale des pages

Il évolue progressivement vers une version plus complète avec :

- gestion des rôles
- espace administrateur
- sécurité renforcée
- meilleures pratiques PHP/MySQL

<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

require_admin();

$user = current_user();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Espace administrateur</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <h1>Espace administrateur</h1>

    <p>Bienvenue, <?= e($user['nom']) ?>.</p>
    <p>Vous êtes connecté avec le rôle : <strong><?= e($user['role']) ?></strong></p>

    <ul>
        <li>Gérer les utilisateurs</li>
        <li>Gérer les formations</li>
        <li>Consulter les inscriptions</li>
    </ul>

    <p>
        <a href="profile.php">Profil</a> |
        <a href="logout.php">Déconnexion</a>
    </p>
</body>
</html>
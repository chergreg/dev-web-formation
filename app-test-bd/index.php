<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

$user = current_user();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Accueil</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <h1>Accueil</h1>
    <p>Bienvenue sur mon application PHP avec authentification.</p>

    <?php if ($user): ?>
        <p>Connecté en tant que <?= e($user['nom']) ?> (<?= e($user['role']) ?>)</p>
        <p>
            <a href="profile.php">Mon profil</a>
            <?php if (($user['role'] ?? '') === 'admin'): ?>
                | <a href="admin.php">Espace admin</a>
            <?php endif; ?>
            | <a href="logout.php">Déconnexion</a>
        </p>
    <?php else: ?>
        <p>
            <a href="login.php">Connexion</a> |
            <a href="register.php">Inscription</a>
        </p>
    <?php endif; ?>
</body>
</html>
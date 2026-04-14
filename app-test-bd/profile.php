<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

require_login();

$user = current_user();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon profil</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <h1>Mon profil</h1>

    <p><strong>Nom :</strong> <?= e($user['nom']) ?></p>
    <p><strong>Courriel :</strong> <?= e($user['email']) ?></p>
    <p><strong>Rôle :</strong> <?= e($user['role']) ?></p>

    <p>
        <a href="index.php">Accueil</a> |
        <?php if (($user['role'] ?? '') === 'admin'): ?>
            <a href="admin.php">Admin</a> |
        <?php endif; ?>
        <a href="logout.php">Déconnexion</a>
    </p>
</body>
</html>
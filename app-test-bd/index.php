<?php
session_start();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Accueil</title>
</head>
<body>
    <h1>Mon application PHP</h1>

    <?php if (isset($_SESSION['user_id'])): ?>
        <p>Bonjour <?= htmlspecialchars($_SESSION['user_nom']) ?></p>
        <p><a href="profile.php">Voir mon profil</a></p>
        <p><a href="logout.php">Se déconnecter</a></p>
    <?php else: ?>
        <p><a href="register.php">S'inscrire</a></p>
        <p><a href="login.php">Se connecter</a></p>
    <?php endif; ?>
</body>
</html>
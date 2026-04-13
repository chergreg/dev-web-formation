<?php
require_once 'includes/auth.php';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Profil</title>
</head>
<body>
    <h1>Mon profil</h1>

    <p>Bienvenue <?= htmlspecialchars($_SESSION['user_nom']) ?></p>
    <p>Email : <?= htmlspecialchars($_SESSION['user_email']) ?></p>

    <p><a href="logout.php">Se déconnecter</a></p>
</body>
</html>
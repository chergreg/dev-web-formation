<?php
require_once 'config/db.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $motDePasse = $_POST['mot_de_passe'] ?? '';

    if ($nom === '' || $email === '' || $motDePasse === '') {
        $message = "Tous les champs sont obligatoires.";
    } else {
        $hash = password_hash($motDePasse, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (nom, email, mot_de_passe) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);

        try {
            $stmt->execute([$nom, $email, $hash]);
            $message = "Inscription réussie. Tu peux maintenant te connecter.";
        } catch (PDOException $e) {
            $message = "Erreur : cet email existe déjà.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription</title>
</head>
<body>
    <h1>Créer un compte</h1>

    <p><?= htmlspecialchars($message) ?></p>

    <form method="post" action="">
        <label>Nom</label><br>
        <input type="text" name="nom"><br><br>

        <label>Email</label><br>
        <input type="email" name="email"><br><br>

        <label>Mot de passe</label><br>
        <input type="password" name="mot_de_passe"><br><br>

        <button type="submit">S'inscrire</button>
    </form>

    <p><a href="login.php">Déjà un compte ? Se connecter</a></p>
</body>
</html>
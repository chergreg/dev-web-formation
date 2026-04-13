<?php
session_start();
require_once 'config/db.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $motDePasse = $_POST['mot_de_passe'] ?? '';

    if ($email === '' || $motDePasse === '') {
        $message = "Tous les champs sont obligatoires.";
    } else {
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$email]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($motDePasse, $user['mot_de_passe'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_nom'] = $user['nom'];
            $_SESSION['user_email'] = $user['email'];

            header('Location: profile.php');
            exit;
        } else {
            $message = "Email ou mot de passe invalide.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
</head>
<body>
    <h1>Connexion</h1>

    <p><?= htmlspecialchars($message) ?></p>

    <form method="post" action="">
        <label>Email</label><br>
        <input type="email" name="email"><br><br>

        <label>Mot de passe</label><br>
        <input type="password" name="mot_de_passe"><br><br>

        <button type="submit">Se connecter</button>
    </form>

    <p><a href="register.php">Créer un compte</a></p>
</body>
</html>
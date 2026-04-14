<?php
declare(strict_types=1);

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

if (is_logged_in()) {
    $role = $_SESSION['user_role'] ?? 'user';
    redirect($role === 'admin' ? 'admin.php' : 'profile.php');
}

$errors = [];
$nom = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $motDePasse = $_POST['mot_de_passe'] ?? '';
    $confirmation = $_POST['confirmation_mot_de_passe'] ?? '';

    if ($nom === '') {
        $errors[] = 'Le nom est obligatoire.';
    }

    if ($email === '') {
        $errors[] = 'L’adresse courriel est obligatoire.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Le format du courriel est invalide.';
    }

    if (!is_valid_password($motDePasse)) {
        $errors[] = 'Le mot de passe doit contenir au moins 8 caractères.';
    }

    if ($motDePasse !== $confirmation) {
        $errors[] = 'La confirmation du mot de passe ne correspond pas.';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        $userExists = $stmt->fetch();

        if ($userExists) {
            $errors[] = 'Cette adresse courriel est déjà utilisée.';
        } else {
            $hash = password_hash($motDePasse, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare('
                INSERT INTO users (nom, email, mot_de_passe, role)
                VALUES (:nom, :email, :mot_de_passe, :role)
            ');

            $stmt->execute([
                'nom' => $nom,
                'email' => $email,
                'mot_de_passe' => $hash,
                'role' => 'user',
            ]);

            redirect('login.php');
        }
    }
}
?>
<!--
Register :
- on valide les champs
- on vérifie si l’email existe déjà
- on hash le mot de passe
- on crée le compte avec role = 'user'
-->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <h1>Créer un compte</h1>

    <?php if (!empty($errors)): ?>
        <div class="error-box">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= e($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" action="">
        <div>
            <label for="nom">Nom</label><br>
            <input type="text" id="nom" name="nom" value="<?= e($nom) ?>" required>
        </div>

        <div>
            <label for="email">Courriel</label><br>
            <input type="email" id="email" name="email" value="<?= e($email) ?>" required>
        </div>

        <div>
            <label for="mot_de_passe">Mot de passe</label><br>
            <input type="password" id="mot_de_passe" name="mot_de_passe" required>
        </div>

        <div>
            <label for="confirmation_mot_de_passe">Confirmer le mot de passe</label><br>
            <input type="password" id="confirmation_mot_de_passe" name="confirmation_mot_de_passe" required>
        </div>

        <button type="submit">S’inscrire</button>
    </form>

    <p><a href="login.php">Déjà un compte ? Se connecter</a></p>
</body>
</html>
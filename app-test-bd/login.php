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
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $motDePasse = $_POST['mot_de_passe'] ?? '';

    if ($email === '') {
        $errors[] = 'Le courriel est obligatoire.';
    }

    if ($motDePasse === '') {
        $errors[] = 'Le mot de passe est obligatoire.';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare('SELECT id, nom, email, mot_de_passe, role FROM users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($motDePasse, $user['mot_de_passe'])) {
            $errors[] = 'Identifiants invalides.';
        } else {
            session_regenerate_id(true);

            $_SESSION['user_id'] = (int)$user['id'];
            $_SESSION['user_name'] = $user['nom'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];

            if ($user['role'] === 'admin') {
                redirect('admin.php');
            }

            redirect('profile.php');
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <h1>Connexion</h1>

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
            <label for="email">Courriel</label><br>
            <input type="email" id="email" name="email" value="<?= e($email) ?>" required>
        </div>

        <div>
            <label for="mot_de_passe">Mot de passe</label><br>
            <input type="password" id="mot_de_passe" name="mot_de_passe" required>
        </div>

        <button type="submit">Se connecter</button>
    </form>

    <p><a href="register.php">Créer un compte</a></p>
</body>
</html>
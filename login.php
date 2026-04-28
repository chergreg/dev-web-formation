<?php
declare(strict_types=1);

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

if (is_logged_in()) {
    $role = $_SESSION['user_role'] ?? 'user';
    redirect($role === 'admin' ? 'view_admin.php' : 'profile.php');
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

            $_SESSION['user_id']    = (int)$user['id'];
            $_SESSION['user_name']  = $user['nom'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role']  = $user['role'];

            redirect($user['role'] === 'admin' ? 'view-admin.php' : 'profile.php');
        }
    }
}
?>
<!doctype html>
<html lang="fr">
<head>
  <?php include __DIR__ . '/partials/_head.php'; ?>
  <title>Web Formations — Connexion</title>
</head>

<body>
<?php include __DIR__ . '/partials/_navbar.php'; ?>

  <main class="container my-4 my-lg-5">

    <div class="row justify-content-center">
      <div class="col-12 col-sm-10 col-md-8 col-lg-5">

        <!-- En-tête -->
        <div class="text-center mb-4">
          <h1 class="h3 fw-semibold mb-1">Connexion</h1>
          <p class="muted">Content de te revoir. Connecte-toi pour accéder à tes formations.</p>
        </div>

        <!-- Carte formulaire -->
        <div class="card p-4 p-lg-5">

          <!-- Erreurs -->
          <?php if (!empty($errors)): ?>
            <div class="mb-4 p-3 rounded-3"
                 style="background: rgba(239,68,68,.10); border: 1px solid rgba(239,68,68,.30);">
              <div class="d-flex align-items-center gap-2 mb-2">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                     stroke="#ef4444" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <circle cx="12" cy="12" r="10"/>
                  <line x1="12" y1="8" x2="12" y2="12"/>
                  <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                <span class="fw-semibold" style="color:#ffd0d0; font-size:.9rem;">Erreur de connexion</span>
              </div>
              <ul class="mb-0 ps-3" style="color:#ffd0d0; font-size:.875rem;">
                <?php foreach ($errors as $error): ?>
                  <li><?= e($error) ?></li>
                <?php endforeach; ?>
              </ul>
            </div>
          <?php endif; ?>

          <!-- Formulaire -->
          <form method="post" action="" novalidate>

            <!-- Courriel -->
            <div class="mb-3">
              <label for="email" class="form-label fw-medium" style="font-size:.9rem;">
                Adresse courriel
              </label>
              <input
                type="email"
                id="email"
                name="email"
                class="form-control"
                value="<?= e($email) ?>"
                placeholder="exemple@courriel.com"
                required
                autocomplete="email"
              >
            </div>

            <!-- Mot de passe -->
            <div class="mb-4">
              <label for="mot_de_passe" class="form-label fw-medium" style="font-size:.9rem;">
                Mot de passe
              </label>
              <input
                type="password"
                id="mot_de_passe"
                name="mot_de_passe"
                class="form-control"
                placeholder="••••••••"
                required
                autocomplete="current-password"
              >
            </div>

            <!-- Bouton -->
            <button type="submit" class="btn btn-accent w-100 py-2 fw-semibold">
              Se connecter
            </button>

          </form>

          <!-- Séparateur -->
          <div class="divider my-4"></div>

          <!-- Lien inscription -->
          <p class="text-center mb-0 muted" style="font-size:.9rem;">
            Pas encore de compte ?
            <a href="register.php" class="text-decoration-none fw-semibold"
               style="color: var(--accent);">
              Créer un compte
            </a>
          </p>

        </div>
        <!-- /card -->

      </div>
    </div>

    <?php include __DIR__ . '/partials/_footer.php'; ?>
  </main>

<?php include __DIR__ . '/partials/_libjs.php'; ?>
<script>
  document.getElementById("year").textContent = new Date().getFullYear();
</script>
</body>
</html>

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
        $errors[] = 'L\'adresse courriel est obligatoire.';
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
                'nom'         => $nom,
                'email'       => $email,
                'mot_de_passe'=> $hash,
                'role'        => 'user',
            ]);

            redirect('login.php');
        }
    }
}
?>
<!doctype html>
<html lang="fr">
<head>
  <?php include __DIR__ . '/partials/_head.php'; ?>
  <title>Web Formations — Inscription</title>
</head>

<body>
<?php include __DIR__ . '/partials/_navbar.php'; ?>

  <main class="container my-4 my-lg-5">

    <div class="row justify-content-center">
      <div class="col-12 col-sm-10 col-md-8 col-lg-5">

        <!-- En-tête de page -->
        <div class="text-center mb-4">
          <h1 class="h3 fw-semibold mb-1">Créer un compte</h1>
          <p class="muted">Rejoins la plateforme et accède aux formations.</p>
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
                <span class="fw-semibold" style="color:#ffd0d0; font-size:.9rem;">
                  <?= count($errors) > 1 ? count($errors) . ' erreurs à corriger' : 'Une erreur à corriger' ?>
                </span>
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

            <!-- Nom -->
            <div class="mb-3">
              <label for="nom" class="form-label fw-medium" style="font-size:.9rem;">
                Nom complet
              </label>
              <input
                type="text"
                id="nom"
                name="nom"
                class="form-control"
                value="<?= e($nom) ?>"
                placeholder="ex : Marie Tremblay"
                required
                autocomplete="name"
              >
            </div>

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
            <div class="mb-3">
              <label for="mot_de_passe" class="form-label fw-medium" style="font-size:.9rem;">
                Mot de passe
              </label>
              <input
                type="password"
                id="mot_de_passe"
                name="mot_de_passe"
                class="form-control"
                placeholder="8 caractères minimum"
                required
                autocomplete="new-password"
              >
              <div class="form-text muted" style="font-size:.8rem; margin-top:.35rem;">
                Minimum 8 caractères.
              </div>
            </div>

            <!-- Confirmation -->
            <div class="mb-4">
              <label for="confirmation_mot_de_passe" class="form-label fw-medium" style="font-size:.9rem;">
                Confirmer le mot de passe
              </label>
              <input
                type="password"
                id="confirmation_mot_de_passe"
                name="confirmation_mot_de_passe"
                class="form-control"
                placeholder="Répète ton mot de passe"
                required
                autocomplete="new-password"
              >
            </div>

            <!-- Bouton -->
            <button type="submit" class="btn btn-accent w-100 py-2 fw-semibold">
              Créer mon compte
            </button>

          </form>

          <!-- Séparateur -->
          <div class="divider my-4"></div>

          <!-- Lien connexion -->
          <p class="text-center mb-0 muted" style="font-size:.9rem;">
            Déjà un compte ?
            <a href="login.php" class="text-decoration-none fw-semibold"
               style="color: var(--accent);">
              Se connecter
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

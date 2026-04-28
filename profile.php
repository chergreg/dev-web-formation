<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

require_login();

$user = current_user();
$isAdmin = ($user['role'] ?? '') === 'admin';
?>
<!doctype html>
<html lang="fr">
<head>
  <?php include __DIR__ . '/partials/_head.php'; ?>
  <title>Web Formations — Mon profil</title>
</head>

<body>
<?php include __DIR__ . '/partials/_navbar.php'; ?>

  <main class="container my-4 my-lg-5">

    <div class="row justify-content-center">
      <div class="col-12 col-md-10 col-lg-7">

        <!-- En-tête -->
        <div class="mb-4">
          <h1 class="h3 fw-semibold mb-1">Mon profil</h1>
          <p class="muted">Informations de ton compte.</p>
        </div>

        <!-- Carte profil -->
        <div class="card p-4 p-lg-5 mb-3">

          <!-- Avatar + nom -->
          <div class="d-flex align-items-center gap-3 mb-4">
            <div class="d-flex align-items-center justify-content-center flex-shrink-0"
                 style="width:52px; height:52px; border-radius:999px;
                        background: rgba(124,92,255,.18);
                        border: 1px solid rgba(124,92,255,.35);">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none"
                   stroke="var(--accent)" stroke-width="1.8"
                   stroke-linecap="round" stroke-linejoin="round">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                <circle cx="12" cy="7" r="4"/>
              </svg>
            </div>
            <div>
              <div class="fw-semibold" style="font-size:1.05rem;">
                <?= e($user['nom']) ?>
              </div>
              <div class="muted" style="font-size:.875rem;">
                <?= e($user['email']) ?>
              </div>
            </div>

            <!-- Badge rôle -->
            <?php if ($isAdmin): ?>
              <span class="ms-auto pill">
                <span class="dot" style="background: var(--warn); box-shadow: 0 0 0 .15rem rgba(245,158,11,.20);"></span>
                Administrateur
              </span>
            <?php else: ?>
              <span class="ms-auto pill">
                <span class="dot"></span>
                Utilisateur
              </span>
            <?php endif; ?>
          </div>

          <div class="divider mb-4"></div>

          <!-- Informations détaillées -->
          <div class="row g-3">
            <div class="col-12 col-sm-6">
              <div class="p-3 rounded-3" style="background: rgba(255,255,255,.03); border: 1px solid var(--border);">
                <div class="muted mb-1" style="font-size:.78rem; text-transform:uppercase; letter-spacing:.06em;">Nom</div>
                <div class="fw-medium"><?= e($user['nom']) ?></div>
              </div>
            </div>
            <div class="col-12 col-sm-6">
              <div class="p-3 rounded-3" style="background: rgba(255,255,255,.03); border: 1px solid var(--border);">
                <div class="muted mb-1" style="font-size:.78rem; text-transform:uppercase; letter-spacing:.06em;">Courriel</div>
                <div class="fw-medium"><?= e($user['email']) ?></div>
              </div>
            </div>
            <div class="col-12 col-sm-6">
              <div class="p-3 rounded-3" style="background: rgba(255,255,255,.03); border: 1px solid var(--border);">
                <div class="muted mb-1" style="font-size:.78rem; text-transform:uppercase; letter-spacing:.06em;">Rôle</div>
                <div class="fw-medium"><?= e($user['role']) ?></div>
              </div>
            </div>
          </div>

        </div>
        <!-- /card profil -->

        <!-- Actions -->
        <div class="d-flex flex-wrap gap-2">
          <a href="index.php" class="btn btn-ghost">
            ← Accueil
          </a>
          <?php if ($isAdmin): ?>
            <a href="admin.php" class="btn btn-ghost">
              ⚙️ Administration
            </a>
          <?php endif; ?>
          <a href="logout.php" class="btn btn-danger-soft ms-auto">
            Déconnexion
          </a>
        </div>

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

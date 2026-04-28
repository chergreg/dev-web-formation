<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/functions.php';

function is_logged_in(): bool
{
    return isset($_SESSION['user_id']);
}

function current_user(): ?array
{
    if (!is_logged_in()) {
        return null;
    }

    return [
        'id'    => $_SESSION['user_id']    ?? null,
        'nom'   => $_SESSION['user_name']  ?? null,
        'email' => $_SESSION['user_email'] ?? null,
        'role'  => $_SESSION['user_role']  ?? null,
    ];
}

function require_login(): void
{
    if (!is_logged_in()) {
        redirect('login.php');
    }
}

function require_admin(): void
{
    require_login();

    if (($_SESSION['user_role'] ?? '') !== 'admin') {
        http_response_code(403);
        ?>
<!doctype html>
<html lang="fr">
<head>
  <?php include __DIR__ . '/../partials/_head.php'; ?>
  <title>Web Formations — Accès refusé</title>
</head>
<body>
<?php include __DIR__ . '/../partials/_navbar.php'; ?>

  <main class="container my-4 my-lg-5">

    <div class="row justify-content-center">
      <div class="col-12 col-sm-10 col-md-8 col-lg-5 text-center">

        <!-- Icône -->
        <div class="d-inline-flex align-items-center justify-content-center mb-4"
             style="width:72px; height:72px; border-radius:999px;
                    background: rgba(239,68,68,.12);
                    border: 1px solid rgba(239,68,68,.30);">
          <svg width="30" height="30" viewBox="0 0 24 24" fill="none"
               stroke="#ef4444" stroke-width="1.8"
               stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
          </svg>
        </div>

        <!-- Titre + message -->
        <h1 class="h3 fw-semibold mb-2" style="color:#ffd0d0;">Accès refusé</h1>
        <p class="muted mb-4">
          Tu es connecté, mais tu n'as pas les droits nécessaires pour accéder à cette page.
          Cette section est réservée aux administrateurs.
        </p>

        <!-- Carte info -->
        <div class="card p-4 mb-4 text-start">
          <div class="d-flex align-items-center gap-2 mb-3">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none"
                 stroke="var(--muted)" stroke-width="2"
                 stroke-linecap="round" stroke-linejoin="round">
              <circle cx="12" cy="12" r="10"/>
              <line x1="12" y1="8" x2="12" y2="12"/>
              <line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            <span class="muted" style="font-size:.85rem;">Pourquoi ce message ?</span>
          </div>
          <p class="muted mb-0" style="font-size:.875rem;">
            Ton compte possède le rôle
            <span class="fw-semibold" style="color: var(--text);">
              <?= htmlspecialchars($_SESSION['user_role'] ?? 'user', ENT_QUOTES, 'UTF-8') ?>
            </span>.
            Seuls les comptes avec le rôle
            <span class="fw-semibold" style="color: var(--accent);">admin</span>
            peuvent accéder à cette section.
          </p>
        </div>

        <!-- Actions -->
        <div class="d-flex justify-content-center gap-2 flex-wrap">
          <a href="index.php" class="btn btn-ghost">← Accueil</a>
          <a href="profile.php" class="btn btn-accent">Mon profil</a>
        </div>

      </div>
    </div>

    <?php include __DIR__ . '/../partials/_footer.php'; ?>
  </main>

<?php include __DIR__ . '/../partials/_libjs.php'; ?>
<script>
  document.getElementById("year").textContent = new Date().getFullYear();
</script>
</body>
</html>
        <?php
        exit;
    }
}

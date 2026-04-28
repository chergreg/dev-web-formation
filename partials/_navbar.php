<?php
/**
 * _navbar.php — Barre de navigation dynamique
 *
 * Ce fichier est inclus sur toutes les pages via :
 *   <?php include __DIR__ . '/partials/_navbar.php'; ?>
 *
 * Il adapte automatiquement les liens affichés selon l'état
 * de la session de l'utilisateur (3 cas possibles) :
 *
 *   1. Visiteur non connecté  → Connexion + Créer un compte
 *   2. Utilisateur connecté   → Profil + Déconnexion
 *   3. Administrateur         → Profil + Admin + Déconnexion
 */

// --- Sécurité : démarrer la session seulement si elle n'est pas déjà active ---
// Évite l'erreur "session already started" si auth.php l'a déjà démarrée.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// --- Lecture des données de session ---
// isset() vérifie que la clé existe ET n'est pas null.
// C'est suffisant pour savoir si quelqu'un est connecté.
$isLoggedIn = isset($_SESSION['user_id']);

// IMPORTANT : on lit bien $_SESSION (avec le $_ ), pas $SESSION.
// $SESSION serait une variable ordinaire non définie → toujours vide.
$isAdmin  = ($_SESSION['user_role'] ?? '') === 'admin';

// ?? est l'opérateur "null coalescing" : si la clé n'existe pas, retourne ''.
$userName = $_SESSION['user_name'] ?? '';
?>
<nav class="navbar navbar-expand-lg sticky-top">
  <div class="container py-2">

    <!-- ===================== LOGO =====================
         Le badge "Admin" n'apparaît que si l'utilisateur
         a le rôle admin, pour un repère visuel rapide.
    ===================================================== -->
    <a class="navbar-brand fw-semibold text-white" href="index.php">
      Web Formations
      <?php if ($isAdmin): ?>
        <span class="badge rounded-pill badge-soft ms-2 align-middle"
              style="font-size:.7rem;">Admin</span>
      <?php endif; ?>
    </a>

    <!-- Bouton hamburger pour mobile (Bootstrap) -->
    <button class="navbar-toggler btn-ghost" type="button"
            data-bs-toggle="collapse" data-bs-target="#nav"
            aria-controls="nav" aria-expanded="false" aria-label="Menu">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- ===================== LIENS =====================
         ms-auto pousse les liens vers la droite.
         align-items-lg-center aligne verticalement sur desktop.
    ===================================================== -->
    <div class="collapse navbar-collapse" id="nav">
      <ul class="navbar-nav ms-auto gap-2 align-items-lg-center">

        <?php if ($isLoggedIn): ?>

          <!-- CAS 2 & 3 : Utilisateur connecté -->

          <?php if ($isAdmin): ?>
            <!-- CAS 3 uniquement : lien vers la page d'administration.
                 Cette condition est vérifiée côté PHP (session),
                 mais la page view-admin.php doit AUSSI vérifier
                 le rôle elle-même (require_admin()) pour être
                 réellement protégée. La navbar seule ne suffit pas. -->
            <li class="nav-item">
              <a class="btn btn-ghost btn-sm" href="view-admin.php">⚙️ Administration</a>
            </li>
          <?php endif; ?>

          <!-- Lien profil avec le prénom de l'utilisateur.
               htmlspecialchars() évite les injections XSS si le nom
               contient des caractères spéciaux comme < > " -->
          <li class="nav-item">
            <a class="btn btn-ghost btn-sm" href="profile.php">
              👤 <?= htmlspecialchars($userName, ENT_QUOTES, 'UTF-8') ?>
            </a>
          </li>

          <!-- Déconnexion en rouge pour attirer l'œil et
               distinguer cette action des autres. -->
          <li class="nav-item">
            <a class="btn btn-danger-soft btn-sm" href="logout.php">Déconnexion</a>
          </li>

        <?php else: ?>

          <!-- CAS 1 : Visiteur non connecté -->

          <!-- Pill décorative (aucune action) -->
          <li class="nav-item">
            <span class="pill"><span class="dot"></span> 100% gratuit</span>
          </li>

          <!-- Connexion (discret) -->
          <li class="nav-item">
            <a class="btn btn-ghost btn-sm" href="login.php">Connexion</a>
          </li>

          <!-- Inscription mis en valeur avec la couleur accent (violet).
               Objectif : inciter les visiteurs à créer un compte. -->
          <li class="nav-item">
            <a class="btn btn-accent btn-sm" href="register.php">Créer un compte</a>
          </li>

        <?php endif; ?>

      </ul>
    </div>

  </div>
</nav>

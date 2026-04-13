<?php
declare(strict_types=1);

// 1) Charger le repository
require_once __DIR__ . '/src/FormationRepository.php';

// 2) Chemin du fichier JSON
$repo = new FormationRepository(__DIR__ . '/data/formations.json');

// 3) Récupération des formations
$formations = $repo->all();

// Optionnel: afficher uniquement les formations actives
// $formations = array_values(array_filter($formations, fn($f) => ($f['statut'] ?? 'active') === 'active'));
?>
<!doctype html>
<html lang="fr">
<head>
  <?php include __DIR__ . '/partials/_head.php'; ?>
  <title>Web Formations — Accueil</title>
</head>

<body>
<?php include __DIR__ . '/partials/_navbar.php'; ?>

  <main class="container my-4 my-lg-5">
    <!-- HERO -->
    <section class="hero p-4 p-lg-5 mb-4">
      <div class="row align-items-center g-4">
        <div class="col-lg-7">
          <h1 class="display-6 fw-semibold mb-2">Plateforme de formations en ligne pour solopreneurs</h1>
          <p class="muted mb-3">
            Découvre des formations courtes et actionnables, avec un format “vignette YouTube”.
            Inscris-toi en 1 clic (simulation — version statique).
          </p>          
        </div>

        <div class="col-lg-5">
          <div class="p-3 rounded-4 border" style="border-color: var(--border); background: rgba(0,0,0,.18);">
            <div class="d-flex align-items-center justify-content-between">
              <div>
                <div class="fw-semibold">À quoi ça sert ?</div>
                <div class="muted small">Consulter des formations + inscription</div>
              </div>
              <span class="badge rounded-pill text-bg-success">v1 statique</span>
            </div>
            <div class="divider my-3"></div>
            <div class="row g-2 small muted">
              <div class="col-6">✅ Cartes Bootstrap</div>
              <div class="col-6">✅ Responsive</div>
              <div class="col-6">✅ Navigation claire</div>
              <div class="col-6">✅ Données fictives</div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- FORMATIONS -->
    <section>
      <div class="d-flex align-items-end justify-content-between flex-wrap gap-2 mb-3">
        <div>
          <h2 class="h4 mb-1">Formations disponibles</h2>
          <p class="muted mb-0">6 formations (titre, description, vignette style YouTube, bouton d’inscription).</p>
        </div>
        <span class="muted small">Astuce : clique “Inscription” pour aller vers la page utilisateur.</span>
      </div>

      <div class="row g-3 g-lg-4">



<?php foreach ($formations as $f): ?>
  <?php
    $id     = (int)($f['id'] ?? 0);
    $titre  = htmlspecialchars((string)($f['titre'] ?? 'Sans titre'));
    $desc   = htmlspecialchars((string)($f['description'] ?? ''));
    $duree  = htmlspecialchars((string)($f['duree'] ?? ''));
    $prix   = (float)($f['prix'] ?? 0);
    $youtube = trim((string)($f['youtube_url'] ?? ''));
    $statut = (string)($f['statut'] ?? 'active');

    // Format prix
    $prixTxt = number_format($prix, 2, ',', ' ') . ' $';

    // Filtrer formations inactives (optionnel)
    if ($statut !== 'active') {
      continue;
    }

    // --- Thumbnail YouTube (simple) ---
    $thumbUrl = null;
    if ($youtube !== '' && filter_var($youtube, FILTER_VALIDATE_URL)) {
      $videoId = null;

      // youtu.be/VIDEOID
      if (preg_match('~youtu\.be/([a-zA-Z0-9_-]{6,})~', $youtube, $m)) {
        $videoId = $m[1];
      }
      // youtube.com/watch?v=VIDEOID
      if (!$videoId && preg_match('~v=([a-zA-Z0-9_-]{6,})~', $youtube, $m)) {
        $videoId = $m[1];
      }
      // youtube.com/embed/VIDEOID
      if (!$videoId && preg_match('~/embed/([a-zA-Z0-9_-]{6,})~', $youtube, $m)) {
        $videoId = $m[1];
      }

      if ($videoId) {
        $thumbUrl = "https://img.youtube.com/vi/" . $videoId . "/hqdefault.jpg";
      }
    }

    // Fallback description courte
    $descTxt = $desc !== '' ? $desc : 'Description à venir…';
  ?>

  <div class="col-12 col-md-6 col-lg-4">
    <article class="card h-100">
      <div class="card-body">

        <div class="yt-thumb mb-3" style="position:relative; border-radius:12px; overflow:hidden;">
          <?php if ($thumbUrl): ?>
            <img
              src="<?= htmlspecialchars($thumbUrl) ?>"
              alt="Aperçu vidéo YouTube"
              style="width:100%; height:170px; object-fit:cover; display:block;"
              loading="lazy"
            >
            <a href="<?= htmlspecialchars($youtube) ?>" target="_blank" rel="noopener"
               aria-label="Ouvrir l’aperçu YouTube"
               style="position:absolute; inset:0; display:block;">
              <span class="yt-play" aria-hidden="true"
                    style="position:absolute; inset:0; display:grid; place-items:center;">
                <span style="width:54px; height:54px; border-radius:999px; background:rgba(0,0,0,.55);
                             display:grid; place-items:center;">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="white" xmlns="http://www.w3.org/2000/svg">
                    <path d="M8 5v14l11-7L8 5z"/>
                  </svg>
                </span>
              </span>
            </a>
          <?php else: ?>
            <div style="width:100%; height:170px; background:#e9ecef; display:grid; place-items:center;">
              <div class="text-muted" style="font-size:14px;">Aperçu vidéo indisponible</div>
            </div>
          <?php endif; ?>
        </div>

        <h3 class="card-title h5 mb-2"><?= $titre ?></h3>

        <p class="muted mb-2"><?= $descTxt ?></p>

        <div class="d-flex flex-wrap gap-2 mb-3">
          <?php if ($duree !== ''): ?>
            <span class="badge text-bg-light">⏱️ <?= $duree ?></span>
          <?php endif; ?>
          <span class="badge text-bg-light">💲 <?= htmlspecialchars($prixTxt) ?></span>
        </div>

        <div class="d-flex gap-2">
          <a class="btn btn-accent w-100" href="inscription.php?formation_id=<?= $id ?>">
            Inscription
          </a>

          <?php if ($thumbUrl && $youtube !== ''): ?>
            <a class="btn btn-ghost" href="<?= htmlspecialchars($youtube) ?>" target="_blank" rel="noopener">
              Aperçu
            </a>
          <?php else: ?>
            <a class="btn btn-ghost disabled" href="#" tabindex="-1" aria-disabled="true">
              Aperçu
            </a>
          <?php endif; ?>
        </div>

      </div>
    </article>
  </div>

<?php endforeach; ?>









      </div>
    </section>

    <!-- FOOTER -->
    <?php include __DIR__ . '/partials/_footer.php'; ?>
  </main>
<?php include __DIR__ . '/partials/_libjs.php'; ?>
  
  <script>
    document.getElementById("year").textContent = new Date().getFullYear();
  </script>
</body>
</html>

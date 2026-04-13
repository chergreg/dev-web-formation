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
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Demo Formations</title>
  <style>
    body { font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; margin: 0; background:#f6f7fb; }
    .container { max-width: 1100px; margin: 0 auto; padding: 24px; }
    h1 { margin: 0 0 16px; }
    .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 16px; }
    .card { background: #fff; border-radius: 14px; padding: 16px; box-shadow: 0 6px 18px rgba(0,0,0,.06); border: 1px solid rgba(0,0,0,.06); }
    .badge { display:inline-block; font-size: 12px; padding: 4px 10px; border-radius: 999px; background:#eef2ff; color:#3730a3; }
    .title { font-size: 18px; font-weight: 700; margin: 10px 0 6px; }
    .desc { color: #444; line-height: 1.45; margin: 0 0 12px; }
    .meta { display:flex; gap: 10px; flex-wrap: wrap; margin-top: 10px; color:#222; }
    .meta span { background:#f3f4f6; padding: 6px 10px; border-radius: 10px; font-size: 13px; }
    .actions { margin-top: 12px; display:flex; gap: 10px; }
    .btn { display:inline-block; text-decoration:none; padding: 10px 12px; border-radius: 10px; font-weight: 600; font-size: 14px; }
    .btn-primary { background:#111827; color:#fff; }
    .btn-secondary { background:#e5e7eb; color:#111827; }
    .empty { padding: 14px 16px; background:#fff; border-radius: 14px; border: 1px dashed rgba(0,0,0,.25); }
    .small { color:#6b7280; font-size: 14px; }
  </style>
</head>
<body>
  <div class="container">
    <h1>Formations</h1>
    <p class="small">Total: <?= count($formations) ?></p>

    <?php if (count($formations) === 0): ?>
      <div class="empty">
        Aucune formation trouvée. Vérifie <code>data/formations.json</code>.
      </div>
    <?php else: ?>
      <div class="grid">
        <?php foreach ($formations as $f): ?>
          <?php
            $id = (int)($f['id'] ?? 0);
            $titre = htmlspecialchars((string)($f['titre'] ?? 'Sans titre'));
            $desc = htmlspecialchars((string)($f['description'] ?? ''));
            $duree = htmlspecialchars((string)($f['duree'] ?? ''));
            $prix = (float)($f['prix'] ?? 0);
            $youtube = trim((string)($f['youtube_url'] ?? ''));
            $statut = htmlspecialchars((string)($f['statut'] ?? 'active'));

            // Format prix (fr_CA ou fr_FR, simple)
            $prixTxt = number_format($prix, 2, ',', ' ') . ' $';
          ?>
          <article class="card">
            <span class="badge">#<?= $id ?> • <?= $statut ?></span>
            <div class="title"><?= $titre ?></div>
            <p class="desc"><?= $desc ?></p>

            <div class="meta">
              <?php if ($duree !== ''): ?><span>⏱️ <?= $duree ?></span><?php endif; ?>
              <span>💲 <?= $prixTxt ?></span>
              <?php if (!empty($f['categorie'])): ?>
                <span>🏷️ <?= htmlspecialchars((string)$f['categorie']) ?></span>
              <?php endif; ?>
            </div>

            <div class="actions">
              <?php if ($youtube !== '' && filter_var($youtube, FILTER_VALIDATE_URL)): ?>
                <a class="btn btn-secondary" href="<?= htmlspecialchars($youtube) ?>" target="_blank" rel="noopener">
                  Voir sur YouTube
                </a>
              <?php endif; ?>

              <!-- Exemple de lien vers une page détail ou inscription -->
              <a class="btn btn-primary" href="formation.php?id=<?= $id ?>">
                Détails
              </a>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</body>
</html>
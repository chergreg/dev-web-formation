<?php

declare(strict_types=1);

require_once __DIR__ . '/src/FormationRepository.php';

$formations = [];
$loadError = null;

try {
    require_once __DIR__ . '/config/db.php';

    if (isset($pdo) && $pdo instanceof PDO) {
        $database = $pdo;
    } elseif (isset($db) && $db instanceof PDO) {
        $database = $db;
    } elseif (isset($connexion) && $connexion instanceof PDO) {
        $database = $connexion;
    } else {
        throw new RuntimeException("Aucune connexion PDO disponible. Vérifie que config/db.php crée une variable $pdo.");
    }

    $formationRepository = new FormationRepository($database);
    $formations = $formationRepository->allPublished();
} catch (Throwable $exception) {
    $loadError = $exception->getMessage();
}

function e(?string $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function splitCategories(?string $categories): array
{
    if ($categories === null || trim($categories) === '') {
        return [];
    }

    $items = array_map('trim', explode(',', $categories));

    return array_values(array_filter($items, fn(string $item): bool => $item !== ''));
}

function slugify(string $value): string
{
    $value = trim($value);
    $value = strtr($value, [
        'à' => 'a', 'â' => 'a', 'ä' => 'a', 'á' => 'a', 'ã' => 'a', 'å' => 'a',
        'ç' => 'c',
        'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e',
        'î' => 'i', 'ï' => 'i', 'í' => 'i', 'ì' => 'i',
        'ô' => 'o', 'ö' => 'o', 'ó' => 'o', 'ò' => 'o', 'õ' => 'o',
        'ù' => 'u', 'û' => 'u', 'ü' => 'u', 'ú' => 'u',
        'ÿ' => 'y', 'ñ' => 'n',
        'À' => 'a', 'Â' => 'a', 'Ä' => 'a', 'Á' => 'a', 'Ã' => 'a', 'Å' => 'a',
        'Ç' => 'c',
        'É' => 'e', 'È' => 'e', 'Ê' => 'e', 'Ë' => 'e',
        'Î' => 'i', 'Ï' => 'i', 'Í' => 'i', 'Ì' => 'i',
        'Ô' => 'o', 'Ö' => 'o', 'Ó' => 'o', 'Ò' => 'o', 'Õ' => 'o',
        'Ù' => 'u', 'Û' => 'u', 'Ü' => 'u', 'Ú' => 'u',
        'Ÿ' => 'y', 'Ñ' => 'n',
    ]);
    $value = strtolower($value);
    $value = preg_replace('/[^a-z0-9]+/', '-', $value) ?? '';
    $value = trim($value, '-');

    return $value !== '' ? $value : 'autre';
}

function typeContentLabel(?string $type): string
{
    return match ($type) {
        'playlist' => 'Playlist',
        'video' => 'Vidéo',
        default => 'Contenu',
    };
}

function formatDurationYoutube(int $durationMinutes): string
{
    $totalSeconds = max(0, $durationMinutes) * 60;
    $hours = intdiv($totalSeconds, 3600);
    $minutes = intdiv($totalSeconds % 3600, 60);
    $seconds = $totalSeconds % 60;

    if ($hours > 0) {
        return sprintf('%d:%02d:%02d', $hours, $minutes, $seconds);
    }

    return sprintf('%d:%02d', $minutes, $seconds);
}

function formationInitials(string $title): string
{
    $words = preg_split('/\s+/', trim($title)) ?: [];
    $letters = '';

    foreach ($words as $word) {
        $cleanWord = preg_replace('/[^A-Za-zÀ-ÿ0-9]/u', '', $word) ?? '';
        if ($cleanWord !== '') {
            $letters .= strtoupper(substr($cleanWord, 0, 1));
        }

        if (strlen($letters) >= 2) {
            break;
        }
    }

    return $letters !== '' ? $letters : 'F';
}

$categoryFilters = [];
$typeFilters = [];

foreach ($formations as $formation) {
    foreach (splitCategories($formation['categories'] ?? null) as $category) {
        $categoryFilters[slugify($category)] = $category;
    }

    $type = (string)($formation['type_contenu'] ?? '');
    if ($type !== '') {
        $typeFilters[$type] = typeContentLabel($type);
    }
}
?>
<!doctype html>
<html lang="fr">
<head>
  <?php include __DIR__ . '/partials/_head.php'; ?>
  <title>Web Formations - Accueil</title>

  <style>
    .filter-panel {
      border: 1px solid var(--border);
      background: rgba(255, 255, 255, .04);
      border-radius: 1.25rem;
      padding: 1rem;
    }

    .filter-chip {
      border: 1px solid var(--border);
      background: rgba(255, 255, 255, .06);
      color: inherit;
      border-radius: 999px;
      padding: .45rem .8rem;
      font-size: .9rem;
      line-height: 1;
      transition: transform .15s ease, background-color .15s ease, border-color .15s ease;
    }

    .filter-chip:hover {
      transform: translateY(-1px);
      background: rgba(255, 255, 255, .12);
    }

    .filter-chip.active {
      background: var(--accent);
      border-color: var(--accent);
      color: #fff;
    }

    .formation-thumb {
      position: relative;
      min-height: 190px;
      aspect-ratio: 16 / 9;
      display: flex;
      align-items: center;
      justify-content: center;
      border: 1px solid var(--border);
      background:
        radial-gradient(circle at top left, rgba(255, 255, 255, .18), transparent 35%),
        linear-gradient(135deg, rgba(255, 255, 255, .08), rgba(255, 255, 255, .02));
    }

    .formation-thumb img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .formation-thumb::after {
      content: "";
      position: absolute;
      inset: 0;
      background: linear-gradient(to bottom, rgba(0,0,0,.32), transparent 38%, rgba(0,0,0,.38));
      pointer-events: none;
    }

    .formation-thumb-placeholder {
      position: relative;
      z-index: 1;
    }

    .formation-thumb-label {
      color: rgba(255, 255, 255, .85);
      letter-spacing: .04em;
    }

    .formation-category-badges {
      position: absolute;
      top: .75rem;
      left: .75rem;
      display: flex;
      flex-wrap: wrap;
      gap: .4rem;
      max-width: calc(100% - 1.5rem);
      z-index: 2;
    }

    .formation-meta-badges {
      position: absolute;
      right: .75rem;
      bottom: .75rem;
      display: flex;
      align-items: center;
      justify-content: flex-end;
      flex-wrap: wrap;
      gap: .4rem;
      z-index: 2;
    }

    .formation-overlay-badge {
      border: 1px solid rgba(255, 255, 255, .35);
      background: rgba(0, 0, 0, .55);
      color: #fff;
      backdrop-filter: blur(8px);
    }

    .formation-duration-badge {
      border-color: rgba(255, 255, 255, .55);
      background: rgba(0, 0, 0, .78);
      font-family: var(--bs-font-monospace);
      letter-spacing: .02em;
    }

    .formation-card-wrapper.is-hidden {
      display: none;
    }

    .formation-card-wrapper {
      transition: opacity .15s ease, transform .15s ease;
    }

    .modal-content {
      border-radius: 1.25rem;
    }
  </style>
</head>

<body>
<?php include __DIR__ . '/partials/_navbar.php'; ?>

  <main class="container my-4 my-lg-5">
    <section class="hero p-4 p-lg-5 mb-4">
      <div class="row align-items-center g-4">
        <div class="col-lg-7">
          <h1 class="display-6 fw-semibold mb-2">Plateforme de formations en ligne pour solopreneurs</h1>
          <p class="muted mb-3">
            Découvre des formations courtes et actionnables, avec un format simple et visuel.
            Les formations publiées sont maintenant récupérées depuis la base de données.
          </p>
        </div>

        <div class="col-lg-5">
          <div class="p-3 rounded-4 border" style="border-color: var(--border); background: rgba(0,0,0,.18);">
            <div class="d-flex align-items-center justify-content-between gap-3">
              <div>
                <div class="fw-semibold">Version connectée</div>
                <div class="muted small">Affichage dynamique depuis MySQL</div>
              </div>
              <span class="badge rounded-pill text-bg-success">BD</span>
            </div>
            <div class="divider my-3"></div>
            <div class="row g-2 small muted">
              <div class="col-6">Cartes Bootstrap</div>
              <div class="col-6">Popup détail</div>
              <div class="col-6">Filtres JS</div>
              <div class="col-6">PDO + Repository</div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section>
      <div class="d-flex align-items-end justify-content-between flex-wrap gap-2 mb-3">
        <div>
          <h2 class="h4 mb-1">Formations disponibles</h2>
          <p class="muted mb-0">
            Les formations affichées ici proviennent de la table <code>formations</code> et ont le statut <code>publie</code>.
          </p>
        </div>
        <span class="muted small"><?= count($formations); ?> formation<?= count($formations) > 1 ? 's' : ''; ?> publiée<?= count($formations) > 1 ? 's' : ''; ?></span>
      </div>

      <?php if ($loadError !== null): ?>
        <div class="alert alert-danger" role="alert">
          <strong>Erreur de chargement des formations.</strong><br>
          <?= e($loadError); ?>
        </div>
      <?php endif; ?>

      <?php if ($loadError === null && count($formations) === 0): ?>
        <div class="alert alert-info" role="status">
          Aucune formation publiée pour le moment.
        </div>
      <?php endif; ?>

      <?php if (count($formations) > 0): ?>
        <div class="filter-panel mb-4">
          <div class="row g-3 align-items-end">
            <div class="col-lg-8">
              <div class="small fw-semibold mb-2">Filtrer par catégorie</div>
              <div class="d-flex flex-wrap gap-2" data-filter-group="category" aria-label="Filtres par catégorie">
                <button class="filter-chip active" type="button" data-filter-value="all">Toutes</button>
                <?php foreach ($categoryFilters as $categorySlug => $categoryLabel): ?>
                  <button class="filter-chip" type="button" data-filter-value="<?= e($categorySlug); ?>"><?= e($categoryLabel); ?></button>
                <?php endforeach; ?>
              </div>
            </div>

            <div class="col-lg-4">
              <div class="small fw-semibold mb-2">Filtrer par type de contenu</div>
              <div class="d-flex flex-wrap gap-2" data-filter-group="type" aria-label="Filtres par type de contenu">
                <button class="filter-chip active" type="button" data-filter-value="all">Tous</button>
                <?php foreach ($typeFilters as $typeValue => $typeLabel): ?>
                  <button class="filter-chip" type="button" data-filter-value="<?= e($typeValue); ?>"><?= e($typeLabel); ?></button>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
        </div>

        <div class="row g-3 g-lg-4" id="formationsGrid">
          <?php foreach ($formations as $formation): ?>
            <?php
              $id = (int)($formation['id'] ?? 0);
              $title = (string)($formation['titre'] ?? 'Formation');
              $shortDescription = (string)($formation['description_courte'] ?? '');
              $longDescription = (string)($formation['description_longue'] ?? '');
              $audience = (string)($formation['audience'] ?? '');
              $durationMinutes = (int)($formation['duree_minutes'] ?? 0);
              $typeContent = (string)($formation['type_contenu'] ?? 'video');
              $imageUrl = trim((string)($formation['image_url'] ?? ''));
              $categories = splitCategories($formation['categories'] ?? null);
              $categorySlugs = array_map('slugify', $categories);
              $dataCategories = implode(',', $categorySlugs);
              $modalId = 'formationModal' . $id;
            ?>
            <div class="col-12 col-md-6 col-lg-4 formation-card-wrapper" data-categories="<?= e($dataCategories); ?>" data-type="<?= e($typeContent); ?>">
              <article class="card h-100">
                <div class="card-body d-flex flex-column">
                  <div class="rounded-4 overflow-hidden mb-3 formation-thumb">
                    <?php if (count($categories) > 0): ?>
                      <div class="formation-category-badges" aria-label="Catégories">
                        <?php foreach ($categories as $category): ?>
                          <span class="badge rounded-pill formation-overlay-badge"><?= e($category); ?></span>
                        <?php endforeach; ?>
                      </div>
                    <?php endif; ?>

                    <div class="formation-meta-badges">
                      <span class="badge rounded-pill formation-overlay-badge"><?= e(typeContentLabel($typeContent)); ?></span>
                      <span class="badge rounded-pill formation-overlay-badge formation-duration-badge"><?= e(formatDurationYoutube($durationMinutes)); ?></span>
                    </div>

                    <?php if ($imageUrl !== ''): ?>
                      <img src="<?= e($imageUrl); ?>" alt="Aperçu de la formation <?= e($title); ?>">
                    <?php else: ?>
                      <div class="formation-thumb-placeholder d-flex align-items-center justify-content-center text-center p-3">
                        <div>
                          <div class="display-6 fw-semibold mb-1"><?= e(formationInitials($title)); ?></div>
                          <div class="small text-uppercase formation-thumb-label">Aperçu formation</div>
                        </div>
                      </div>
                    <?php endif; ?>
                  </div>

                  <h3 class="card-title h5 mb-2"><?= e($title); ?></h3>
                  <p class="muted mb-3"><?= e($shortDescription); ?></p>

                  <div class="d-flex gap-2 mt-auto">
                    <button class="btn btn-ghost w-100" type="button" data-bs-toggle="modal" data-bs-target="#<?= e($modalId); ?>">
                      Voir la formation
                    </button>
                    <a class="btn btn-accent w-100" href="view-user.php?formation_id=<?= $id; ?>">
                      Inscription
                    </a>
                  </div>
                </div>
              </article>
            </div>
          <?php endforeach; ?>
        </div>

        <div class="alert alert-info mt-4 d-none" id="noResultsMessage" role="status">
          Aucune formation ne correspond aux filtres sélectionnés.
        </div>
      <?php endif; ?>
    </section>

    <?php foreach ($formations as $formation): ?>
      <?php
        $id = (int)($formation['id'] ?? 0);
        $title = (string)($formation['titre'] ?? 'Formation');
        $longDescription = (string)($formation['description_longue'] ?? '');
        $audience = (string)($formation['audience'] ?? '');
        $durationMinutes = (int)($formation['duree_minutes'] ?? 0);
        $typeContent = (string)($formation['type_contenu'] ?? 'video');
        $categories = splitCategories($formation['categories'] ?? null);
        $modalId = 'formationModal' . $id;
      ?>
      <div class="modal fade" id="<?= e($modalId); ?>" tabindex="-1" aria-labelledby="<?= e($modalId); ?>Label" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h2 class="modal-title h5" id="<?= e($modalId); ?>Label"><?= e($title); ?></h2>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body">
              <div class="d-flex flex-wrap gap-2 mb-3">
                <?php foreach ($categories as $category): ?>
                  <span class="badge text-bg-light"><?= e($category); ?></span>
                <?php endforeach; ?>
                <span class="badge text-bg-light"><?= e(typeContentLabel($typeContent)); ?></span>
                <span class="badge text-bg-light"><?= e(formatDurationYoutube($durationMinutes)); ?></span>
              </div>

              <p><?= nl2br(e($longDescription)); ?></p>

              <?php if ($audience !== ''): ?>
                <p class="mb-0 muted">
                  Public cible : <?= e($audience); ?>
                </p>
              <?php endif; ?>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-ghost" data-bs-dismiss="modal">Fermer</button>
              <a class="btn btn-accent" href="view-user.php?formation_id=<?= $id; ?>">Inscription</a>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>

    <?php include __DIR__ . '/partials/_footer.php'; ?>
  </main>

<?php include __DIR__ . '/partials/_libjs.php'; ?>

  <script>
    const yearElement = document.getElementById("year");

    if (yearElement) {
      yearElement.textContent = new Date().getFullYear();
    }

    const filterState = {
      category: "all",
      type: "all"
    };

    const cards = document.querySelectorAll(".formation-card-wrapper");
    const noResultsMessage = document.getElementById("noResultsMessage");

    function updateActiveButton(group, selectedValue) {
      const buttons = document.querySelectorAll(`[data-filter-group="${group}"] .filter-chip`);

      buttons.forEach((button) => {
        button.classList.toggle("active", button.dataset.filterValue === selectedValue);
      });
    }

    function applyFilters() {
      let visibleCount = 0;

      cards.forEach((card) => {
        const categories = card.dataset.categories === "" ? [] : card.dataset.categories.split(",");
        const type = card.dataset.type;

        const categoryMatches = filterState.category === "all" || categories.includes(filterState.category);
        const typeMatches = filterState.type === "all" || type === filterState.type;
        const isVisible = categoryMatches && typeMatches;

        card.classList.toggle("is-hidden", !isVisible);

        if (isVisible) {
          visibleCount++;
        }
      });

      if (noResultsMessage) {
        noResultsMessage.classList.toggle("d-none", visibleCount > 0);
      }
    }

    document.querySelectorAll(".filter-chip").forEach((button) => {
      button.addEventListener("click", () => {
        const group = button.closest("[data-filter-group]").dataset.filterGroup;
        const selectedValue = button.dataset.filterValue;

        filterState[group] = selectedValue;
        updateActiveButton(group, selectedValue);
        applyFilters();
      });
    });
  </script>
</body>
</html>

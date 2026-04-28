<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/src/FormationRepository.php';

require_admin();

$user = current_user();
$formations = [];
$editingFormation = null;
$formData = [];
$successMessage = null;
$errorMessage = null;
$loadError = null;
$repo = null;

function adminE(mixed $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function adminFormValue(array $data, string $key): string
{
    return adminE($data[$key] ?? '');
}

function adminSelected(array $data, string $key, string $value): string
{
    return (string)($data[$key] ?? '') === $value ? ' selected' : '';
}

function adminStatusBadge(string $status): string
{
    return match ($status) {
        'publie' => '<span class="badge rounded-pill text-bg-success">Publié</span>',
        'archive' => '<span class="badge rounded-pill text-bg-secondary">Archivé</span>',
        default => '<span class="badge rounded-pill text-bg-warning">Brouillon</span>',
    };
}

function adminTypeLabel(string $type): string
{
    return match ($type) {
        'playlist' => 'Playlist',
        'video' => 'Vidéo',
        default => 'Contenu',
    };
}

function adminDurationLabel(int $minutes): string
{
    if ($minutes >= 60) {
        $hours = intdiv($minutes, 60);
        $remainingMinutes = $minutes % 60;

        return $remainingMinutes > 0
            ? $hours . ' h ' . $remainingMinutes . ' min'
            : $hours . ' h';
    }

    return $minutes . ' min';
}

function adminDateLabel(?string $date): string
{
    if ($date === null || trim($date) === '') {
        return '-';
    }

    $timestamp = strtotime($date);

    if ($timestamp === false) {
        return $date;
    }

    return date('Y-m-d H:i', $timestamp);
}

try {
    require_once __DIR__ . '/config/db.php';

    if (isset($pdo) && $pdo instanceof PDO) {
        $database = $pdo;
    } elseif (isset($db) && $db instanceof PDO) {
        $database = $db;
    } elseif (isset($connexion) && $connexion instanceof PDO) {
        $database = $connexion;
    } else {
        throw new RuntimeException('Aucune connexion PDO disponible. Vérifie que config/db.php crée une variable $pdo.');
    }

    $repo = new FormationRepository($database);
} catch (Throwable $exception) {
    $loadError = $exception->getMessage();
}

if ($repo instanceof FormationRepository && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = (string)($_POST['action'] ?? '');

    try {
        if ($action === 'save_formation') {
            $id = (int)($_POST['id'] ?? 0);
            $formData = [
                'id' => $id > 0 ? $id : '',
                'titre' => $_POST['titre'] ?? '',
                'description_courte' => $_POST['description_courte'] ?? '',
                'description_longue' => $_POST['description_longue'] ?? '',
                'audience' => $_POST['audience'] ?? '',
                'youtube_url' => $_POST['youtube_url'] ?? '',
                'image_url' => $_POST['image_url'] ?? '',
                'type_contenu' => $_POST['type_contenu'] ?? 'video',
                'duree_minutes' => $_POST['duree_minutes'] ?? '',
                'nb_videos' => $_POST['nb_videos'] ?? '1',
                'categories' => $_POST['categories'] ?? '',
                'statut' => $_POST['statut'] ?? 'brouillon',
            ];

            if ($id > 0) {
                $repo->update($id, $formData);
                header('Location: view-admin.php?success=formation_updated');
                exit;
            }

            $repo->create($formData);
            header('Location: view-admin.php?success=formation_created');
            exit;
        }

        if ($action === 'delete_formation') {
            $id = (int)($_POST['id'] ?? 0);

            if ($id <= 0) {
                throw new InvalidArgumentException('Identifiant de formation invalide.');
            }

            $repo->delete($id);
            header('Location: view-admin.php?success=formation_deleted');
            exit;
        }
    } catch (Throwable $exception) {
        $errorMessage = $exception->getMessage();
    }
}

if (isset($_GET['success'])) {
    $successMessage = match ((string)$_GET['success']) {
        'formation_created' => 'La formation a été créée.',
        'formation_updated' => 'La formation a été modifiée.',
        'formation_deleted' => 'La formation a été supprimée.',
        default => null,
    };
}

if ($repo instanceof FormationRepository) {
    try {
        $editId = isset($_GET['edit']) ? (int)$_GET['edit'] : 0;

        if ($editId > 0 && $errorMessage === null) {
            $editingFormation = $repo->find($editId);

            if ($editingFormation === null) {
                $errorMessage = 'Formation introuvable.';
            } else {
                $formData = $editingFormation;
            }
        }

        $formations = $repo->all();
    } catch (Throwable $exception) {
        $loadError = $exception->getMessage();
    }
}

$isEditing = (int)($formData['id'] ?? 0) > 0;
?>
<!doctype html>
<html lang="fr">
<head>
  <?php include __DIR__ . '/partials/_head.php'; ?>
  <title>Web Formations - Admin</title>

  <style>
    .admin-form-card {
      scroll-margin-top: 1rem;
    }

    .formation-preview-thumb {
      width: 72px;
      height: 48px;
      border-radius: .75rem;
      border: 1px solid var(--border);
      background:
        radial-gradient(circle at top left, rgba(255, 255, 255, .16), transparent 40%),
        linear-gradient(135deg, rgba(255, 255, 255, .08), rgba(255, 255, 255, .02));
      overflow: hidden;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: .75rem;
      font-weight: 700;
      color: rgba(255, 255, 255, .8);
    }

    .formation-preview-thumb img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .admin-table-row.is-hidden {
      display: none;
    }
  </style>
</head>

<body>
<?php include __DIR__ . '/partials/_navbar.php'; ?>

  <main class="container my-4 my-lg-5">
    <section class="panel p-4 p-lg-5 mb-4">
      <div class="row g-4 align-items-center">
        <div class="col-lg-8">
          <h1 class="h3 fw-semibold mb-2">Administration</h1>
          <p class="muted mb-3">
            Gère les <strong>formations</strong> avec le repository connecté à la base de données.
          </p>
          <div class="d-flex flex-wrap gap-2">
            <a class="btn btn-accent" href="#formation-form">+ Ajouter une formation</a>
            <a class="btn btn-ghost" href="index.php">Voir la page publique</a>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="help p-3">
            <div class="fw-semibold mb-1">Source des données</div>
            <div class="muted small">
              Les formations sont lues et modifiées dans MySQL via <code>FormationRepository</code>.
            </div>
          </div>
        </div>
      </div>
    </section>

    <?php if ($successMessage !== null): ?>
      <div class="alert alert-success" role="status">
        <?= adminE($successMessage); ?>
      </div>
    <?php endif; ?>

    <?php if ($errorMessage !== null): ?>
      <div class="alert alert-danger" role="alert">
        <?= adminE($errorMessage); ?>
      </div>
    <?php endif; ?>

    <?php if ($loadError !== null): ?>
      <div class="alert alert-danger" role="alert">
        <strong>Erreur de connexion aux formations.</strong><br>
        <?= adminE($loadError); ?>
      </div>
    <?php endif; ?>

    <section id="formation-form" class="card admin-form-card mb-4">
      <div class="card-body p-3 p-lg-4">
        <div class="d-flex align-items-start justify-content-between gap-3 flex-wrap mb-3">
          <div>
            <h2 class="h5 mb-1"><?= $isEditing ? 'Modifier une formation' : 'Ajouter une formation'; ?></h2>
            <p class="muted mb-0">
              Formulaire branché sur la table <code>formations</code> via le repository.
            </p>
          </div>

          <?php if ($isEditing): ?>
            <a class="btn btn-ghost btn-sm" href="view-admin.php#formation-form">Annuler la modification</a>
          <?php endif; ?>
        </div>

        <?php if ($repo instanceof FormationRepository): ?>
          <form action="view-admin.php#formation-form" method="post" class="row g-3">
            <input type="hidden" name="action" value="save_formation">
            <input type="hidden" name="id" value="<?= adminFormValue($formData, 'id'); ?>">

            <div class="col-12 col-lg-6">
              <label class="form-label" for="titre">Titre</label>
              <input class="form-control" id="titre" type="text" name="titre" value="<?= adminFormValue($formData, 'titre'); ?>" required>
            </div>

            <div class="col-12 col-lg-6">
              <label class="form-label" for="youtube_url">URL YouTube</label>
              <input class="form-control" id="youtube_url" type="url" name="youtube_url" value="<?= adminFormValue($formData, 'youtube_url'); ?>" placeholder="https://www.youtube.com/watch?v=..." required>
            </div>

            <div class="col-12">
              <label class="form-label" for="description_courte">Description courte</label>
              <input class="form-control" id="description_courte" type="text" name="description_courte" value="<?= adminFormValue($formData, 'description_courte'); ?>" maxlength="255" required>
            </div>

            <div class="col-12 col-lg-6">
              <label class="form-label" for="description_longue">Description longue</label>
              <textarea class="form-control" id="description_longue" name="description_longue" rows="6" required><?= adminFormValue($formData, 'description_longue'); ?></textarea>
            </div>

            <div class="col-12 col-lg-6">
              <label class="form-label" for="audience">Audience</label>
              <textarea class="form-control" id="audience" name="audience" rows="6" required><?= adminFormValue($formData, 'audience'); ?></textarea>
            </div>

            <div class="col-12 col-lg-6">
              <label class="form-label" for="image_url">Image URL</label>
              <input class="form-control" id="image_url" type="text" name="image_url" value="<?= adminFormValue($formData, 'image_url'); ?>" placeholder="assets/img/formation.jpg">
              <div class="form-text">Champ optionnel. Tu peux utiliser un chemin local ou une URL.</div>
            </div>

            <div class="col-12 col-md-6 col-lg-3">
              <label class="form-label" for="type_contenu">Type de contenu</label>
              <select class="form-select" id="type_contenu" name="type_contenu" required>
                <option value="video"<?= adminSelected($formData, 'type_contenu', 'video'); ?>>Vidéo</option>
                <option value="playlist"<?= adminSelected($formData, 'type_contenu', 'playlist'); ?>>Playlist</option>
              </select>
            </div>

            <div class="col-12 col-md-6 col-lg-3">
              <label class="form-label" for="statut">Statut</label>
              <select class="form-select" id="statut" name="statut" required>
                <option value="brouillon"<?= adminSelected($formData, 'statut', 'brouillon'); ?>>Brouillon</option>
                <option value="publie"<?= adminSelected($formData, 'statut', 'publie'); ?>>Publié</option>
                <option value="archive"<?= adminSelected($formData, 'statut', 'archive'); ?>>Archivé</option>
              </select>
            </div>

            <div class="col-12 col-md-6 col-lg-3">
              <label class="form-label" for="duree_minutes">Durée en minutes</label>
              <input class="form-control" id="duree_minutes" type="number" name="duree_minutes" value="<?= adminFormValue($formData, 'duree_minutes'); ?>" min="1" required>
            </div>

            <div class="col-12 col-md-6 col-lg-3">
              <label class="form-label" for="nb_videos">Nombre de vidéos</label>
              <input class="form-control" id="nb_videos" type="number" name="nb_videos" value="<?= adminFormValue($formData, 'nb_videos') !== '' ? adminFormValue($formData, 'nb_videos') : '1'; ?>" min="1" required>
            </div>

            <div class="col-12">
              <label class="form-label" for="categories">Catégories</label>
              <input class="form-control" id="categories" type="text" name="categories" value="<?= adminFormValue($formData, 'categories'); ?>" placeholder="Web, Bootstrap, Marketing">
              <div class="form-text">Sépare les catégories par des virgules.</div>
            </div>

            <div class="col-12 d-flex flex-wrap align-items-center gap-2">
              <button class="btn btn-accent" type="submit">
                <?= $isEditing ? 'Enregistrer les modifications' : 'Créer la formation'; ?>
              </button>

              <?php if ($isEditing): ?>
                <a class="btn btn-ghost" href="view-admin.php#formation-form">Annuler</a>
              <?php endif; ?>
            </div>
          </form>
        <?php else: ?>
          <div class="alert alert-warning mb-0">
            Le formulaire est désactivé parce que la connexion au repository n’est pas disponible.
          </div>
        <?php endif; ?>
      </div>
    </section>

    <div class="row g-4">
      <div class="col-12">
        <section class="card">
          <div class="card-body p-3 p-lg-4">
            <div class="d-flex align-items-start justify-content-between gap-2 flex-wrap">
              <div>
                <h2 class="h5 mb-1">1) Gestion des formations</h2>
                <p class="muted mb-0">Liste complète des formations, incluant les brouillons et les archives.</p>
              </div>
              <span class="badge rounded-pill text-bg-secondary"><?= count($formations); ?> formation<?= count($formations) > 1 ? 's' : ''; ?></span>
            </div>

            <div class="divider my-3"></div>

            <div class="row g-2 align-items-center mb-3">
              <div class="col-12 col-md-7">
                <input id="searchFormations" class="form-control" placeholder="Rechercher une formation, une catégorie ou un statut">
              </div>
              <div class="col-12 col-md-5 d-flex gap-2">
                <select id="sortFormations" class="form-select">
                  <option value="id-asc">Tri: ID croissant</option>
                  <option value="title-asc">Tri: Titre A-Z</option>
                  <option value="title-desc">Tri: Titre Z-A</option>
                  <option value="newest">Tri: Plus récentes</option>
                  <option value="oldest">Tri: Plus anciennes</option>
                </select>
              </div>
            </div>

            <div class="table-responsive">
              <table class="table table-hover align-middle mb-0">
                <thead>
                  <tr>
                    <th class="text-nowrap">ID</th>
                    <th>Formation</th>
                    <th class="text-nowrap">Aperçu</th>
                    <th class="text-nowrap">Type</th>
                    <th class="text-nowrap">Durée</th>
                    <th class="text-nowrap">Statut</th>
                    <th class="text-nowrap">Créée</th>
                    <th class="text-end">Actions</th>
                  </tr>
                </thead>
                <tbody id="formationsTbody">
                  <?php foreach ($formations as $formation): ?>
                    <?php
                      $id = (int)($formation['id'] ?? 0);
                      $title = (string)($formation['titre'] ?? '');
                      $description = (string)($formation['description_courte'] ?? '');
                      $categories = (string)($formation['categories'] ?? '');
                      $status = (string)($formation['statut'] ?? 'brouillon');
                      $type = (string)($formation['type_contenu'] ?? 'video');
                      $duration = (int)($formation['duree_minutes'] ?? 0);
                      $createdAt = (string)($formation['created_at'] ?? '');
                      $imageUrl = trim((string)($formation['image_url'] ?? ''));
                      $searchText = strtolower($title . ' ' . $description . ' ' . $categories . ' ' . $status . ' ' . $type);
                    ?>
                    <tr class="admin-table-row" data-search="<?= adminE($searchText); ?>" data-id="<?= $id; ?>" data-title="<?= adminE(strtolower($title)); ?>" data-created="<?= adminE($createdAt); ?>">
                      <td class="text-nowrap muted">#<?= $id; ?></td>
                      <td>
                        <div class="fw-semibold"><?= adminE($title); ?></div>
                        <div class="muted small"><?= adminE($description); ?></div>
                        <?php if ($categories !== ''): ?>
                          <div class="d-flex flex-wrap gap-1 mt-2">
                            <?php foreach (array_filter(array_map('trim', explode(',', $categories))) as $category): ?>
                              <span class="badge rounded-pill text-bg-light"><?= adminE($category); ?></span>
                            <?php endforeach; ?>
                          </div>
                        <?php endif; ?>
                      </td>
                      <td>
                        <div class="formation-preview-thumb">
                          <?php if ($imageUrl !== ''): ?>
                            <img src="<?= adminE($imageUrl); ?>" alt="Aperçu de <?= adminE($title); ?>">
                          <?php else: ?>
                            IMG
                          <?php endif; ?>
                        </div>
                      </td>
                      <td class="text-nowrap"><?= adminE(adminTypeLabel($type)); ?></td>
                      <td class="text-nowrap"><?= adminE(adminDurationLabel($duration)); ?></td>
                      <td class="text-nowrap"><?= adminStatusBadge($status); ?></td>
                      <td class="text-nowrap muted small"><?= adminE(adminDateLabel($createdAt)); ?></td>
                      <td class="text-end">
                        <div class="btn-group btn-group-sm" role="group" aria-label="Actions formation <?= $id; ?>">
                          <?php if ((string)($formation['youtube_url'] ?? '') !== ''): ?>
                            <a class="btn btn-ghost" href="<?= adminE($formation['youtube_url']); ?>" target="_blank" rel="noopener">Voir</a>
                          <?php endif; ?>
                          <a class="btn btn-ghost" href="view-admin.php?edit=<?= $id; ?>#formation-form">Modifier</a>
                          <form action="view-admin.php" method="post" onsubmit="return confirm('Supprimer cette formation ?');">
                            <input type="hidden" name="action" value="delete_formation">
                            <input type="hidden" name="id" value="<?= $id; ?>">
                            <button class="btn btn-danger" type="submit">Supprimer</button>
                          </form>
                        </div>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>

            <?php if (count($formations) === 0 && $loadError === null): ?>
              <div class="alert alert-info mt-3 mb-0" role="status">
                Aucune formation pour le moment.
              </div>
            <?php endif; ?>

            <div class="alert alert-info mt-3 mb-0 d-none" id="noFormationResults" role="status">
              Aucune formation ne correspond à la recherche.
            </div>
          </div>
        </section>
      </div>

      <div class="col-12">
        <section class="card">
          <div class="card-body p-3 p-lg-4">
            <div class="d-flex align-items-start justify-content-between gap-2">
              <div>
                <h2 class="h5 mb-1">2) Gestion des inscriptions</h2>
                <p class="muted mb-0">Section conservée en démo pour l’instant.</p>
              </div>
              <span class="badge rounded-pill text-bg-success">Démo</span>
            </div>

            <div class="divider my-3"></div>

            <div class="table-responsive">
              <table class="table table-hover align-middle mb-0">
                <thead>
                  <tr>
                    <th>Contact</th>
                    <th class="text-nowrap">Statut</th>
                    <th class="text-end">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>
                      <div class="fw-semibold">Alex Martin</div>
                      <div class="muted small">alex@email.com - Bootstrap v2 - pages efficaces</div>
                    </td>
                    <td class="text-nowrap"><span class="badge text-bg-success">Nouveau</span></td>
                    <td class="text-end">
                      <div class="btn-group btn-group-sm">
                        <a class="btn btn-ghost" href="#">Détails</a>
                        <a class="btn btn-ghost" href="#">Marquer contacté</a>
                        <a class="btn btn-danger" href="#">Archiver</a>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <div class="fw-semibold">Samira Diallo</div>
                      <div class="muted small">samira@email.com - Marketing - offre claire</div>
                    </td>
                    <td class="text-nowrap"><span class="badge text-bg-primary">Contacté</span></td>
                    <td class="text-end">
                      <div class="btn-group btn-group-sm">
                        <a class="btn btn-ghost" href="#">Détails</a>
                        <a class="btn btn-ghost" href="#">Marquer contacté</a>
                        <a class="btn btn-danger" href="#">Archiver</a>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </section>
      </div>
    </div>

    <?php include __DIR__ . '/partials/_footer.php'; ?>
  </main>

<?php include __DIR__ . '/partials/_libjs.php'; ?>

  <script>
    const yearElement = document.getElementById("year");

    if (yearElement) {
      yearElement.textContent = new Date().getFullYear();
    }

    const searchInput = document.getElementById("searchFormations");
    const sortSelect = document.getElementById("sortFormations");
    const tbody = document.getElementById("formationsTbody");
    const noResults = document.getElementById("noFormationResults");

    function filterRows() {
      if (!tbody) {
        return;
      }

      const query = searchInput ? searchInput.value.trim().toLowerCase() : "";
      let visibleCount = 0;

      tbody.querySelectorAll("tr").forEach((row) => {
        const searchText = row.dataset.search || "";
        const isVisible = query === "" || searchText.includes(query);

        row.classList.toggle("is-hidden", !isVisible);

        if (isVisible) {
          visibleCount++;
        }
      });

      if (noResults) {
        noResults.classList.toggle("d-none", visibleCount > 0 || query === "");
      }
    }

    function sortRows() {
      if (!tbody || !sortSelect) {
        return;
      }

      const rows = Array.from(tbody.querySelectorAll("tr"));
      const sortValue = sortSelect.value;

      rows.sort((a, b) => {
        if (sortValue === "title-asc") {
          return (a.dataset.title || "").localeCompare(b.dataset.title || "");
        }

        if (sortValue === "title-desc") {
          return (b.dataset.title || "").localeCompare(a.dataset.title || "");
        }

        if (sortValue === "newest") {
          return (b.dataset.created || "").localeCompare(a.dataset.created || "");
        }

        if (sortValue === "oldest") {
          return (a.dataset.created || "").localeCompare(b.dataset.created || "");
        }

        return Number(a.dataset.id || 0) - Number(b.dataset.id || 0);
      });

      rows.forEach((row) => tbody.appendChild(row));
      filterRows();
    }

    if (searchInput) {
      searchInput.addEventListener("input", filterRows);
    }

    if (sortSelect) {
      sortSelect.addEventListener("change", sortRows);
    }
  </script>
</body>
</html>

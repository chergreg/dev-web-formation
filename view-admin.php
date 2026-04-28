<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/src/FormationRepository.php';

require_admin();

$user = current_user();
$formations = [];
$users = [];
$inscriptions = [];
$formationFormData = [];
$inscriptionFormData = [];
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

function adminInscriptionStatusBadge(string $status): string
{
    return match ($status) {
        'confirmee' => '<span class="badge rounded-pill text-bg-success">Confirmée</span>',
        'annulee' => '<span class="badge rounded-pill text-bg-secondary">Annulée</span>',
        default => '<span class="badge rounded-pill text-bg-warning">En attente</span>',
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

function adminReferenceLabel(string $reference): string
{
    $labels = [
        'youtube' => 'YouTube',
        'reseaux_sociaux' => 'Réseaux sociaux',
        'recherche_google' => 'Recherche Google',
        'bouche_a_oreille' => 'Bouche à oreille',
        'newsletter' => 'Newsletter',
        'admin' => 'Ajout admin',
        'autre' => 'Autre',
    ];

    return $labels[$reference] ?? str_replace('_', ' ', $reference);
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
            $formationFormData = [
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
                $repo->update($id, $formationFormData);
                header('Location: view-admin.php?success=formation_updated');
                exit;
            }

            $repo->create($formationFormData);
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

        if ($action === 'save_inscription') {
            $id = (int)($_POST['id'] ?? 0);
            $inscriptionFormData = [
                'id' => $id > 0 ? $id : '',
                'user_id' => $_POST['user_id'] ?? '',
                'formation_id' => $_POST['formation_id'] ?? '',
                'commentaire' => $_POST['commentaire'] ?? '',
                'reference_source' => $_POST['reference_source'] ?? '',
                'details_reference' => $_POST['details_reference'] ?? '',
                'statut' => $_POST['statut'] ?? 'en_attente',
                'commentaire_admin' => $_POST['commentaire_admin'] ?? '',
            ];

            if ($id > 0) {
                $repo->updateInscription($id, $inscriptionFormData);
                header('Location: view-admin.php?success=inscription_updated#inscriptions');
                exit;
            }

            $repo->createInscription($inscriptionFormData);
            header('Location: view-admin.php?success=inscription_created#inscriptions');
            exit;
        }

        if ($action === 'delete_inscription') {
            $id = (int)($_POST['id'] ?? 0);

            if ($id <= 0) {
                throw new InvalidArgumentException("Identifiant d'inscription invalide.");
            }

            $repo->deleteInscription($id);
            header('Location: view-admin.php?success=inscription_deleted#inscriptions');
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
        'inscription_created' => "L'inscription a été créée.",
        'inscription_updated' => "L'inscription a été modifiée.",
        'inscription_deleted' => "L'inscription a été supprimée.",
        default => null,
    };
}

if ($repo instanceof FormationRepository) {
    try {
        $editFormationId = isset($_GET['edit_formation']) ? (int)$_GET['edit_formation'] : 0;

        if ($editFormationId === 0 && isset($_GET['edit'])) {
            $editFormationId = (int)$_GET['edit'];
        }

        if ($editFormationId > 0 && $errorMessage === null) {
            $editingFormation = $repo->find($editFormationId);

            if ($editingFormation === null) {
                $errorMessage = 'Formation introuvable.';
            } else {
                $formationFormData = $editingFormation;
            }
        }

        $editInscriptionId = isset($_GET['edit_inscription']) ? (int)$_GET['edit_inscription'] : 0;

        if ($editInscriptionId > 0 && $errorMessage === null) {
            $editingInscription = $repo->findInscription($editInscriptionId);

            if ($editingInscription === null) {
                $errorMessage = 'Inscription introuvable.';
            } else {
                $inscriptionFormData = $editingInscription;
            }
        }

        $formations = $repo->all();
        $users = $repo->allUsers();
        $inscriptions = $repo->allInscriptions();
    } catch (Throwable $exception) {
        $loadError = $exception->getMessage();
    }
}

$isEditingFormation = (int)($formationFormData['id'] ?? 0) > 0;
$isEditingInscription = (int)($inscriptionFormData['id'] ?? 0) > 0;
$canManageInscriptions = $repo instanceof FormationRepository && count($users) > 0 && count($formations) > 0;
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

    .admin-table-row.is-hidden,
    .admin-inscription-row.is-hidden {
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
            Gère les <strong>formations</strong> et les <strong>inscriptions</strong> avec le repository connecté à la base de données.
          </p>
          <div class="d-flex flex-wrap gap-2">
            <a class="btn btn-accent" href="#formation-form">+ Ajouter une formation</a>
            <a class="btn btn-ghost" href="#inscription-form">+ Ajouter une inscription</a>
            <a class="btn btn-ghost" href="index.php">Voir la page publique</a>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="help p-3">
            <div class="fw-semibold mb-1">Source des données</div>
            <div class="muted small">
              Les données sont lues et modifiées dans MySQL via <code>FormationRepository</code>.
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
        <strong>Erreur de connexion aux données.</strong><br>
        <?= adminE($loadError); ?>
      </div>
    <?php endif; ?>

    <section id="formation-form" class="card admin-form-card mb-4">
      <div class="card-body p-3 p-lg-4">
        <div class="d-flex align-items-start justify-content-between gap-3 flex-wrap mb-3">
          <div>
            <h2 class="h5 mb-1"><?= $isEditingFormation ? 'Modifier une formation' : 'Ajouter une formation'; ?></h2>
            <p class="muted mb-0">
              Formulaire branché sur la table <code>formations</code> via le repository.
            </p>
          </div>

          <?php if ($isEditingFormation): ?>
            <a class="btn btn-ghost btn-sm" href="view-admin.php#formation-form">Annuler la modification</a>
          <?php endif; ?>
        </div>

        <?php if ($repo instanceof FormationRepository): ?>
          <form action="view-admin.php#formation-form" method="post" class="row g-3">
            <input type="hidden" name="action" value="save_formation">
            <input type="hidden" name="id" value="<?= adminFormValue($formationFormData, 'id'); ?>">

            <div class="col-12 col-lg-6">
              <label class="form-label" for="titre">Titre</label>
              <input class="form-control" id="titre" type="text" name="titre" value="<?= adminFormValue($formationFormData, 'titre'); ?>" required>
            </div>

            <div class="col-12 col-lg-6">
              <label class="form-label" for="youtube_url">URL YouTube</label>
              <input class="form-control" id="youtube_url" type="url" name="youtube_url" value="<?= adminFormValue($formationFormData, 'youtube_url'); ?>" placeholder="https://www.youtube.com/watch?v=..." required>
            </div>

            <div class="col-12">
              <label class="form-label" for="description_courte">Description courte</label>
              <input class="form-control" id="description_courte" type="text" name="description_courte" value="<?= adminFormValue($formationFormData, 'description_courte'); ?>" maxlength="255" required>
            </div>

            <div class="col-12 col-lg-6">
              <label class="form-label" for="description_longue">Description longue</label>
              <textarea class="form-control" id="description_longue" name="description_longue" rows="6" required><?= adminFormValue($formationFormData, 'description_longue'); ?></textarea>
            </div>

            <div class="col-12 col-lg-6">
              <label class="form-label" for="audience">Audience</label>
              <textarea class="form-control" id="audience" name="audience" rows="6" required><?= adminFormValue($formationFormData, 'audience'); ?></textarea>
            </div>

            <div class="col-12 col-lg-6">
              <label class="form-label" for="image_url">Image URL</label>
              <input class="form-control" id="image_url" type="text" name="image_url" value="<?= adminFormValue($formationFormData, 'image_url'); ?>" placeholder="assets/img/formation.jpg">
              <div class="form-text">Champ optionnel. Tu peux utiliser un chemin local ou une URL.</div>
            </div>

            <div class="col-12 col-md-6 col-lg-3">
              <label class="form-label" for="type_contenu">Type de contenu</label>
              <select class="form-select" id="type_contenu" name="type_contenu" required>
                <option value="video"<?= adminSelected($formationFormData, 'type_contenu', 'video'); ?>>Vidéo</option>
                <option value="playlist"<?= adminSelected($formationFormData, 'type_contenu', 'playlist'); ?>>Playlist</option>
              </select>
            </div>

            <div class="col-12 col-md-6 col-lg-3">
              <label class="form-label" for="statut">Statut</label>
              <select class="form-select" id="statut" name="statut" required>
                <option value="brouillon"<?= adminSelected($formationFormData, 'statut', 'brouillon'); ?>>Brouillon</option>
                <option value="publie"<?= adminSelected($formationFormData, 'statut', 'publie'); ?>>Publié</option>
                <option value="archive"<?= adminSelected($formationFormData, 'statut', 'archive'); ?>>Archivé</option>
              </select>
            </div>

            <div class="col-12 col-md-6 col-lg-3">
              <label class="form-label" for="duree_minutes">Durée en minutes</label>
              <input class="form-control" id="duree_minutes" type="number" name="duree_minutes" value="<?= adminFormValue($formationFormData, 'duree_minutes'); ?>" min="1" required>
            </div>

            <div class="col-12 col-md-6 col-lg-3">
              <label class="form-label" for="nb_videos">Nombre de vidéos</label>
              <?php $nbVideosValue = adminFormValue($formationFormData, 'nb_videos') !== '' ? adminFormValue($formationFormData, 'nb_videos') : '1'; ?>
              <input class="form-control" id="nb_videos" type="number" name="nb_videos" value="<?= $nbVideosValue; ?>" min="1" required>
            </div>

            <div class="col-12">
              <label class="form-label" for="categories">Catégories</label>
              <input class="form-control" id="categories" type="text" name="categories" value="<?= adminFormValue($formationFormData, 'categories'); ?>" placeholder="Web, Bootstrap, Marketing">
              <div class="form-text">Sépare les catégories par des virgules.</div>
            </div>

            <div class="col-12 d-flex flex-wrap align-items-center gap-2">
              <button class="btn btn-accent" type="submit">
                <?= $isEditingFormation ? 'Enregistrer les modifications' : 'Créer la formation'; ?>
              </button>

              <?php if ($isEditingFormation): ?>
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
                      $youtubeUrl = (string)($formation['youtube_url'] ?? '');
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
                          <?php if ($youtubeUrl !== ''): ?>
                            <a class="btn btn-ghost" href="<?= adminE($youtubeUrl); ?>" target="_blank" rel="noopener">Voir</a>
                          <?php endif; ?>
                          <a class="btn btn-ghost" href="view-admin.php?edit_formation=<?= $id; ?>#formation-form">Modifier</a>
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

      <div class="col-12" id="inscriptions">
        <section id="inscription-form" class="card admin-form-card mb-4">
          <div class="card-body p-3 p-lg-4">
            <div class="d-flex align-items-start justify-content-between gap-3 flex-wrap mb-3">
              <div>
                <h2 class="h5 mb-1"><?= $isEditingInscription ? 'Modifier une inscription' : 'Ajouter une inscription'; ?></h2>
                <p class="muted mb-0">
                  Formulaire branché sur la table <code>inscriptions</code> via le repository.
                </p>
              </div>

              <?php if ($isEditingInscription): ?>
                <a class="btn btn-ghost btn-sm" href="view-admin.php#inscription-form">Annuler la modification</a>
              <?php endif; ?>
            </div>

            <?php if ($canManageInscriptions): ?>
              <form action="view-admin.php#inscription-form" method="post" class="row g-3">
                <input type="hidden" name="action" value="save_inscription">
                <input type="hidden" name="id" value="<?= adminFormValue($inscriptionFormData, 'id'); ?>">

                <div class="col-12 col-lg-6">
                  <label class="form-label" for="user_id">Utilisateur</label>
                  <select class="form-select" id="user_id" name="user_id" required>
                    <option value="">Choisir un utilisateur</option>
                    <?php foreach ($users as $appUser): ?>
                      <?php
                        $userId = (int)($appUser['id'] ?? 0);
                        $userLabel = trim((string)($appUser['nom'] ?? ''));
                        $userEmail = trim((string)($appUser['email'] ?? ''));
                        $optionLabel = $userLabel !== '' ? $userLabel : 'Utilisateur #' . $userId;
                        if ($userEmail !== '') {
                            $optionLabel .= ' - ' . $userEmail;
                        }
                      ?>
                      <option value="<?= $userId; ?>"<?= adminSelected($inscriptionFormData, 'user_id', (string)$userId); ?>><?= adminE($optionLabel); ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>

                <div class="col-12 col-lg-6">
                  <label class="form-label" for="formation_id">Formation</label>
                  <select class="form-select" id="formation_id" name="formation_id" required>
                    <option value="">Choisir une formation</option>
                    <?php foreach ($formations as $formation): ?>
                      <?php
                        $formationId = (int)($formation['id'] ?? 0);
                        $formationTitle = (string)($formation['titre'] ?? ('Formation #' . $formationId));
                      ?>
                      <option value="<?= $formationId; ?>"<?= adminSelected($inscriptionFormData, 'formation_id', (string)$formationId); ?>><?= adminE($formationTitle); ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>

                <div class="col-12 col-lg-4">
                  <label class="form-label" for="reference_source">Source de référence</label>
                  <input class="form-control" id="reference_source" type="text" name="reference_source" value="<?= adminFormValue($inscriptionFormData, 'reference_source') !== '' ? adminFormValue($inscriptionFormData, 'reference_source') : 'admin'; ?>" list="referenceOptions" required>
                  <datalist id="referenceOptions">
                    <option value="admin">
                    <option value="youtube">
                    <option value="reseaux_sociaux">
                    <option value="recherche_google">
                    <option value="bouche_a_oreille">
                    <option value="newsletter">
                    <option value="autre">
                  </datalist>
                </div>

                <div class="col-12 col-lg-4">
                  <label class="form-label" for="details_reference">Détails de référence</label>
                  <input class="form-control" id="details_reference" type="text" name="details_reference" value="<?= adminFormValue($inscriptionFormData, 'details_reference'); ?>" placeholder="Ex: vidéo YouTube, publication, ami...">
                </div>

                <div class="col-12 col-lg-4">
                  <label class="form-label" for="inscription_statut">Statut</label>
                  <select class="form-select" id="inscription_statut" name="statut" required>
                    <option value="en_attente"<?= adminSelected($inscriptionFormData, 'statut', 'en_attente'); ?>>En attente</option>
                    <option value="confirmee"<?= adminSelected($inscriptionFormData, 'statut', 'confirmee'); ?>>Confirmée</option>
                    <option value="annulee"<?= adminSelected($inscriptionFormData, 'statut', 'annulee'); ?>>Annulée</option>
                  </select>
                </div>

                <div class="col-12 col-lg-6">
                  <label class="form-label" for="commentaire">Commentaire utilisateur</label>
                  <textarea class="form-control" id="commentaire" name="commentaire" rows="4" placeholder="Commentaire laissé par l'utilisateur"><?= adminFormValue($inscriptionFormData, 'commentaire'); ?></textarea>
                </div>

                <div class="col-12 col-lg-6">
                  <label class="form-label" for="commentaire_admin">Commentaire admin</label>
                  <textarea class="form-control" id="commentaire_admin" name="commentaire_admin" rows="4" placeholder="Note interne pour l'administration"><?= adminFormValue($inscriptionFormData, 'commentaire_admin'); ?></textarea>
                </div>

                <div class="col-12 d-flex flex-wrap align-items-center gap-2">
                  <button class="btn btn-accent" type="submit">
                    <?= $isEditingInscription ? "Enregistrer l'inscription" : "Créer l'inscription"; ?>
                  </button>

                  <?php if ($isEditingInscription): ?>
                    <a class="btn btn-ghost" href="view-admin.php#inscription-form">Annuler</a>
                  <?php endif; ?>
                </div>
              </form>
            <?php elseif ($repo instanceof FormationRepository): ?>
              <div class="alert alert-warning mb-0">
                Le formulaire d’inscription nécessite au moins un utilisateur et une formation.
              </div>
            <?php else: ?>
              <div class="alert alert-warning mb-0">
                Le formulaire est désactivé parce que la connexion au repository n’est pas disponible.
              </div>
            <?php endif; ?>
          </div>
        </section>

        <section class="card">
          <div class="card-body p-3 p-lg-4">
            <div class="d-flex align-items-start justify-content-between gap-2 flex-wrap">
              <div>
                <h2 class="h5 mb-1">2) Gestion des inscriptions</h2>
                <p class="muted mb-0">Liste des inscriptions reliées aux utilisateurs et aux formations.</p>
              </div>
              <span class="badge rounded-pill text-bg-secondary"><?= count($inscriptions); ?> inscription<?= count($inscriptions) > 1 ? 's' : ''; ?></span>
            </div>

            <div class="divider my-3"></div>

            <div class="row g-2 align-items-center mb-3">
              <div class="col-12 col-md-7">
                <input id="searchInscriptions" class="form-control" placeholder="Rechercher un utilisateur, une formation, une source ou un statut">
              </div>
              <div class="col-12 col-md-5 d-flex gap-2">
                <select id="sortInscriptions" class="form-select">
                  <option value="newest">Tri: Plus récentes</option>
                  <option value="oldest">Tri: Plus anciennes</option>
                  <option value="status-asc">Tri: Statut A-Z</option>
                  <option value="user-asc">Tri: Utilisateur A-Z</option>
                  <option value="formation-asc">Tri: Formation A-Z</option>
                </select>
              </div>
            </div>

            <div class="table-responsive">
              <table class="table table-hover align-middle mb-0">
                <thead>
                  <tr>
                    <th class="text-nowrap">ID</th>
                    <th>Utilisateur</th>
                    <th>Formation</th>
                    <th class="text-nowrap">Référence</th>
                    <th class="text-nowrap">Statut</th>
                    <th class="text-nowrap">Créée</th>
                    <th class="text-end">Actions</th>
                  </tr>
                </thead>
                <tbody id="inscriptionsTbody">
                  <?php foreach ($inscriptions as $inscription): ?>
                    <?php
                      $inscriptionId = (int)($inscription['id'] ?? 0);
                      $inscriptionUserId = (int)($inscription['user_id'] ?? 0);
                      $inscriptionFormationId = (int)($inscription['formation_id'] ?? 0);
                      $userName = trim((string)($inscription['user_nom'] ?? ''));
                      $userEmail = trim((string)($inscription['user_email'] ?? ''));
                      $formationTitle = trim((string)($inscription['formation_titre'] ?? ''));
                      $referenceSource = (string)($inscription['reference_source'] ?? '');
                      $detailsReference = trim((string)($inscription['details_reference'] ?? ''));
                      $inscriptionStatus = (string)($inscription['statut'] ?? 'en_attente');
                      $createdAt = (string)($inscription['created_at'] ?? '');
                      $commentaire = trim((string)($inscription['commentaire'] ?? ''));
                      $commentaireAdmin = trim((string)($inscription['commentaire_admin'] ?? ''));
                      $displayUser = $userName !== '' ? $userName : 'Utilisateur #' . $inscriptionUserId;
                      $displayFormation = $formationTitle !== '' ? $formationTitle : 'Formation #' . $inscriptionFormationId;
                      $searchText = strtolower($displayUser . ' ' . $userEmail . ' ' . $displayFormation . ' ' . $referenceSource . ' ' . $detailsReference . ' ' . $inscriptionStatus . ' ' . $commentaire . ' ' . $commentaireAdmin);
                    ?>
                    <tr class="admin-inscription-row" data-search="<?= adminE($searchText); ?>" data-id="<?= $inscriptionId; ?>" data-created="<?= adminE($createdAt); ?>" data-user="<?= adminE(strtolower($displayUser)); ?>" data-formation="<?= adminE(strtolower($displayFormation)); ?>" data-status="<?= adminE(strtolower($inscriptionStatus)); ?>">
                      <td class="text-nowrap muted">#<?= $inscriptionId; ?></td>
                      <td>
                        <div class="fw-semibold"><?= adminE($displayUser); ?></div>
                        <?php if ($userEmail !== ''): ?>
                          <div class="muted small"><?= adminE($userEmail); ?></div>
                        <?php endif; ?>
                        <?php if ($commentaire !== ''): ?>
                          <div class="muted small mt-2">Commentaire: <?= adminE($commentaire); ?></div>
                        <?php endif; ?>
                      </td>
                      <td>
                        <div class="fw-semibold"><?= adminE($displayFormation); ?></div>
                        <div class="muted small">Formation #<?= $inscriptionFormationId; ?></div>
                      </td>
                      <td class="text-nowrap">
                        <div><?= adminE(adminReferenceLabel($referenceSource)); ?></div>
                        <?php if ($detailsReference !== ''): ?>
                          <div class="muted small"><?= adminE($detailsReference); ?></div>
                        <?php endif; ?>
                      </td>
                      <td class="text-nowrap">
                        <?= adminInscriptionStatusBadge($inscriptionStatus); ?>
                        <?php if ($commentaireAdmin !== ''): ?>
                          <div class="muted small mt-2">Note admin ajoutée</div>
                        <?php endif; ?>
                      </td>
                      <td class="text-nowrap muted small"><?= adminE(adminDateLabel($createdAt)); ?></td>
                      <td class="text-end">
                        <div class="btn-group btn-group-sm" role="group" aria-label="Actions inscription <?= $inscriptionId; ?>">
                          <a class="btn btn-ghost" href="view-admin.php?edit_inscription=<?= $inscriptionId; ?>#inscription-form">Modifier</a>
                          <form action="view-admin.php#inscriptions" method="post" onsubmit="return confirm('Supprimer cette inscription ?');">
                            <input type="hidden" name="action" value="delete_inscription">
                            <input type="hidden" name="id" value="<?= $inscriptionId; ?>">
                            <button class="btn btn-danger" type="submit">Supprimer</button>
                          </form>
                        </div>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>

            <?php if (count($inscriptions) === 0 && $loadError === null): ?>
              <div class="alert alert-info mt-3 mb-0" role="status">
                Aucune inscription pour le moment.
              </div>
            <?php endif; ?>

            <div class="alert alert-info mt-3 mb-0 d-none" id="noInscriptionResults" role="status">
              Aucune inscription ne correspond à la recherche.
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

    const inscriptionSearchInput = document.getElementById("searchInscriptions");
    const inscriptionSortSelect = document.getElementById("sortInscriptions");
    const inscriptionTbody = document.getElementById("inscriptionsTbody");
    const noInscriptionResults = document.getElementById("noInscriptionResults");

    function filterInscriptionRows() {
      if (!inscriptionTbody) {
        return;
      }

      const query = inscriptionSearchInput ? inscriptionSearchInput.value.trim().toLowerCase() : "";
      let visibleCount = 0;

      inscriptionTbody.querySelectorAll("tr").forEach((row) => {
        const searchText = row.dataset.search || "";
        const isVisible = query === "" || searchText.includes(query);

        row.classList.toggle("is-hidden", !isVisible);

        if (isVisible) {
          visibleCount++;
        }
      });

      if (noInscriptionResults) {
        noInscriptionResults.classList.toggle("d-none", visibleCount > 0 || query === "");
      }
    }

    function sortInscriptionRows() {
      if (!inscriptionTbody || !inscriptionSortSelect) {
        return;
      }

      const rows = Array.from(inscriptionTbody.querySelectorAll("tr"));
      const sortValue = inscriptionSortSelect.value;

      rows.sort((a, b) => {
        if (sortValue === "oldest") {
          return (a.dataset.created || "").localeCompare(b.dataset.created || "");
        }

        if (sortValue === "status-asc") {
          return (a.dataset.status || "").localeCompare(b.dataset.status || "");
        }

        if (sortValue === "user-asc") {
          return (a.dataset.user || "").localeCompare(b.dataset.user || "");
        }

        if (sortValue === "formation-asc") {
          return (a.dataset.formation || "").localeCompare(b.dataset.formation || "");
        }

        return (b.dataset.created || "").localeCompare(a.dataset.created || "");
      });

      rows.forEach((row) => inscriptionTbody.appendChild(row));
      filterInscriptionRows();
    }

    if (searchInput) {
      searchInput.addEventListener("input", filterRows);
    }

    if (sortSelect) {
      sortSelect.addEventListener("change", sortRows);
    }

    if (inscriptionSearchInput) {
      inscriptionSearchInput.addEventListener("input", filterInscriptionRows);
    }

    if (inscriptionSortSelect) {
      inscriptionSortSelect.addEventListener("change", sortInscriptionRows);
    }
  </script>
</body>
</html>

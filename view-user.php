<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/src/FormationRepository.php';

if (function_exists('require_login')) {
    require_login();
} else {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (empty($_SESSION['user']) && empty($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
}

$currentUser = function_exists('current_user') ? current_user() : ($_SESSION['user'] ?? []);

$formations = [];
$selectedFormation = null;
$successMessage = null;
$errorMessage = null;
$loadError = null;
$repo = null;

$formData = [
    'formation_id' => $_GET['formation_id'] ?? '',
    'commentaire' => '',
    'reference_source' => '',
    'details_reference' => '',
];

function userE(mixed $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function userSelected(array $data, string $key, string $value): string
{
    return (string)($data[$key] ?? '') === $value ? ' selected' : '';
}

function userCurrentId(array $user): int
{
    if (isset($user['id'])) {
        return (int)$user['id'];
    }

    if (isset($_SESSION['user_id'])) {
        return (int)$_SESSION['user_id'];
    }

    if (isset($_SESSION['user']['id'])) {
        return (int)$_SESSION['user']['id'];
    }

    return 0;
}

function userCurrentName(array $user): string
{
    $name = trim((string)($user['nom'] ?? $user['name'] ?? ''));

    return $name !== '' ? $name : 'Utilisateur connecté';
}

function userCurrentEmail(array $user): string
{
    return trim((string)($user['email'] ?? ''));
}

function userSplitCategories(?string $categories): array
{
    if ($categories === null || trim($categories) === '') {
        return [];
    }

    $items = array_map('trim', explode(',', $categories));

    return array_values(array_filter($items, fn(string $item): bool => $item !== ''));
}

function userTypeLabel(?string $type): string
{
    return match ($type) {
        'playlist' => 'Playlist',
        'video' => 'Vidéo',
        default => 'Contenu',
    };
}

function userDurationLabel(int $minutes): string
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

function userFormationInitials(string $title): string
{
    $words = preg_split('/\s+/', trim($title)) ?: [];
    $letters = '';

    foreach ($words as $word) {
        $cleanWord = preg_replace('/[^A-Za-zÀ-ÿ0-9]/u', '', $word) ?? '';

        if ($cleanWord !== '') {
            $letters .= mb_strtoupper(mb_substr($cleanWord, 0, 1));
        }

        if (mb_strlen($letters) >= 2) {
            break;
        }
    }

    return $letters !== '' ? $letters : 'F';
}

function userFindFormation(array $formations, int $id): ?array
{
    foreach ($formations as $formation) {
        if ((int)($formation['id'] ?? 0) === $id) {
            return $formation;
        }
    }

    return null;
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
    $formations = $repo->allPublished();
} catch (Throwable $exception) {
    $loadError = $exception->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formData = [
        'formation_id' => $_POST['formation_id'] ?? '',
        'commentaire' => $_POST['commentaire'] ?? '',
        'reference_source' => $_POST['reference_source'] ?? '',
        'details_reference' => $_POST['details_reference'] ?? '',
    ];

    if ($repo instanceof FormationRepository) {
        try {
            $userId = userCurrentId($currentUser);
            $formationId = (int)($formData['formation_id'] ?? 0);
            $selectedFormation = userFindFormation($formations, $formationId);

            if ($userId <= 0) {
                throw new InvalidArgumentException("Impossible d'identifier l'utilisateur connecté.");
            }

            if ($selectedFormation === null) {
                throw new InvalidArgumentException('La formation choisie est invalide ou non publiée.');
            }

            $repo->createInscription([
                'user_id' => $userId,
                'formation_id' => $formationId,
                'commentaire' => $formData['commentaire'],
                'reference_source' => $formData['reference_source'],
                'details_reference' => $formData['details_reference'],
                'statut' => 'en_attente',
                'commentaire_admin' => '',
            ]);

            header('Location: view-user.php?success=inscription_created&formation_id=' . $formationId);
            exit;
        } catch (Throwable $exception) {
            $errorMessage = $exception->getMessage();
        }
    }
}

if (isset($_GET['success']) && $_GET['success'] === 'inscription_created') {
    $successMessage = "Ton inscription a été enregistrée. Elle est maintenant en attente de validation.";
}

$selectedFormationId = (int)($formData['formation_id'] ?? 0);

if ($selectedFormationId <= 0 && isset($_GET['formation_id'])) {
    $selectedFormationId = (int)$_GET['formation_id'];
}

if ($selectedFormationId > 0 && $selectedFormation === null) {
    $selectedFormation = userFindFormation($formations, $selectedFormationId);
}

$userName = userCurrentName($currentUser);
$userEmail = userCurrentEmail($currentUser);
?>
<!doctype html>
<html lang="fr">
<head>
  <?php include __DIR__ . '/partials/_head.php'; ?>
  <title>Web Formations - Inscription</title>

  <style>
    .user-info-card {
      border: 1px solid var(--border);
      background: rgba(255, 255, 255, .04);
      border-radius: 1.25rem;
    }

    .formation-summary-thumb {
      min-height: 170px;
      border: 1px solid var(--border);
      background:
        radial-gradient(circle at top left, rgba(255, 255, 255, .18), transparent 35%),
        linear-gradient(135deg, rgba(255, 255, 255, .08), rgba(255, 255, 255, .02));
    }

    .formation-summary-thumb img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .formation-choice-card {
      border: 1px solid var(--border);
      background: rgba(255, 255, 255, .04);
      border-radius: 1.25rem;
      transition: transform .15s ease, border-color .15s ease, background-color .15s ease;
    }

    .formation-choice-card:hover {
      transform: translateY(-2px);
      background: rgba(255, 255, 255, .06);
    }

    .formation-choice-card.is-selected {
      border-color: var(--accent);
      background: rgba(255, 255, 255, .08);
    }

    .form-section-card {
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
          <span class="badge rounded-pill text-bg-success mb-3">Espace utilisateur</span>
          <h1 class="display-6 fw-semibold mb-2">Inscription à une formation</h1>
          <p class="muted mb-0">
            Choisis une formation publiée, ajoute quelques informations utiles, puis envoie ta demande d’inscription.
          </p>
        </div>

        <div class="col-lg-5">
          <div class="user-info-card p-3 p-lg-4">
            <div class="small muted mb-1">Connecté en tant que</div>
            <div class="fw-semibold"><?= userE($userName); ?></div>
            <?php if ($userEmail !== ''): ?>
              <div class="muted small"><?= userE($userEmail); ?></div>
            <?php endif; ?>
            <div class="divider my-3"></div>
            <div class="small muted">
              L’inscription sera liée automatiquement à ton compte utilisateur.
            </div>
          </div>
        </div>
      </div>
    </section>

    <?php if ($successMessage !== null): ?>
      <div class="alert alert-success" role="status">
        <?= userE($successMessage); ?>
      </div>
    <?php endif; ?>

    <?php if ($errorMessage !== null): ?>
      <div class="alert alert-danger" role="alert">
        <?= userE($errorMessage); ?>
      </div>
    <?php endif; ?>

    <?php if ($loadError !== null): ?>
      <div class="alert alert-warning" role="alert">
        Impossible de charger les formations : <?= userE($loadError); ?>
      </div>
    <?php endif; ?>

    <div class="row g-4">
      <div class="col-12 col-lg-7">
        <section class="card form-section-card h-100">
          <div class="card-body p-3 p-lg-4">
            <div class="d-flex align-items-start justify-content-between gap-3 flex-wrap mb-3">
              <div>
                <h2 class="h5 mb-1">Formulaire d’inscription</h2>
                <p class="muted mb-0">Les données seront enregistrées dans la table <code>inscriptions</code>.</p>
              </div>
              <span class="badge rounded-pill text-bg-warning">en attente</span>
            </div>

            <?php if ($repo instanceof FormationRepository): ?>
              <form action="view-user.php" method="post" class="row g-3">
                <div class="col-12">
                  <label class="form-label" for="formation_id">Formation choisie</label>
                  <select class="form-select" id="formation_id" name="formation_id" required>
                    <option value="">Choisir une formation</option>
                    <?php foreach ($formations as $formation): ?>
                      <?php
                        $formationId = (int)($formation['id'] ?? 0);
                        $formationTitle = (string)($formation['titre'] ?? ('Formation #' . $formationId));
                      ?>
                      <option value="<?= $formationId; ?>"<?= userSelected(['formation_id' => (string)$selectedFormationId], 'formation_id', (string)$formationId); ?>>
                        <?= userE($formationTitle); ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                  <div class="form-text">La liste contient uniquement les formations publiées.</div>
                </div>

                <div class="col-12 col-lg-6">
                  <label class="form-label" for="reference_source">Comment as-tu connu la formation?</label>
                  <select class="form-select" id="reference_source" name="reference_source" required>
                    <option value="">Choisir une source</option>
                    <option value="youtube"<?= userSelected($formData, 'reference_source', 'youtube'); ?>>YouTube</option>
                    <option value="reseaux_sociaux"<?= userSelected($formData, 'reference_source', 'reseaux_sociaux'); ?>>Réseaux sociaux</option>
                    <option value="recherche_google"<?= userSelected($formData, 'reference_source', 'recherche_google'); ?>>Recherche Google</option>
                    <option value="bouche_a_oreille"<?= userSelected($formData, 'reference_source', 'bouche_a_oreille'); ?>>Bouche à oreille</option>
                    <option value="newsletter"<?= userSelected($formData, 'reference_source', 'newsletter'); ?>>Newsletter</option>
                    <option value="autre"<?= userSelected($formData, 'reference_source', 'autre'); ?>>Autre</option>
                  </select>
                </div>

                <div class="col-12 col-lg-6">
                  <label class="form-label" for="details_reference">Détail de la référence</label>
                  <input
                    class="form-control"
                    id="details_reference"
                    name="details_reference"
                    value="<?= userE($formData['details_reference'] ?? ''); ?>"
                    maxlength="255"
                    placeholder="Ex: nom de la chaîne, ami, groupe, etc."
                  >
                </div>

                <div class="col-12">
                  <label class="form-label" for="commentaire">Commentaire</label>
                  <textarea
                    class="form-control"
                    id="commentaire"
                    name="commentaire"
                    rows="4"
                    placeholder="Pourquoi veux-tu suivre cette formation?"
                  ><?= userE($formData['commentaire'] ?? ''); ?></textarea>
                </div>

                <div class="col-12 d-flex gap-2 flex-wrap">
                  <button class="btn btn-accent" type="submit">Envoyer mon inscription</button>
                  <a class="btn btn-ghost" href="index.php">Retour aux formations</a>
                </div>
              </form>
            <?php else: ?>
              <div class="alert alert-warning mb-0">
                Le formulaire est désactivé parce que la connexion au repository n’est pas disponible.
              </div>
            <?php endif; ?>
          </div>
        </section>
      </div>

      <div class="col-12 col-lg-5">
        <section class="card form-section-card h-100">
          <div class="card-body p-3 p-lg-4">
            <h2 class="h5 mb-3">Formation sélectionnée</h2>

            <?php if ($selectedFormation !== null): ?>
              <?php
                $title = (string)($selectedFormation['titre'] ?? 'Formation');
                $description = (string)($selectedFormation['description_courte'] ?? '');
                $longDescription = (string)($selectedFormation['description_longue'] ?? '');
                $audience = (string)($selectedFormation['audience'] ?? '');
                $imageUrl = trim((string)($selectedFormation['image_url'] ?? ''));
                $duration = (int)($selectedFormation['duree_minutes'] ?? 0);
                $type = (string)($selectedFormation['type_contenu'] ?? 'video');
                $categories = userSplitCategories($selectedFormation['categories'] ?? null);
              ?>

              <div class="formation-summary-thumb rounded-4 overflow-hidden d-flex align-items-center justify-content-center text-center mb-3">
                <?php if ($imageUrl !== ''): ?>
                  <img src="<?= userE($imageUrl); ?>" alt="Aperçu de <?= userE($title); ?>">
                <?php else: ?>
                  <div>
                    <div class="display-6 fw-semibold mb-1"><?= userE(userFormationInitials($title)); ?></div>
                    <div class="small text-uppercase muted">Aperçu formation</div>
                  </div>
                <?php endif; ?>
              </div>

              <h3 class="h5 mb-2"><?= userE($title); ?></h3>
              <p class="muted"><?= userE($description); ?></p>

              <div class="d-flex flex-wrap gap-2 mb-3">
                <?php foreach ($categories as $category): ?>
                  <span class="badge rounded-pill text-bg-light"><?= userE($category); ?></span>
                <?php endforeach; ?>
                <span class="badge rounded-pill text-bg-light"><?= userE(userTypeLabel($type)); ?></span>
                <span class="badge rounded-pill text-bg-light"><?= userE(userDurationLabel($duration)); ?></span>
              </div>

              <?php if ($longDescription !== ''): ?>
                <div class="divider my-3"></div>
                <p class="mb-3"><?= nl2br(userE($longDescription)); ?></p>
              <?php endif; ?>

              <?php if ($audience !== ''): ?>
                <div class="alert alert-info mb-0">
                  <div class="fw-semibold mb-1">Public cible</div>
                  <div><?= userE($audience); ?></div>
                </div>
              <?php endif; ?>
            <?php else: ?>
              <div class="alert alert-info mb-0">
                Sélectionne une formation dans le formulaire pour voir son résumé ici.
              </div>
            <?php endif; ?>
          </div>
        </section>
      </div>
    </div>

    <?php if (count($formations) > 0): ?>
      <section class="mt-4">
        <div class="d-flex align-items-end justify-content-between gap-2 flex-wrap mb-3">
          <div>
            <h2 class="h5 mb-1">Formations publiées</h2>
            <p class="muted mb-0">Aperçu rapide des formations disponibles à l’inscription.</p>
          </div>
          <span class="badge rounded-pill text-bg-secondary"><?= count($formations); ?> formation<?= count($formations) > 1 ? 's' : ''; ?></span>
        </div>

        <div class="row g-3">
          <?php foreach ($formations as $formation): ?>
            <?php
              $id = (int)($formation['id'] ?? 0);
              $title = (string)($formation['titre'] ?? 'Formation');
              $description = (string)($formation['description_courte'] ?? '');
              $type = (string)($formation['type_contenu'] ?? 'video');
              $duration = (int)($formation['duree_minutes'] ?? 0);
              $categories = userSplitCategories($formation['categories'] ?? null);
              $isSelected = $selectedFormationId === $id;
            ?>

            <div class="col-12 col-md-6 col-xl-4">
              <a class="formation-choice-card d-block p-3 text-decoration-none text-reset<?= $isSelected ? ' is-selected' : ''; ?>" href="view-user.php?formation_id=<?= $id; ?>">
                <div class="d-flex align-items-start justify-content-between gap-3 mb-2">
                  <div class="fw-semibold"><?= userE($title); ?></div>
                  <?php if ($isSelected): ?>
                    <span class="badge rounded-pill text-bg-success">choisie</span>
                  <?php endif; ?>
                </div>
                <div class="muted small mb-2"><?= userE($description); ?></div>
                <div class="d-flex flex-wrap gap-1">
                  <?php foreach ($categories as $category): ?>
                    <span class="badge rounded-pill text-bg-light"><?= userE($category); ?></span>
                  <?php endforeach; ?>
                  <span class="badge rounded-pill text-bg-light"><?= userE(userTypeLabel($type)); ?></span>
                  <span class="badge rounded-pill text-bg-light"><?= userE(userDurationLabel($duration)); ?></span>
                </div>
              </a>
            </div>
          <?php endforeach; ?>
        </div>
      </section>
    <?php endif; ?>

    <?php include __DIR__ . '/partials/_footer.php'; ?>
  </main>

<?php include __DIR__ . '/partials/_libjs.php'; ?>

  <script>
    const formationSelect = document.getElementById('formation_id');

    if (formationSelect) {
      formationSelect.addEventListener('change', () => {
        const formationId = formationSelect.value;

        if (formationId !== '') {
          window.location.href = `view-user.php?formation_id=${encodeURIComponent(formationId)}`;
        }
      });
    }
  </script>
</body>
</html>

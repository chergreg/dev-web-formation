<!doctype html>
<html lang="fr">
<head>
  <?php include __DIR__ . '/partials/_head.php'; ?>
  <title>Web Formations — Admin</title>
</head>

<body>

<?php
require_once __DIR__ . '/src/JsonRepository.php';

$repo = new JsonRepository(__DIR__ . '/data/formations.json');
$formations = $repo->all();

// Test visuel rapide
echo "<h1>Test v1</h1>";
echo "<pre>";
print_r($formations);
echo "</pre>";
exit;
?>


  <!-- NAVBAR -->
  <?php include __DIR__ . '/partials/_navbar.php'; ?>

  <main class="container my-4 my-lg-5">
    <!-- HEADER -->
    <section class="panel p-4 p-lg-5 mb-4">
      <div class="row g-4 align-items-center">
        <div class="col-lg-8">
          <h1 class="h3 fw-semibold mb-2">Administration</h1>
          <p class="muted mb-3">
            Gère les <strong>formations</strong> (ajout / modification / suppression) avec php + json.
          </p>
          <div class="d-flex flex-wrap gap-2">
            <a class="btn btn-accent" href="#add-formation">+ Ajouter une formation</a>
</div>
        </div>
        <div class="col-lg-4">
          <div class="help p-3">
            <div class="fw-semibold mb-1">Note</div>
            <div class="muted small">
              Les données sont stockés sur le serveur en json <em>json</em>, pas de base de données.
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- AJOUT FORMATION (statique, sans JS) -->
    <section id="add-formation" class="card mb-4">
      <div class="card-body p-3 p-lg-4">
        <h2 class="h5 mb-3">Ajouter une formation (démo)</h2>
        <form action="#" method="post" class="row g-3">
          <div class="col-12 col-lg-6">
            <label class="form-label">Titre</label>
            <input class="form-control" type="text" name="title" placeholder="Ex: Lancer son offre en 7 jours" required>
          </div>
          <div class="col-12 col-lg-6">
            <label class="form-label">URL YouTube</label>
            <input class="form-control" type="url" name="youtube_url" placeholder="https://www.youtube.com/watch?v=..." required>
          </div>
          <div class="col-12">
            <label class="form-label">Description</label>
            <textarea class="form-control" name="description" rows="3" placeholder="Résumé de la formation…" required></textarea>
          </div>
          <div class="col-12 col-lg-4">
            <label class="form-label">Prix</label>
            <input class="form-control" type="text" name="price" placeholder="Ex: 29$" required>
          </div>
          <div class="col-12 col-lg-4">
            <label class="form-label">Durée</label>
            <input class="form-control" type="text" name="duration" placeholder="Ex: 45 min" required>
          </div>
          <div class="col-12 col-lg-4">
            <label class="form-label">Vignette (optionnel)</label>
            <input class="form-control" type="url" name="thumb" placeholder="https://.../image.jpg">
          </div>
          <div class="col-12">
            <button class="btn btn-accent" type="submit">Enregistrer</button>
            <span class="muted small ms-2">Traitement PHP à brancher plus tard.</span>
          </div>
        </form>
      </div>
    </section>


    <div class="row g-4">
      <!-- FORMATIONS -->
      <div class="col-12">
        <section class="card">
          <div class="card-body p-3 p-lg-4">
            <div class="d-flex align-items-start justify-content-between gap-2">
              <div>
                <h2 class="h5 mb-1">1) Gestion des formations</h2>
                <p class="muted mb-0">Ajoute, modifie ou supprime des formations.</p>
              </div>
              <span class="badge rounded-pill text-bg-secondary">local</span>
            </div>

            <div class="divider my-3"></div>

            <div class="row g-2 align-items-center mb-3">
              <div class="col-12 col-md-7">
                <input id="searchFormations" class="form-control" placeholder="Rechercher une formation (titre, description)…">
              </div>
              <div class="col-12 col-md-5 d-flex gap-2">
                <select id="sortFormations" class="form-select">
                  <option value="title-asc">Tri: Titre (A→Z)</option>
                  <option value="title-desc">Tri: Titre (Z→A)</option>
                  <option value="newest">Tri: Plus récentes</option>
                  <option value="oldest">Tri: Plus anciennes</option>
                </select>
              </div>
            </div>

            <div class="table-responsive">
              <table class="table table-hover align-middle mb-0">
                <thead>
                  <tr>
                    <th>Formation</th>
                    <th class="text-nowrap">Vignette</th>
                    <th class="text-nowrap">Créée</th>
                    <th class="text-end">Actions</th>
                  </tr>
                </thead>
                <tbody id="formationsTbody">
                  <tr>
                    <td>
                      <div class="fw-semibold">Lancer son offre en 7 jours</div>
                      <div class="muted small">Promesse, prix, tunnel simple, plan d’action.</div>
                    </td>
                    <td class="text-nowrap"><span class="badge text-bg-secondary">—</span></td>
                    <td class="text-nowrap">2026-02-17</td>
                    <td class="text-end">
                      <div class="btn-group btn-group-sm">
                        <a class="btn btn-ghost" href="#">Voir</a>
                        <a class="btn btn-ghost" href="#">Modifier</a>
                        <a class="btn btn-danger" href="#">Supprimer</a>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <div class="fw-semibold">YouTube pour formateurs</div>
                      <div class="muted small">Structure, script, miniature, publication.</div>
                    </td>
                    <td class="text-nowrap"><span class="badge text-bg-secondary">—</span></td>
                    <td class="text-nowrap">2026-02-18</td>
                    <td class="text-end">
                      <div class="btn-group btn-group-sm">
                        <a class="btn btn-ghost" href="#">Voir</a>
                        <a class="btn btn-ghost" href="#">Modifier</a>
                        <a class="btn btn-danger" href="#">Supprimer</a>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <div class="fw-semibold">Bootstrap — pages efficaces</div>
                      <div class="muted small">Mise en page responsive + composants essentiels.</div>
                    </td>
                    <td class="text-nowrap"><span class="badge text-bg-secondary">—</span></td>
                    <td class="text-nowrap">2026-02-19</td>
                    <td class="text-end">
                      <div class="btn-group btn-group-sm">
                        <a class="btn btn-ghost" href="#">Voir</a>
                        <a class="btn btn-ghost" href="#">Modifier</a>
                        <a class="btn btn-danger" href="#">Supprimer</a>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <div class="fw-semibold">Marketing — offre claire</div>
                      <div class="muted small">Positionnement, promesse, page de vente.</div>
                    </td>
                    <td class="text-nowrap"><span class="badge text-bg-secondary">—</span></td>
                    <td class="text-nowrap">2026-02-20</td>
                    <td class="text-end">
                      <div class="btn-group btn-group-sm">
                        <a class="btn btn-ghost" href="#">Voir</a>
                        <a class="btn btn-ghost" href="#">Modifier</a>
                        <a class="btn btn-danger" href="#">Supprimer</a>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <div class="fw-semibold">Productivité — plan 7 jours</div>
                      <div class="muted small">Priorités, routines, système simple.</div>
                    </td>
                    <td class="text-nowrap"><span class="badge text-bg-secondary">—</span></td>
                    <td class="text-nowrap">2026-02-21</td>
                    <td class="text-end">
                      <div class="btn-group btn-group-sm">
                        <a class="btn btn-ghost" href="#">Voir</a>
                        <a class="btn btn-ghost" href="#">Modifier</a>
                        <a class="btn btn-danger" href="#">Supprimer</a>
                      </div>
                    </td>
                  </tr>

                </tbody>
              </table>
            </div>
</div>
        </section>
      </div>

      <!-- INSCRIPTIONS -->
      <div class="col-12">
        <section class="card">
          <div class="card-body p-3 p-lg-4">
            <div class="d-flex align-items-start justify-content-between gap-2">
              <div>
                <h2 class="h5 mb-1">2) Gestion des inscriptions</h2>
                <p class="muted mb-0">Contacts + formation choisie. C’est gratuit.</p>
              </div>
              <span class="badge rounded-pill text-bg-success">GRATUIT</span>
            </div>

            <div class="divider my-3"></div>

            <div class="row g-2 align-items-center mb-3">
              <div class="col-12">
                <input id="searchInscriptions" class="form-control" placeholder="Rechercher (nom, email, formation)…">
              </div>
              <div class="col-12 d-flex gap-2">
                <select id="filterStatus" class="form-select">
                  <option value="all">Tous les statuts</option>
                  <option value="new">Nouveau</option>
                  <option value="contacted">Contacté</option>
                  <option value="archived">Archivé</option>
                </select>
</div>
            </div>

            <div class="table-responsive">
              <table class="table table-hover align-middle mb-0">
                <thead>
                  <tr>
                    <th>Contact</th>
                    <th class="text-nowrap">Statut</th>
                    <th class="text-end">Actions</th>
                  </tr>
                </thead>
                <tbody id="inscriptionsTbody">
                  <tr>
                    <td>
                      <div class="fw-semibold">Alex Martin</div>
                      <div class="muted small">alex@email.com · Lancer son offre en 7 jours</div>
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
                      <div class="muted small">samira@email.com · Bootstrap — pages efficaces</div>
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
                  <tr>
                    <td>
                      <div class="fw-semibold">Noah Tremblay</div>
                      <div class="muted small">noah@email.com · Marketing — offre claire</div>
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
                      <div class="fw-semibold">Camille Nguyen</div>
                      <div class="muted small">camille@email.com · YouTube pour formateurs</div>
                    </td>
                    <td class="text-nowrap"><span class="badge text-bg-secondary">Archivé</span></td>
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
                      <div class="fw-semibold">Liam Roy</div>
                      <div class="muted small">liam@email.com · Productivité — plan 7 jours</div>
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


</body>
</html>

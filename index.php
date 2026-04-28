<?php
// Page statique temporaire
// Objectif : valider le visuel des formations avant la connexion à la base de données.
declare(strict_types=1);
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
    <!-- HERO -->
    <section class="hero p-4 p-lg-5 mb-4">
      <div class="row align-items-center g-4">
        <div class="col-lg-7">
          <h1 class="display-6 fw-semibold mb-2">Plateforme de formations en ligne pour solopreneurs</h1>
          <p class="muted mb-3">
            Découvre des formations courtes et actionnables, avec un format simple et visuel.
            Cette version est statique afin de valider l’interface avant la connexion à la base de données.
          </p>
        </div>

        <div class="col-lg-5">
          <div class="p-3 rounded-4 border" style="border-color: var(--border); background: rgba(0,0,0,.18);">
            <div class="d-flex align-items-center justify-content-between gap-3">
              <div>
                <div class="fw-semibold">Version de validation</div>
                <div class="muted small">Affichage statique des formations publiées</div>
              </div>
              <span class="badge rounded-pill text-bg-success">statique</span>
            </div>
            <div class="divider my-3"></div>
            <div class="row g-2 small muted">
              <div class="col-6">Cartes Bootstrap</div>
              <div class="col-6">Popup détail</div>
              <div class="col-6">Filtres JS</div>
              <div class="col-6">Données fictives</div>
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
          <p class="muted mb-0">
            Formations de démonstration affichées en statique. Plus tard, elles seront récupérées depuis la base de données.
          </p>
        </div>
        <span class="muted small">Seules les formations publiées seront affichées.</span>
      </div>

      <div class="filter-panel mb-4">
        <div class="row g-3 align-items-end">
          <div class="col-lg-8">
            <div class="small fw-semibold mb-2">Filtrer par catégorie</div>
            <div class="d-flex flex-wrap gap-2" data-filter-group="category" aria-label="Filtres par catégorie">
              <button class="filter-chip active" type="button" data-filter-value="all">Toutes</button>
              <button class="filter-chip" type="button" data-filter-value="web">Web</button>
              <button class="filter-chip" type="button" data-filter-value="bootstrap">Bootstrap</button>
              <button class="filter-chip" type="button" data-filter-value="marketing">Marketing</button>
              <button class="filter-chip" type="button" data-filter-value="offre">Offre</button>
              <button class="filter-chip" type="button" data-filter-value="productivite">Productivité</button>
              <button class="filter-chip" type="button" data-filter-value="organisation">Organisation</button>
              <button class="filter-chip" type="button" data-filter-value="seo">SEO</button>
              <button class="filter-chip" type="button" data-filter-value="emailing">Emailing</button>
            </div>
          </div>

          <div class="col-lg-4">
            <div class="small fw-semibold mb-2">Filtrer par type de contenu</div>
            <div class="d-flex flex-wrap gap-2" data-filter-group="type" aria-label="Filtres par type de contenu">
              <button class="filter-chip active" type="button" data-filter-value="all">Tous</button>
              <button class="filter-chip" type="button" data-filter-value="video">Vidéo</button>
              <button class="filter-chip" type="button" data-filter-value="playlist">Playlist</button>
            </div>
          </div>
        </div>
      </div>

      <div class="row g-3 g-lg-4" id="formationsGrid">
        <!-- Formation 1 -->
        <div class="col-12 col-md-6 col-lg-4 formation-card-wrapper" data-categories="web,bootstrap" data-type="video">
          <article class="card h-100">
            <div class="card-body d-flex flex-column">
              <div class="rounded-4 overflow-hidden mb-3 formation-thumb">
                <div class="formation-category-badges" aria-label="Catégories">
                  <span class="badge rounded-pill formation-overlay-badge">Web</span>
                  <span class="badge rounded-pill formation-overlay-badge">Bootstrap</span>
                </div>
                <div class="formation-meta-badges">
                  <span class="badge rounded-pill formation-overlay-badge">Vidéo</span>
                  <span class="badge rounded-pill formation-overlay-badge formation-duration-badge">45:00</span>
                </div>
                <div class="d-flex align-items-center justify-content-center text-center p-3">
                  <div>
                    <div class="display-6 fw-semibold mb-1">BS</div>
                    <div class="small text-uppercase formation-thumb-label">Aperçu formation</div>
                  </div>
                </div>
              </div>

              <h3 class="card-title h5 mb-2">Bootstrap v2 - pages efficaces</h3>
              <p class="muted mb-3">
                Apprendre à créer des pages Web simples, propres et responsive avec Bootstrap.
              </p>

              <div class="d-flex gap-2 mt-auto">
                <button class="btn btn-ghost w-100" type="button" data-bs-toggle="modal" data-bs-target="#formationModal1">
                  Voir la formation
                </button>
                <a class="btn btn-accent w-100" href="view-user.php?formation_id=1">
                  Inscription
                </a>
              </div>
            </div>
          </article>
        </div>

        <!-- Formation 2 -->
        <div class="col-12 col-md-6 col-lg-4 formation-card-wrapper" data-categories="marketing,offre" data-type="video">
          <article class="card h-100">
            <div class="card-body d-flex flex-column">
              <div class="rounded-4 overflow-hidden mb-3 formation-thumb">
                <div class="formation-category-badges" aria-label="Catégories">
                  <span class="badge rounded-pill formation-overlay-badge">Marketing</span>
                  <span class="badge rounded-pill formation-overlay-badge">Offre</span>
                </div>
                <div class="formation-meta-badges">
                  <span class="badge rounded-pill formation-overlay-badge">Vidéo</span>
                  <span class="badge rounded-pill formation-overlay-badge formation-duration-badge">1:00:00</span>
                </div>
                <div class="d-flex align-items-center justify-content-center text-center p-3">
                  <div>
                    <div class="display-6 fw-semibold mb-1">MK</div>
                    <div class="small text-uppercase formation-thumb-label">Aperçu formation</div>
                  </div>
                </div>
              </div>

              <h3 class="card-title h5 mb-2">Marketing - offre claire</h3>
              <p class="muted mb-3">
                Clarifier son offre pour mieux présenter ses services et attirer les bons clients.
              </p>

              <div class="d-flex gap-2 mt-auto">
                <button class="btn btn-ghost w-100" type="button" data-bs-toggle="modal" data-bs-target="#formationModal2">
                  Voir la formation
                </button>
                <a class="btn btn-accent w-100" href="view-user.php?formation_id=2">
                  Inscription
                </a>
              </div>
            </div>
          </article>
        </div>

        <!-- Formation 3 -->
        <div class="col-12 col-md-6 col-lg-4 formation-card-wrapper" data-categories="productivite,organisation" data-type="video">
          <article class="card h-100">
            <div class="card-body d-flex flex-column">
              <div class="rounded-4 overflow-hidden mb-3 formation-thumb">
                <div class="formation-category-badges" aria-label="Catégories">
                  <span class="badge rounded-pill formation-overlay-badge">Productivité</span>
                  <span class="badge rounded-pill formation-overlay-badge">Organisation</span>
                </div>
                <div class="formation-meta-badges">
                  <span class="badge rounded-pill formation-overlay-badge">Vidéo</span>
                  <span class="badge rounded-pill formation-overlay-badge formation-duration-badge">30:00</span>
                </div>
                <div class="d-flex align-items-center justify-content-center text-center p-3">
                  <div>
                    <div class="display-6 fw-semibold mb-1">P7</div>
                    <div class="small text-uppercase formation-thumb-label">Aperçu formation</div>
                  </div>
                </div>
              </div>

              <h3 class="card-title h5 mb-2">Productivité - plan 7 jours</h3>
              <p class="muted mb-3">
                Mettre en place un plan simple sur 7 jours pour mieux organiser ses priorités.
              </p>

              <div class="d-flex gap-2 mt-auto">
                <button class="btn btn-ghost w-100" type="button" data-bs-toggle="modal" data-bs-target="#formationModal3">
                  Voir la formation
                </button>
                <a class="btn btn-accent w-100" href="view-user.php?formation_id=3">
                  Inscription
                </a>
              </div>
            </div>
          </article>
        </div>

        <!-- Formation 4 -->
        <div class="col-12 col-md-6 col-lg-4 formation-card-wrapper" data-categories="marketing,seo,web" data-type="video">
          <article class="card h-100">
            <div class="card-body d-flex flex-column">
              <div class="rounded-4 overflow-hidden mb-3 formation-thumb">
                <div class="formation-category-badges" aria-label="Catégories">
                  <span class="badge rounded-pill formation-overlay-badge">Marketing</span>
                  <span class="badge rounded-pill formation-overlay-badge">SEO</span>
                  <span class="badge rounded-pill formation-overlay-badge">Web</span>
                </div>
                <div class="formation-meta-badges">
                  <span class="badge rounded-pill formation-overlay-badge">Vidéo</span>
                  <span class="badge rounded-pill formation-overlay-badge formation-duration-badge">50:00</span>
                </div>
                <div class="d-flex align-items-center justify-content-center text-center p-3">
                  <div>
                    <div class="display-6 fw-semibold mb-1">SEO</div>
                    <div class="small text-uppercase formation-thumb-label">Aperçu formation</div>
                  </div>
                </div>
              </div>

              <h3 class="card-title h5 mb-2">SEO - bases solides</h3>
              <p class="muted mb-3">
                Comprendre les bases du référencement naturel pour améliorer la visibilité d’un site Web.
              </p>

              <div class="d-flex gap-2 mt-auto">
                <button class="btn btn-ghost w-100" type="button" data-bs-toggle="modal" data-bs-target="#formationModal4">
                  Voir la formation
                </button>
                <a class="btn btn-accent w-100" href="view-user.php?formation_id=4">
                  Inscription
                </a>
              </div>
            </div>
          </article>
        </div>

        <!-- Formation 5 -->
        <div class="col-12 col-md-6 col-lg-4 formation-card-wrapper" data-categories="marketing,emailing" data-type="video">
          <article class="card h-100">
            <div class="card-body d-flex flex-column">
              <div class="rounded-4 overflow-hidden mb-3 formation-thumb">
                <div class="formation-category-badges" aria-label="Catégories">
                  <span class="badge rounded-pill formation-overlay-badge">Marketing</span>
                  <span class="badge rounded-pill formation-overlay-badge">Emailing</span>
                </div>
                <div class="formation-meta-badges">
                  <span class="badge rounded-pill formation-overlay-badge">Vidéo</span>
                  <span class="badge rounded-pill formation-overlay-badge formation-duration-badge">40:00</span>
                </div>
                <div class="d-flex align-items-center justify-content-center text-center p-3">
                  <div>
                    <div class="display-6 fw-semibold mb-1">EM</div>
                    <div class="small text-uppercase formation-thumb-label">Aperçu formation</div>
                  </div>
                </div>
              </div>

              <h3 class="card-title h5 mb-2">Emailing - newsletter simple</h3>
              <p class="muted mb-3">
                Créer une newsletter simple pour communiquer régulièrement avec son audience.
              </p>

              <div class="d-flex gap-2 mt-auto">
                <button class="btn btn-ghost w-100" type="button" data-bs-toggle="modal" data-bs-target="#formationModal5">
                  Voir la formation
                </button>
                <a class="btn btn-accent w-100" href="view-user.php?formation_id=5">
                  Inscription
                </a>
              </div>
            </div>
          </article>
        </div>

        <!-- Formation 6 -->
        <div class="col-12 col-md-6 col-lg-4 formation-card-wrapper" data-categories="web,bootstrap" data-type="playlist">
          <article class="card h-100">
            <div class="card-body d-flex flex-column">
              <div class="rounded-4 overflow-hidden mb-3 formation-thumb">
                <div class="formation-category-badges" aria-label="Catégories">
                  <span class="badge rounded-pill formation-overlay-badge">Web</span>
                  <span class="badge rounded-pill formation-overlay-badge">Bootstrap</span>
                </div>
                <div class="formation-meta-badges">
                  <span class="badge rounded-pill formation-overlay-badge">Playlist</span>
                  <span class="badge rounded-pill formation-overlay-badge formation-duration-badge">2:00:00</span>
                </div>
                <div class="d-flex align-items-center justify-content-center text-center p-3">
                  <div>
                    <div class="display-6 fw-semibold mb-1">BA</div>
                    <div class="small text-uppercase formation-thumb-label">Aperçu formation</div>
                  </div>
                </div>
              </div>

              <h3 class="card-title h5 mb-2">Bootstrap avancé</h3>
              <p class="muted mb-3">
                Aller plus loin avec Bootstrap pour créer des interfaces plus complètes.
              </p>

              <div class="d-flex gap-2 mt-auto">
                <button class="btn btn-ghost w-100" type="button" data-bs-toggle="modal" data-bs-target="#formationModal6">
                  Voir la formation
                </button>
                <a class="btn btn-accent w-100" href="view-user.php?formation_id=6">
                  Inscription
                </a>
              </div>
            </div>
          </article>
        </div>
      </div>

      <div class="alert alert-info mt-4 d-none" id="noResultsMessage" role="status">
        Aucune formation ne correspond aux filtres sélectionnés.
      </div>
    </section>

    <!-- Modals détails formations -->
    <div class="modal fade" id="formationModal1" tabindex="-1" aria-labelledby="formationModalLabel1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h2 class="modal-title h5" id="formationModalLabel1">Bootstrap v2 - pages efficaces</h2>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
          </div>
          <div class="modal-body">
            <div class="d-flex flex-wrap gap-2 mb-3">
              <span class="badge text-bg-light">Web, Bootstrap</span>
              <span class="badge text-bg-light">45 min</span>
              <span class="badge text-bg-light">Vidéo</span>
            </div>
            <p>
              Cette formation présente les bases de Bootstrap pour construire rapidement des pages Web efficaces.
              Elle couvre la grille, les cartes, les boutons, la navigation et la mise en page responsive.
            </p>
            <p class="mb-0 muted">
              Public cible : débutants en développement Web, étudiants et solopreneurs qui veulent créer des pages simples et professionnelles.
            </p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-ghost" data-bs-dismiss="modal">Fermer</button>
            <a class="btn btn-accent" href="view-user.php?formation_id=1">Inscription</a>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="formationModal2" tabindex="-1" aria-labelledby="formationModalLabel2" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h2 class="modal-title h5" id="formationModalLabel2">Marketing - offre claire</h2>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
          </div>
          <div class="modal-body">
            <div class="d-flex flex-wrap gap-2 mb-3">
              <span class="badge text-bg-light">Marketing, Offre</span>
              <span class="badge text-bg-light">60 min</span>
              <span class="badge text-bg-light">Vidéo</span>
            </div>
            <p>
              Cette formation aide à structurer une offre claire, simple et compréhensible. Elle aborde le positionnement,
              le message principal, la promesse et la présentation d’une offre destinée à des clients potentiels.
            </p>
            <p class="mb-0 muted">
              Public cible : solopreneurs, freelances, formateurs indépendants et entrepreneurs qui veulent mieux présenter leur offre.
            </p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-ghost" data-bs-dismiss="modal">Fermer</button>
            <a class="btn btn-accent" href="view-user.php?formation_id=2">Inscription</a>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="formationModal3" tabindex="-1" aria-labelledby="formationModalLabel3" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h2 class="modal-title h5" id="formationModalLabel3">Productivité - plan 7 jours</h2>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
          </div>
          <div class="modal-body">
            <div class="d-flex flex-wrap gap-2 mb-3">
              <span class="badge text-bg-light">Productivité, Organisation</span>
              <span class="badge text-bg-light">30 min</span>
              <span class="badge text-bg-light">Vidéo</span>
            </div>
            <p>
              Cette formation propose une méthode progressive sur 7 jours pour organiser ses tâches, prioriser ses actions
              et améliorer sa productivité personnelle sans complexifier son quotidien.
            </p>
            <p class="mb-0 muted">
              Public cible : personnes qui veulent mieux s’organiser, étudiants, travailleurs autonomes et solopreneurs.
            </p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-ghost" data-bs-dismiss="modal">Fermer</button>
            <a class="btn btn-accent" href="view-user.php?formation_id=3">Inscription</a>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="formationModal4" tabindex="-1" aria-labelledby="formationModalLabel4" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h2 class="modal-title h5" id="formationModalLabel4">SEO - bases solides</h2>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
          </div>
          <div class="modal-body">
            <div class="d-flex flex-wrap gap-2 mb-3">
              <span class="badge text-bg-light">Marketing, SEO, Web</span>
              <span class="badge text-bg-light">50 min</span>
              <span class="badge text-bg-light">Vidéo</span>
            </div>
            <p>
              Cette formation introduit les principes essentiels du SEO : mots-clés, structure des pages, titres,
              descriptions, contenu utile et bonnes pratiques pour aider un site à être mieux compris par les moteurs de recherche.
            </p>
            <p class="mb-0 muted">
              Public cible : débutants, créateurs de sites Web, solopreneurs et propriétaires de petits projets Web.
            </p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-ghost" data-bs-dismiss="modal">Fermer</button>
            <a class="btn btn-accent" href="view-user.php?formation_id=4">Inscription</a>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="formationModal5" tabindex="-1" aria-labelledby="formationModalLabel5" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h2 class="modal-title h5" id="formationModalLabel5">Emailing - newsletter simple</h2>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
          </div>
          <div class="modal-body">
            <div class="d-flex flex-wrap gap-2 mb-3">
              <span class="badge text-bg-light">Marketing, Emailing</span>
              <span class="badge text-bg-light">40 min</span>
              <span class="badge text-bg-light">Vidéo</span>
            </div>
            <p>
              Cette formation explique comment préparer une newsletter claire : objectif du courriel, structure du message,
              fréquence d’envoi, appel à l’action et bonnes pratiques pour garder une communication simple et utile.
            </p>
            <p class="mb-0 muted">
              Public cible : solopreneurs, créateurs de contenu, formateurs et petites entreprises qui veulent démarrer une infolettre.
            </p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-ghost" data-bs-dismiss="modal">Fermer</button>
            <a class="btn btn-accent" href="view-user.php?formation_id=5">Inscription</a>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="formationModal6" tabindex="-1" aria-labelledby="formationModalLabel6" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h2 class="modal-title h5" id="formationModalLabel6">Bootstrap avancé</h2>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
          </div>
          <div class="modal-body">
            <div class="d-flex flex-wrap gap-2 mb-3">
              <span class="badge text-bg-light">Web, Bootstrap</span>
              <span class="badge text-bg-light">120 min</span>
              <span class="badge text-bg-light">Playlist</span>
            </div>
            <p>
              Cette formation approfondit l’utilisation de Bootstrap avec des composants plus avancés, des mises en page plus riches,
              des formulaires améliorés et une meilleure organisation visuelle des interfaces.
            </p>
            <p class="mb-0 muted">
              Public cible : étudiants et développeurs débutants qui connaissent déjà les bases de Bootstrap.
            </p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-ghost" data-bs-dismiss="modal">Fermer</button>
            <a class="btn btn-accent" href="view-user.php?formation_id=6">Inscription</a>
          </div>
        </div>
      </div>
    </div>

    <!-- FOOTER -->
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
        const categories = card.dataset.categories.split(",");
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

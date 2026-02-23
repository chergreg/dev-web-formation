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
        <!-- 1 -->
        <div class="col-12 col-md-6 col-lg-4">
          <article class="card h-100">
            <div class="card-body">
              <div class="yt-thumb mb-3">
                <div class="yt-play" aria-hidden="true">
                  <span>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="white" xmlns="http://www.w3.org/2000/svg">
                      <path d="M8 5v14l11-7L8 5z"/>
                    </svg>
                  </span>
                </div>
              </div>
              <h3 class="card-title h5 mb-2">Lancer son offre en 7 jours</h3>
              <p class="muted mb-3">Un plan clair pour définir ta promesse, ton prix et ton premier tunnel simple.</p>
              <div class="d-flex gap-2">
                <a class="btn btn-accent w-100" href="view-user.html">Inscription</a>
                <a class="btn btn-ghost" href="#" aria-disabled="true">Aperçu</a>
              </div>
            </div>
          </article>
        </div>

        <!-- 2 -->
        <div class="col-12 col-md-6 col-lg-4">
          <article class="card h-100">
            <div class="card-body">
              <div class="yt-thumb mb-3">
                <div class="yt-play" aria-hidden="true">
                  <span>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="white" xmlns="http://www.w3.org/2000/svg">
                      <path d="M8 5v14l11-7L8 5z"/>
                    </svg>
                  </span>
                </div>
              </div>
              <h3 class="card-title h5 mb-2">YouTube pour formateurs</h3>
              <p class="muted mb-3">Script, structure et miniatures : produire des vidéos utiles sans y passer ta vie.</p>
              <div class="d-flex gap-2">
                <a class="btn btn-accent w-100" href="view-user.html">Inscription</a>
                <a class="btn btn-ghost" href="#" aria-disabled="true">Aperçu</a>
              </div>
            </div>
          </article>
        </div>

        <!-- 3 -->
        <div class="col-12 col-md-6 col-lg-4">
          <article class="card h-100">
            <div class="card-body">
              <div class="yt-thumb mb-3">
                <div class="yt-play" aria-hidden="true">
                  <span>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="white" xmlns="http://www.w3.org/2000/svg">
                      <path d="M8 5v14l11-7L8 5z"/>
                    </svg>
                  </span>
                </div>
              </div>
              <h3 class="card-title h5 mb-2">Branding minimaliste</h3>
              <p class="muted mb-3">Couleurs, typo, ton : créer une identité cohérente et simple à appliquer partout.</p>
              <div class="d-flex gap-2">
                <a class="btn btn-accent w-100" href="view-user.html">Inscription</a>
                <a class="btn btn-ghost" href="#" aria-disabled="true">Aperçu</a>
              </div>
            </div>
          </article>
        </div>

        <!-- 4 -->
        <div class="col-12 col-md-6 col-lg-4">
          <article class="card h-100">
            <div class="card-body">
              <div class="yt-thumb mb-3">
                <div class="yt-play" aria-hidden="true">
                  <span>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="white" xmlns="http://www.w3.org/2000/svg">
                      <path d="M8 5v14l11-7L8 5z"/>
                    </svg>
                  </span>
                </div>
              </div>
              <h3 class="card-title h5 mb-2">Pages web efficaces (Bootstrap)</h3>
              <p class="muted mb-3">Grille, composants, bonnes pratiques : construire vite des pages propres et responsive.</p>
              <div class="d-flex gap-2">
                <a class="btn btn-accent w-100" href="view-user.html">Inscription</a>
                <a class="btn btn-ghost" href="#" aria-disabled="true">Aperçu</a>
              </div>
            </div>
          </article>
        </div>

        <!-- 5 -->
        <div class="col-12 col-md-6 col-lg-4">
          <article class="card h-100">
            <div class="card-body">
              <div class="yt-thumb mb-3">
                <div class="yt-play" aria-hidden="true">
                  <span>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="white" xmlns="http://www.w3.org/2000/svg">
                      <path d="M8 5v14l11-7L8 5z"/>
                    </svg>
                  </span>
                </div>
              </div>
              <h3 class="card-title h5 mb-2">Email & conversion</h3>
              <p class="muted mb-3">Créer une séquence simple (welcome + valeur) pour transformer des vues en inscrits.</p>
              <div class="d-flex gap-2">
                <a class="btn btn-accent w-100" href="view-user.html">Inscription</a>
                <a class="btn btn-ghost" href="#" aria-disabled="true">Aperçu</a>
              </div>
            </div>
          </article>
        </div>

        <!-- 6 -->
        <div class="col-12 col-md-6 col-lg-4">
          <article class="card h-100">
            <div class="card-body">
              <div class="yt-thumb mb-3">
                <div class="yt-play" aria-hidden="true">
                  <span>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="white" xmlns="http://www.w3.org/2000/svg">
                      <path d="M8 5v14l11-7L8 5z"/>
                    </svg>
                  </span>
                </div>
              </div>
              <h3 class="card-title h5 mb-2">Organisation & productivité</h3>
              <p class="muted mb-3">Systèmes légers pour planifier, publier et livrer sans te surcharger (solo-friendly).</p>
              <div class="d-flex gap-2">
                <a class="btn btn-accent w-100" href="view-user.html">Inscription</a>
                <a class="btn btn-ghost" href="#" aria-disabled="true">Aperçu</a>
              </div>
            </div>
          </article>
        </div>
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

<!doctype html>
<html lang="fr">
<head>
  <?php include __DIR__ . '/partials/_head.php'; ?>
  <title>Web Formations — Admin</title>
</head>

<body>
  <!-- NAVBAR -->
  <?php include __DIR__ . '/partials/_navbar.php'; ?>

  <main class="container my-4 my-lg-5">
    <!-- HEADER -->
    <section class="panel p-4 p-lg-5 mb-4">
      <div class="row g-4 align-items-center">
        <div class="col-lg-8">
          <h1 class="h3 fw-semibold mb-2">Administration</h1>
          <p class="muted mb-3">
            Gère les <strong>formations</strong> (ajout / modification / suppression) et les <strong>inscriptions</strong>
            (informations de contact). Tout est stocké <strong>localement</strong> (localStorage), donc <strong>aucune base de données</strong>.
          </p>
          <div class="d-flex flex-wrap gap-2">
            <button class="btn btn-accent" data-bs-toggle="modal" data-bs-target="#modalAddFormation">
              + Ajouter une formation
            </button>
            <button class="btn btn-ghost" id="btnResetDemo">
              Réinitialiser la démo
            </button>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="help p-3">
            <div class="fw-semibold mb-1">Note</div>
            <div class="muted small">
              Les données restent sur <em>ton</em> navigateur. Sur un autre PC/navigateur, la liste sera différente.
              Pour une vraie sauvegarde multi-utilisateurs, il faudrait un backend + base de données.
            </div>
          </div>
        </div>
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
                  <!-- JS -->
                </tbody>
              </table>
            </div>

            <div id="formationsEmpty" class="muted small mt-3 d-none">
              Aucune formation. Clique sur “Ajouter une formation”.
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
                <button class="btn btn-ghost w-50" id="btnExportJson">Exporter JSON</button>
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
                  <!-- JS -->
                </tbody>
              </table>
            </div>

            <div id="inscriptionsEmpty" class="muted small mt-3 d-none">
              Aucune inscription (démo). Tu peux en simuler via view-user.html plus tard.
            </div>
          </div>
        </section>
      </div>
    </div>

    <?php include __DIR__ . '/partials/_footer.php'; ?>
  </main>

  <!-- MODAL: Add Formation -->
  <div class="modal fade" id="modalAddFormation" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header border-0">
          <h5 class="modal-title">Ajouter une formation</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
        </div>
        <div class="modal-body">
          <form id="formAddFormation" class="row g-3">
            <div class="col-12">
              <label class="form-label">Titre</label>
              <input class="form-control" id="addTitle" required maxlength="80" placeholder="Ex: Bootstrap — pages efficaces">
            </div>
            <div class="col-12">
              <label class="form-label">Description</label>
              <textarea class="form-control" id="addDesc" required rows="3" maxlength="220" placeholder="Résumé court (max ~220 caractères)"></textarea>
            </div>
            <div class="col-12">
              <label class="form-label">Vignette (optionnel)</label>
              <input class="form-control" id="addThumb" placeholder="URL d'image (facultatif)">
              <div class="muted small mt-1">Tu peux laisser vide : une vignette “YouTube-style” sera affichée par défaut.</div>
            </div>
            <div class="col-12 d-flex gap-2 justify-content-end pt-2">
              <button type="button" class="btn btn-ghost" data-bs-dismiss="modal">Annuler</button>
              <button type="submit" class="btn btn-accent">Ajouter</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- MODAL: Edit Formation -->
  <div class="modal fade" id="modalEditFormation" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header border-0">
          <h5 class="modal-title">Modifier la formation</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
        </div>
        <div class="modal-body">
          <form id="formEditFormation" class="row g-3">
            <input type="hidden" id="editId">
            <div class="col-12">
              <label class="form-label">Titre</label>
              <input class="form-control" id="editTitle" required maxlength="80">
            </div>
            <div class="col-12">
              <label class="form-label">Description</label>
              <textarea class="form-control" id="editDesc" required rows="3" maxlength="220"></textarea>
            </div>
            <div class="col-12">
              <label class="form-label">Vignette (optionnel)</label>
              <input class="form-control" id="editThumb" placeholder="URL d'image (facultatif)">
            </div>
            <div class="col-12 d-flex gap-2 justify-content-end pt-2">
              <button type="button" class="btn btn-ghost" data-bs-dismiss="modal">Annuler</button>
              <button type="submit" class="btn btn-accent">Enregistrer</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- MODAL: Confirm Delete -->
  <div class="modal fade" id="modalConfirm" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header border-0">
          <h5 class="modal-title" id="confirmTitle">Confirmation</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
        </div>
        <div class="modal-body muted" id="confirmBody">Êtes-vous sûr ?</div>
        <div class="modal-footer border-0">
          <button type="button" class="btn btn-ghost" data-bs-dismiss="modal">Annuler</button>
          <button type="button" class="btn btn-danger-soft" id="confirmYes">Supprimer</button>
        </div>
      </div>
    </div>
  </div>

  <!-- MODAL: Export JSON -->
  <div class="modal fade" id="modalExport" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header border-0">
          <h5 class="modal-title">Export JSON (inscriptions)</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
        </div>
        <div class="modal-body">
          <textarea id="exportArea" class="form-control" rows="10" readonly></textarea>
          <div class="muted small mt-2">Copie/colle ce JSON si tu veux le sauvegarder ailleurs.</div>
        </div>
        <div class="modal-footer border-0">
          <button class="btn btn-ghost" data-bs-dismiss="modal">Fermer</button>
          <button class="btn btn-accent" id="btnCopyJson">Copier</button>
        </div>
      </div>
    </div>
  </div>

  <?php include __DIR__ . '/partials/_libjs.php'; ?>

  <script>
    // -------------------------
    // LocalStorage keys
    // -------------------------
    const K_FORMATIONS = "wf_formations_v1";
    const K_INSCRIPTIONS = "wf_inscriptions_v1";

    // -------------------------
    // Demo seed data
    // -------------------------
    const seedFormations = [
      { id: crypto.randomUUID(), title: "Lancer son offre en 7 jours", desc: "Promesse, prix, tunnel simple, et plan d’action clair.", thumb: "", createdAt: Date.now()-86400000*6 },
      { id: crypto.randomUUID(), title: "YouTube pour formateurs", desc: "Structure, script et miniatures pour publier efficacement.", thumb: "", createdAt: Date.now()-86400000*5 },
      { id: crypto.randomUUID(), title: "Branding minimaliste", desc: "Couleurs, typo, ton : une identité simple et cohérente.", thumb: "", createdAt: Date.now()-86400000*4 },
      { id: crypto.randomUUID(), title: "Pages web efficaces (Bootstrap)", desc: "Grille, composants, responsive : construire vite et propre.", thumb: "", createdAt: Date.now()-86400000*3 },
      { id: crypto.randomUUID(), title: "Email & conversion", desc: "Séquence simple pour transformer des vues en inscrits.", thumb: "", createdAt: Date.now()-86400000*2 },
      { id: crypto.randomUUID(), title: "Organisation & productivité", desc: "Systèmes légers pour planifier, publier, livrer sans stress.", thumb: "", createdAt: Date.now()-86400000*1 },
    ];

    const seedInscriptions = [
      { id: crypto.randomUUID(), name: "Alex Martin", email: "alex@example.com", phone: "514-555-0101", formationTitle: "YouTube pour formateurs", status: "new", createdAt: Date.now()-86400000*2 },
      { id: crypto.randomUUID(), name: "Sam Nguyen", email: "sam@example.com", phone: "438-555-0199", formationTitle: "Pages web efficaces (Bootstrap)", status: "contacted", createdAt: Date.now()-86400000*1 },
      { id: crypto.randomUUID(), name: "Maya Dupont", email: "maya@example.com", phone: "514-555-0112", formationTitle: "Lancer son offre en 7 jours", status: "archived", createdAt: Date.now()-3600000*10 },
    ];

    // -------------------------
    // Helpers
    // -------------------------
    const $ = (s) => document.querySelector(s);
    const fmtDate = (ts) => new Date(ts).toLocaleDateString("fr-CA", { year:"numeric", month:"short", day:"2-digit" });

    function load(key, fallback){
      try{
        const raw = localStorage.getItem(key);
        return raw ? JSON.parse(raw) : fallback;
      }catch(e){
        return fallback;
      }
    }
    function save(key, value){
      localStorage.setItem(key, JSON.stringify(value));
    }

    function ensureSeed(){
      const f = load(K_FORMATIONS, null);
      const i = load(K_INSCRIPTIONS, null);
      if(!f) save(K_FORMATIONS, seedFormations);
      if(!i) save(K_INSCRIPTIONS, seedInscriptions);
    }

    function ytThumbMini(){
      return `
        <div class="d-inline-flex align-items-center justify-content-center"
             style="width:92px;height:52px;border-radius:.6rem;border:1px solid var(--border);
                    background:linear-gradient(135deg, rgba(255,255,255,.06), rgba(255,255,255,.02));">
          <span style="width:24px;height:24px;border-radius:999px;background:rgba(0,0,0,.35);
                       border:1px solid rgba(255,255,255,.16);display:grid;place-items:center;">
            <svg width="10" height="10" viewBox="0 0 24 24" fill="white" xmlns="http://www.w3.org/2000/svg">
              <path d="M8 5v14l11-7L8 5z"/>
            </svg>
          </span>
        </div>
      `;
    }

    // -------------------------
    // Rendering
    // -------------------------
    function renderFormations(){
      const q = $("#searchFormations").value.trim().toLowerCase();
      const sort = $("#sortFormations").value;

      let formations = load(K_FORMATIONS, []);
      // filter
      if(q){
        formations = formations.filter(f =>
          (f.title || "").toLowerCase().includes(q) ||
          (f.desc || "").toLowerCase().includes(q)
        );
      }
      // sort
      formations.sort((a,b)=>{
        if(sort === "title-asc") return (a.title||"").localeCompare(b.title||"", "fr");
        if(sort === "title-desc") return (b.title||"").localeCompare(a.title||"", "fr");
        if(sort === "newest") return (b.createdAt||0) - (a.createdAt||0);
        if(sort === "oldest") return (a.createdAt||0) - (b.createdAt||0);
        return 0;
      });

      const tbody = $("#formationsTbody");
      tbody.innerHTML = "";

      $("#formationsEmpty").classList.toggle("d-none", formations.length !== 0);

      formations.forEach(f=>{
        const tr = document.createElement("tr");

        tr.innerHTML = `
          <td style="min-width: 280px;">
            <div class="fw-semibold">${escapeHtml(f.title)}</div>
            <div class="muted small text-truncate" style="max-width: 420px;">${escapeHtml(f.desc)}</div>
          </td>
          <td class="text-nowrap">
            ${f.thumb ? `<img src="${escapeAttr(f.thumb)}" alt="" style="width:92px;height:52px;object-fit:cover;border-radius:.6rem;border:1px solid var(--border);">`
                      : ytThumbMini()}
          </td>
          <td class="text-nowrap muted small">${fmtDate(f.createdAt || Date.now())}</td>
          <td class="text-end text-nowrap">
            <button class="btn btn-ghost btn-sm me-2" data-action="edit" data-id="${f.id}">Modifier</button>
            <button class="btn btn-danger-soft btn-sm" data-action="delete" data-id="${f.id}">Supprimer</button>
          </td>
        `;
        tbody.appendChild(tr);
      });
    }

    function renderInscriptions(){
      const q = $("#searchInscriptions").value.trim().toLowerCase();
      const statusFilter = $("#filterStatus").value;

      let ins = load(K_INSCRIPTIONS, []);
      // filter
      if(statusFilter !== "all"){
        ins = ins.filter(x => x.status === statusFilter);
      }
      if(q){
        ins = ins.filter(x =>
          (x.name||"").toLowerCase().includes(q) ||
          (x.email||"").toLowerCase().includes(q) ||
          (x.formationTitle||"").toLowerCase().includes(q)
        );
      }

      // newest first
      ins.sort((a,b)=> (b.createdAt||0) - (a.createdAt||0));

      const tbody = $("#inscriptionsTbody");
      tbody.innerHTML = "";

      $("#inscriptionsEmpty").classList.toggle("d-none", ins.length !== 0);

      ins.forEach(x=>{
        const badge = statusBadge(x.status);
        const tr = document.createElement("tr");
        tr.innerHTML = `
          <td style="min-width: 260px;">
            <div class="fw-semibold">${escapeHtml(x.name)}</div>
            <div class="muted small">${escapeHtml(x.email)}${x.phone ? " • " + escapeHtml(x.phone) : ""}</div>
            <div class="muted small">Formation: <span class="text-white-50">${escapeHtml(x.formationTitle)}</span></div>
            <div class="muted small">Reçu: ${fmtDate(x.createdAt || Date.now())}</div>
          </td>
          <td class="text-nowrap">${badge}</td>
          <td class="text-end text-nowrap">
            <div class="d-flex justify-content-end gap-2 flex-wrap">
              <select class="form-select form-select-sm" style="width: 150px;" data-action="status" data-id="${x.id}">
                <option value="new" ${x.status==="new"?"selected":""}>Nouveau</option>
                <option value="contacted" ${x.status==="contacted"?"selected":""}>Contacté</option>
                <option value="archived" ${x.status==="archived"?"selected":""}>Archivé</option>
              </select>
              <button class="btn btn-danger-soft btn-sm" data-action="del-ins" data-id="${x.id}">Supprimer</button>
            </div>
          </td>
        `;
        tbody.appendChild(tr);
      });
    }

    function statusBadge(status){
      if(status === "new") return `<span class="badge rounded-pill" style="background: rgba(34,197,94,.18); border:1px solid rgba(34,197,94,.35);">Nouveau</span>`;
      if(status === "contacted") return `<span class="badge rounded-pill" style="background: rgba(245,158,11,.18); border:1px solid rgba(245,158,11,.35);">Contacté</span>`;
      if(status === "archived") return `<span class="badge rounded-pill" style="background: rgba(148,163,184,.16); border:1px solid rgba(148,163,184,.30);">Archivé</span>`;
      return `<span class="badge rounded-pill text-bg-secondary">—</span>`;
    }

    // -------------------------
    // Actions: Formations
    // -------------------------
    function addFormation(e){
      e.preventDefault();
      const formations = load(K_FORMATIONS, []);

      formations.push({
        id: crypto.randomUUID(),
        title: $("#addTitle").value.trim(),
        desc: $("#addDesc").value.trim(),
        thumb: $("#addThumb").value.trim(),
        createdAt: Date.now()
      });

      save(K_FORMATIONS, formations);
      $("#formAddFormation").reset();
      bootstrap.Modal.getInstance($("#modalAddFormation")).hide();
      renderFormations();
    }

    function openEditFormation(id){
      const formations = load(K_FORMATIONS, []);
      const f = formations.find(x => x.id === id);
      if(!f) return;

      $("#editId").value = f.id;
      $("#editTitle").value = f.title || "";
      $("#editDesc").value = f.desc || "";
      $("#editThumb").value = f.thumb || "";

      new bootstrap.Modal($("#modalEditFormation")).show();
    }

    function saveEditFormation(e){
      e.preventDefault();
      const id = $("#editId").value;
      const formations = load(K_FORMATIONS, []);
      const idx = formations.findIndex(x => x.id === id);
      if(idx === -1) return;

      formations[idx].title = $("#editTitle").value.trim();
      formations[idx].desc  = $("#editDesc").value.trim();
      formations[idx].thumb = $("#editThumb").value.trim();

      save(K_FORMATIONS, formations);
      bootstrap.Modal.getInstance($("#modalEditFormation")).hide();
      renderFormations();
      // Update inscriptions formationTitle if the title changed (simple sync)
      syncInscriptionTitles();
      renderInscriptions();
    }

    function confirmDeleteFormation(id){
      $("#confirmTitle").textContent = "Supprimer la formation ?";
      $("#confirmBody").textContent = "Cette action supprime la formation. Les inscriptions existantes ne seront pas supprimées (mais garderont l’ancien titre).";
      $("#confirmYes").onclick = () => {
        const formations = load(K_FORMATIONS, []).filter(x => x.id !== id);
        save(K_FORMATIONS, formations);
        bootstrap.Modal.getInstance($("#modalConfirm")).hide();
        renderFormations();
      };
      new bootstrap.Modal($("#modalConfirm")).show();
    }

    // -------------------------
    // Actions: Inscriptions
    // -------------------------
    function changeInscriptionStatus(id, newStatus){
      const ins = load(K_INSCRIPTIONS, []);
      const idx = ins.findIndex(x => x.id === id);
      if(idx === -1) return;
      ins[idx].status = newStatus;
      save(K_INSCRIPTIONS, ins);
      renderInscriptions();
    }

    function confirmDeleteInscription(id){
      $("#confirmTitle").textContent = "Supprimer l’inscription ?";
      $("#confirmBody").textContent = "Cette action supprime le contact de la liste (localement).";
      $("#confirmYes").onclick = () => {
        const ins = load(K_INSCRIPTIONS, []).filter(x => x.id !== id);
        save(K_INSCRIPTIONS, ins);
        bootstrap.Modal.getInstance($("#modalConfirm")).hide();
        renderInscriptions();
      };
      new bootstrap.Modal($("#modalConfirm")).show();
    }

    function exportInscriptionsJson(){
      const ins = load(K_INSCRIPTIONS, []);
      $("#exportArea").value = JSON.stringify(ins, null, 2);
      new bootstrap.Modal($("#modalExport")).show();
    }

    function copyExport(){
      $("#exportArea").select();
      document.execCommand("copy");
    }

    function syncInscriptionTitles(){
      // Optionnel: si tu veux vraiment synchroniser, il faut une clé formationId côté inscriptions.
      // Ici, on ne peut pas matcher "proprement" sans ID. Donc on laisse tel quel.
      // (On garde la fonction au cas où tu ajoutes formationId plus tard.)
    }

    // -------------------------
    // Reset demo
    // -------------------------
    function resetDemo(){
      localStorage.removeItem(K_FORMATIONS);
      localStorage.removeItem(K_INSCRIPTIONS);
      ensureSeed();
      renderFormations();
      renderInscriptions();
    }

    // -------------------------
    // Safe escaping (basic)
    // -------------------------
    function escapeHtml(str){
      return String(str ?? "")
        .replaceAll("&", "&amp;")
        .replaceAll("<", "&lt;")
        .replaceAll(">", "&gt;")
        .replaceAll('"', "&quot;")
        .replaceAll("'", "&#039;");
    }
    function escapeAttr(str){
      return escapeHtml(str).replaceAll("`", "");
    }

    // -------------------------
    // Events
    // -------------------------
    document.addEventListener("click", (e)=>{
      const btn = e.target.closest("button[data-action]");
      if(!btn) return;

      const action = btn.dataset.action;
      const id = btn.dataset.id;

      if(action === "edit") openEditFormation(id);
      if(action === "delete") confirmDeleteFormation(id);
      if(action === "del-ins") confirmDeleteInscription(id);
    });

    document.addEventListener("change", (e)=>{
      const sel = e.target.closest("select[data-action='status']");
      if(!sel) return;
      changeInscriptionStatus(sel.dataset.id, sel.value);
    });

    $("#formAddFormation").addEventListener("submit", addFormation);
    $("#formEditFormation").addEventListener("submit", saveEditFormation);

    $("#searchFormations").addEventListener("input", renderFormations);
    $("#sortFormations").addEventListener("change", renderFormations);

    $("#searchInscriptions").addEventListener("input", renderInscriptions);
    $("#filterStatus").addEventListener("change", renderInscriptions);

    $("#btnExportJson").addEventListener("click", exportInscriptionsJson);
    $("#btnCopyJson").addEventListener("click", copyExport);

    $("#btnResetDemo").addEventListener("click", resetDemo);

    // Init
    document.getElementById("year").textContent = new Date().getFullYear();
    ensureSeed();
    renderFormations();
    renderInscriptions();
  </script>
</body>
</html>

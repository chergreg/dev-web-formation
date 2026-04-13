<?php

class FormationRepository
{
    private string $filePath;

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;

        // Si le fichier n'existe pas, on le crée avec un tableau vide
        if (!file_exists($this->filePath)) {
            file_put_contents($this->filePath, json_encode([], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }
    }

    /** Retourne toutes les formations */
    public function all(): array
    {
        return $this->readData();
    }

    /** Retourne une formation par son id (ou null si introuvable) */
    public function find(int $id): ?array
    {
        foreach ($this->readData() as $formation) {
            if (isset($formation['id']) && (int)$formation['id'] === $id) {
                return $formation;
            }
        }
        return null;
    }

    /**
     * Crée une formation.
     * @return array la formation créée (avec id)
     */
    public function create(array $data): array
    {
        $formations = $this->readData();

        $clean = $this->sanitizeFormation($data);
        $this->assertValidFormation($clean);

        $clean['id'] = $this->nextId($formations);

        $formations[] = $clean;
        $this->writeData($formations);

        return $clean;
    }

    /**
     * Met à jour une formation existante.
     * @return array la formation mise à jour
     * @throws RuntimeException si introuvable
     */
    public function update(int $id, array $data): array
    {
        $formations = $this->readData();

        $index = $this->findIndexById($formations, $id);
        if ($index === -1) {
            throw new RuntimeException("Formation introuvable (id=$id).");
        }

        // On repart de l'existant, et on écrase avec les champs fournis
        $existing = $formations[$index];
        $merged = array_merge($existing, $data);

        $clean = $this->sanitizeFormation($merged);
        $this->assertValidFormation($clean);

        // Sécurité: on conserve l'id
        $clean['id'] = $id;

        $formations[$index] = $clean;
        $this->writeData($formations);

        return $clean;
    }

    /**
     * Supprime une formation.
     * @return bool true si supprimée, false si introuvable
     */
    public function delete(int $id): bool
    {
        $formations = $this->readData();

        $index = $this->findIndexById($formations, $id);
        if ($index === -1) {
            return false;
        }

        array_splice($formations, $index, 1);
        $this->writeData($formations);

        return true;
    }

    /**
     * "Upsert": si id présent -> update, sinon -> create
     * pratique pour un formulaire admin.
     */
    public function save(array $data): array
    {
        $id = isset($data['id']) ? (int)$data['id'] : 0;

        if ($id > 0 && $this->find($id) !== null) {
            return $this->update($id, $data);
        }
        return $this->create($data);
    }

    // --------------------
    // Helpers internes
    // --------------------

    private function readData(): array
    {
        $json = @file_get_contents($this->filePath);
        if ($json === false || trim($json) === '') {
            return [];
        }

        $data = json_decode($json, true);
        return is_array($data) ? $data : [];
    }

    private function writeData(array $data): void
    {
        // (Optionnel) Tri par id croissant pour garder un fichier propre
        usort($data, fn($a, $b) => ((int)($a['id'] ?? 0)) <=> ((int)($b['id'] ?? 0)));

        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        if ($json === false) {
            throw new RuntimeException("Erreur d'encodage JSON.");
        }

        // Écriture atomique simple (évite un fichier vide si crash)
        $tmp = $this->filePath . '.tmp';
        if (file_put_contents($tmp, $json) === false) {
            throw new RuntimeException("Impossible d'écrire le fichier temporaire.");
        }
        rename($tmp, $this->filePath);
    }

    private function nextId(array $formations): int
    {
        $max = 0;
        foreach ($formations as $f) {
            $max = max($max, (int)($f['id'] ?? 0));
        }
        return $max + 1;
    }

    private function findIndexById(array $formations, int $id): int
    {
        foreach ($formations as $i => $formation) {
            if (isset($formation['id']) && (int)$formation['id'] === $id) {
                return $i;
            }
        }
        return -1;
    }

    /**
     * Normalise les champs (trim, types, valeurs par défaut).
     * Tu peux adapter les clés à ton formulaire.
     */
    private function sanitizeFormation(array $data): array
    {
        $out = [];

        // Champs "catalogue"
        $out['titre'] = trim((string)($data['titre'] ?? ''));
        $out['description'] = trim((string)($data['description'] ?? ''));
        $out['duree'] = trim((string)($data['duree'] ?? ''));          // ex: "2h30"
        $out['prix'] = (float)($data['prix'] ?? 0);                    // ex: 99.99
        $out['youtube_url'] = trim((string)($data['youtube_url'] ?? ''));
        $out['statut'] = trim((string)($data['statut'] ?? 'active'));  // active/inactive

        // optionnel : catégorie, niveau, etc.
        if (isset($data['categorie'])) {
            $out['categorie'] = trim((string)$data['categorie']);
        }

        return $out;
    }

    /**
     * Validation légère (PHP) — en plus de la validation HTML.
     * Garde ça simple pour débutant.
     */
    private function assertValidFormation(array $f): void
    {
        if ($f['titre'] === '') {
            throw new InvalidArgumentException("Le titre est obligatoire.");
        }
        if ($f['description'] === '') {
            throw new InvalidArgumentException("La description est obligatoire.");
        }
        if ($f['prix'] < 0) {
            throw new InvalidArgumentException("Le prix ne peut pas être négatif.");
        }
        if ($f['youtube_url'] !== '' && !filter_var($f['youtube_url'], FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException("L'URL YouTube n'est pas valide.");
        }
        if ($f['statut'] !== 'active' && $f['statut'] !== 'inactive') {
            throw new InvalidArgumentException("Le statut doit être 'active' ou 'inactive'.");
        }
    }
}
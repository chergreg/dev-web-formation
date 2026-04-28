<?php

declare(strict_types=1);

class FormationRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }

    /**
     * Retourne toutes les formations, peu importe leur statut.
     * Utile pour l'administration.
     */
    public function all(): array
    {
        $sql = 'SELECT ' . $this->columns() . ' FROM formations ORDER BY id ASC';
        $stmt = $this->pdo->query($sql);

        return $stmt->fetchAll();
    }

    /**
     * Retourne seulement les formations publiées.
     * Utile pour la page publique index.php.
     */
    public function allPublished(): array
    {
        $sql = 'SELECT ' . $this->columns() . '
                FROM formations
                WHERE statut = :statut
                ORDER BY id ASC';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['statut' => 'publie']);

        return $stmt->fetchAll();
    }

    /**
     * Retourne une formation par son id, ou null si elle n'existe pas.
     */
    public function find(int $id): ?array
    {
        $sql = 'SELECT ' . $this->columns() . '
                FROM formations
                WHERE id = :id
                LIMIT 1';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);

        $formation = $stmt->fetch();

        return $formation !== false ? $formation : null;
    }

    /**
     * Crée une formation dans la base de données.
     */
    public function create(array $data): array
    {
        $formation = $this->sanitizeFormation($data);
        $this->assertValidFormation($formation);

        $sql = 'INSERT INTO formations (
                    titre,
                    description_courte,
                    description_longue,
                    audience,
                    youtube_url,
                    image_url,
                    type_contenu,
                    duree_minutes,
                    nb_videos,
                    categories,
                    statut
                ) VALUES (
                    :titre,
                    :description_courte,
                    :description_longue,
                    :audience,
                    :youtube_url,
                    :image_url,
                    :type_contenu,
                    :duree_minutes,
                    :nb_videos,
                    :categories,
                    :statut
                )';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($formation);

        $id = (int)$this->pdo->lastInsertId();
        $created = $this->find($id);

        if ($created === null) {
            throw new RuntimeException('La formation a été créée, mais elle est introuvable ensuite.');
        }

        return $created;
    }

    /**
     * Met à jour une formation existante.
     */
    public function update(int $id, array $data): array
    {
        $existing = $this->find($id);

        if ($existing === null) {
            throw new RuntimeException("Formation introuvable (id={$id}).");
        }

        $formation = $this->sanitizeFormation(array_merge($existing, $data));
        $this->assertValidFormation($formation);

        $sql = 'UPDATE formations
                SET
                    titre = :titre,
                    description_courte = :description_courte,
                    description_longue = :description_longue,
                    audience = :audience,
                    youtube_url = :youtube_url,
                    image_url = :image_url,
                    type_contenu = :type_contenu,
                    duree_minutes = :duree_minutes,
                    nb_videos = :nb_videos,
                    categories = :categories,
                    statut = :statut
                WHERE id = :id';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array_merge($formation, ['id' => $id]));

        $updated = $this->find($id);

        if ($updated === null) {
            throw new RuntimeException('La formation a été modifiée, mais elle est introuvable ensuite.');
        }

        return $updated;
    }

    /**
     * Supprime une formation.
     */
    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM formations WHERE id = :id');
        $stmt->execute(['id' => $id]);

        return $stmt->rowCount() > 0;
    }

    /**
     * Si id présent et existant: update.
     * Sinon: create.
     */
    public function save(array $data): array
    {
        $id = isset($data['id']) ? (int)$data['id'] : 0;

        if ($id > 0 && $this->find($id) !== null) {
            return $this->update($id, $data);
        }

        return $this->create($data);
    }

    private function columns(): string
    {
        return 'id,
                titre,
                description_courte,
                description_longue,
                audience,
                youtube_url,
                image_url,
                type_contenu,
                duree_minutes,
                nb_videos,
                categories,
                statut,
                created_at';
    }

    /**
     * Normalise les champs selon la structure actuelle de ta table formations.
     */
    private function sanitizeFormation(array $data): array
    {
        $imageUrl = trim((string)($data['image_url'] ?? ''));
        $categories = trim((string)($data['categories'] ?? ''));

        return [
            'titre' => trim((string)($data['titre'] ?? '')),
            'description_courte' => trim((string)($data['description_courte'] ?? '')),
            'description_longue' => trim((string)($data['description_longue'] ?? '')),
            'audience' => trim((string)($data['audience'] ?? '')),
            'youtube_url' => trim((string)($data['youtube_url'] ?? '')),
            'image_url' => $imageUrl !== '' ? $imageUrl : null,
            'type_contenu' => trim((string)($data['type_contenu'] ?? 'video')),
            'duree_minutes' => max(0, (int)($data['duree_minutes'] ?? 0)),
            'nb_videos' => max(1, (int)($data['nb_videos'] ?? 1)),
            'categories' => $categories !== '' ? $categories : null,
            'statut' => trim((string)($data['statut'] ?? 'brouillon')),
        ];
    }

    /**
     * Validation serveur simple, en plus de la validation HTML.
     */
    private function assertValidFormation(array $formation): void
    {
        if ($formation['titre'] === '') {
            throw new InvalidArgumentException('Le titre est obligatoire.');
        }

        if ($formation['description_courte'] === '') {
            throw new InvalidArgumentException('La description courte est obligatoire.');
        }

        if ($formation['description_longue'] === '') {
            throw new InvalidArgumentException('La description longue est obligatoire.');
        }

        if ($formation['audience'] === '') {
            throw new InvalidArgumentException("L'audience est obligatoire.");
        }

        if ($formation['youtube_url'] === '') {
            throw new InvalidArgumentException("L'URL YouTube est obligatoire.");
        }

        if (!filter_var($formation['youtube_url'], FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException("L'URL YouTube n'est pas valide.");
        }

        if (!in_array($formation['type_contenu'], ['video', 'playlist'], true)) {
            throw new InvalidArgumentException("Le type de contenu doit être 'video' ou 'playlist'.");
        }

        if ($formation['duree_minutes'] <= 0) {
            throw new InvalidArgumentException('La durée doit être supérieure à 0 minute.');
        }

        if ($formation['nb_videos'] <= 0) {
            throw new InvalidArgumentException('Le nombre de vidéos doit être supérieur à 0.');
        }

        if (!in_array($formation['statut'], ['brouillon', 'publie', 'archive'], true)) {
            throw new InvalidArgumentException("Le statut doit être 'brouillon', 'publie' ou 'archive'.");
        }
    }
}

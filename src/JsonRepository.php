<?php

class JsonRepository
{
    private string $filePath;

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;

        // Si le fichier n'existe pas, on le crée avec un tableau vide
        if (!file_exists($this->filePath)) {
            file_put_contents($this->filePath, json_encode([], JSON_PRETTY_PRINT));
        }
    }

    /** Retourne toutes les données sous forme de tableau PHP */
    public function all(): array
    {
        $json = file_get_contents($this->filePath);
        $data = json_decode($json, true);

        return is_array($data) ? $data : [];
    }
}
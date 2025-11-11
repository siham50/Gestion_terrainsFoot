<?php
// classes/Prix.php

require_once __DIR__ . '/../config/database.php';

class Prix {
    private $db;

    public function __construct($database = null) {
        $this->db = $database ?: getDB();
    }

    /**
     * Récupérer le prix d'un terrain par son identifiant
     */
    public function getPrixByTerrainId($idTerrain) {
        $sql = "SELECT prix FROM terrain WHERE idTerrain = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$idTerrain]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ? $result['prix'] : null;
    }

    /**
     * Récupérer les prix pour une liste de terrains (renvoie un tableau associatif idTerrain => prix)
     */
    public function getPrixForTerrains(array $terrainIds) {
        if (empty($terrainIds)) {
            return [];
        }

        // Nettoyer et dédupliquer les identifiants
        $terrainIds = array_unique(array_filter(array_map('intval', $terrainIds))); // phpstan baseline: ensure ints
        if (empty($terrainIds)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($terrainIds), '?'));
        $sql = "SELECT idTerrain, prix FROM terrain WHERE idTerrain IN ($placeholders)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($terrainIds);

        $prices = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $prices[$row['idTerrain']] = $row['prix'];
        }

        return $prices;
    }

    /**
     * Compter le nombre de terrains possédant un prix défini
     */
    public function countTerrainsAvecPrix() {
        $sql = "SELECT COUNT(*) as total FROM terrain WHERE prix IS NOT NULL";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? (int)$result['total'] : 0;
    }
}

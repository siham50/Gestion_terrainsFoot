<?php
// classes/Terrain.php

require_once __DIR__ . '/../config/database.php';

class Terrain {
    private $conn;
    private $table_name = "terrain";

    public $idTerrain;
    public $nom;
    public $taille;
    public $type;
    public $disponible;
    public $photoT;
    public $creneaux_disponibles;
    public $prix_heure;

    public function __construct() {
        $this->conn = getDB();
    }

    // Lire tous les terrains
    public function readAll() {
        $query = "SELECT t.*, 
                         COUNT(r.idReservation) as reservations_count,
                         p.prix as prix_heure
                  FROM " . $this->table_name . " t
                  LEFT JOIN reservation r ON t.idTerrain = r.idTerrain 
                  LEFT JOIN prix p ON t.taille = p.categorie AND p.reference LIKE 'TERRAIN_%'
                  GROUP BY t.idTerrain
                  ORDER BY t.disponible DESC, t.nom ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt;
    }

    // Lire les terrains disponibles - VERSION SIMPLIFIÉE
public function readDisponibles() {
    try {
        $query = "SELECT t.*, 
                         p.prix as prix_heure,
                         (SELECT COUNT(*) FROM reservation r WHERE r.idTerrain = t.idTerrain AND r.dateReservation = CURDATE()) as reservations_aujourdhui,
                         (12 - (SELECT COUNT(*) FROM reservation r WHERE r.idTerrain = t.idTerrain AND r.dateReservation = CURDATE())) as creneaux_disponibles
                  FROM terrain t 
                  LEFT JOIN prix p ON t.taille = p.categorie 
                  WHERE t.disponible = 1 
                    AND (SELECT COUNT(*) FROM reservation r WHERE r.idTerrain = t.idTerrain AND r.dateReservation = CURDATE()) < 12
                  ORDER BY t.idTerrain DESC";  // ← Tri par date de modification
        

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt;
        
    } catch (Exception $e) {
        error_log("Erreur readDisponibles: " . $e->getMessage());
        return $this->conn->prepare("SELECT 0 as no_data");
    }
}

    // Lire les terrains indisponibles - VERSION SIMPLIFIÉE
public function readIndisponibles() {
    try {
        $query = "SELECT t.*, 
                         p.prix as prix_heure,
                         (SELECT COUNT(*) FROM reservation r WHERE r.idTerrain = t.idTerrain AND r.dateReservation = CURDATE()) as reservations_aujourdhui,
                         (12 - (SELECT COUNT(*) FROM reservation r WHERE r.idTerrain = t.idTerrain AND r.dateReservation = CURDATE())) as creneaux_disponibles
                  FROM terrain t 
                  LEFT JOIN prix p ON t.taille = p.categorie 
                  WHERE t.disponible = 0 
                     OR (SELECT COUNT(*) FROM reservation r WHERE r.idTerrain = t.idTerrain AND r.dateReservation = CURDATE()) >= 12
                  ORDER BY t.nom ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt;
        
    } catch (Exception $e) {
        error_log("Erreur readIndisponibles: " . $e->getMessage());
        return $this->conn->prepare("SELECT 0 as no_data");
    }
}

    // Mettre à jour la disponibilité
    public function updateDisponibilite($idTerrain, $disponible) {
        $query = "UPDATE " . $this->table_name . " 
                  SET disponible = :disponible 
                  WHERE idTerrain = :idTerrain";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':disponible', $disponible);
        $stmt->bindParam(':idTerrain', $idTerrain);
        
        return $stmt->execute();
    }

    // Ajouter un terrain
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET nom=:nom, taille=:taille, type=:type, disponible=:disponible, photoT=:photoT";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':nom', $this->nom);
        $stmt->bindParam(':taille', $this->taille);
        $stmt->bindParam(':type', $this->type);
        $stmt->bindParam(':disponible', $this->disponible);
        $stmt->bindParam(':photoT', $this->photoT);
        
        if($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    // Supprimer un terrain
    public function delete($idTerrain) {
        $query = "DELETE FROM " . $this->table_name . " WHERE idTerrain = :idTerrain";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':idTerrain', $idTerrain);
        
        return $stmt->execute();
    }

    // Obtenir les données pour AJAX - VERSION AMÉLIORÉE
    public function getDataForAjax() {
        try {
            $disponibles = $this->readDisponibles();
            $indisponibles = $this->readIndisponibles();
            
            $data = [
                'disponibles' => [],
                'indisponibles' => []
            ];
            
            // Vérifier si on a des données valides
            if ($disponibles) {
                while ($row = $disponibles->fetch(PDO::FETCH_ASSOC)) {
                    // Assurer que creneaux_disponibles est au moins 0
                    $row['creneaux_disponibles'] = max(0, $row['creneaux_disponibles'] ?? 0);
                    $data['disponibles'][] = $row;
                }
            }
            
            if ($indisponibles) {
                while ($row = $indisponibles->fetch(PDO::FETCH_ASSOC)) {
                    $row['creneaux_disponibles'] = max(0, $row['creneaux_disponibles'] ?? 0);
                    $data['indisponibles'][] = $row;
                }
            }
            
            return $data;
            
        } catch (Exception $e) {
            error_log("Erreur getDataForAjax: " . $e->getMessage());
            return [
                'disponibles' => [],
                'indisponibles' => []
            ];
        }
    }

    // NOUVELLE MÉTHODE : Test de connexion et données
    public function testConnection() {
        try {
            $test = dbFetchOne("SELECT COUNT(*) as total FROM terrain");
            $prix = dbFetchOne("SELECT COUNT(*) as total FROM prix");
            
            return [
                'success' => true,
                'terrains_count' => $test['total'],
                'prix_count' => $prix['total'],
                'terrains' => dbFetchAll("SELECT * FROM terrain LIMIT 5")
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
?>
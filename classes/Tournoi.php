<?php
// classes/Tournoi.php

require_once __DIR__ . '/../config/database.php';

class Tournoi {
    private $db;

    public function __construct($database = null) {
        // Accept PDO connection or use getDB() if not provided
        $this->db = $database ? $database : getDB();
    }

    /**
     * Créer un nouveau tournoi
     * @param array $data Données du tournoi (format, equipes, idUtilisateur)
     * @return int|false ID du tournoi créé ou false en cas d'erreur
     */
    public function createTournoi($data) {
        try {
            // Valider le format
            if (!$this->validateTournoiFormat($data['format'], $data['equipes'])) {
                return false;
            }

            $sql = "INSERT INTO tournoi (format, equipes, idUtilisateur) 
                    VALUES (?, ?, ?)";
            
            $stmt = $this->db->prepare($sql);
            $equipesJson = json_encode($data['equipes'], JSON_UNESCAPED_UNICODE);
            
            $stmt->execute([
                $data['format'],
                $equipesJson,
                $data['idUtilisateur']
            ]);
            
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Erreur création tournoi: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupérer un tournoi par son ID
     * @param int $idTournoi
     * @return array|false Données du tournoi ou false
     */
    public function getTournoiById($idTournoi) {
        try {
            $sql = "SELECT t.*, 
                           u.nom as organisateur_nom, 
                           u.prenom as organisateur_prenom,
                           u.email as organisateur_email
                    FROM tournoi t
                    LEFT JOIN utilisateur u ON t.idUtilisateur = u.idUtilisateur
                    WHERE t.idTournoi = ?";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$idTournoi]);
            $tournoi = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($tournoi) {
                // Décoder les équipes JSON
                $tournoi['equipes'] = json_decode($tournoi['equipes'], true) ?? [];
            }
            
            return $tournoi;
        } catch (PDOException $e) {
            error_log("Erreur récupération tournoi: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupérer tous les tournois
     * @return array Liste des tournois
     */
    public function getAllTournois() {
        try {
            $sql = "SELECT t.*, 
                           u.nom as organisateur_nom, 
                           u.prenom as organisateur_prenom,
                           COUNT(m.idMatch) as nombre_matchs
                    FROM tournoi t
                    LEFT JOIN utilisateur u ON t.idUtilisateur = u.idUtilisateur
                    LEFT JOIN matchs m ON t.idTournoi = m.idTournoi
                    GROUP BY t.idTournoi
                    ORDER BY t.idTournoi DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $tournois = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Décoder les équipes JSON pour chaque tournoi
            foreach ($tournois as &$tournoi) {
                $tournoi['equipes'] = json_decode($tournoi['equipes'], true) ?? [];
            }
            unset($tournoi);
            
            return $tournois;
        } catch (PDOException $e) {
            error_log("Erreur récupération tournois: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Récupérer les tournois d'un utilisateur
     * @param int $idUtilisateur
     * @return array Liste des tournois
     */
    public function getTournoisByUser($idUtilisateur) {
        try {
            $sql = "SELECT t.*, 
                           COUNT(m.idMatch) as nombre_matchs
                    FROM tournoi t
                    LEFT JOIN matchs m ON t.idTournoi = m.idTournoi
                    WHERE t.idUtilisateur = ?
                    GROUP BY t.idTournoi
                    ORDER BY t.idTournoi DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$idUtilisateur]);
            $tournois = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Décoder les équipes JSON
            foreach ($tournois as &$tournoi) {
                $tournoi['equipes'] = json_decode($tournoi['equipes'], true) ?? [];
            }
            unset($tournoi);
            
            return $tournois;
        } catch (PDOException $e) {
            error_log("Erreur récupération tournois utilisateur: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Mettre à jour un tournoi
     * @param int $idTournoi
     * @param array $data Données à mettre à jour
     * @return bool Succès de l'opération
     */
    public function updateTournoi($idTournoi, $data) {
        try {
            $updates = [];
            $params = [];
            
            if (isset($data['format'])) {
                $updates[] = "format = ?";
                $params[] = $data['format'];
            }
            
            if (isset($data['equipes'])) {
                // Valider le format si fourni
                if (isset($data['format']) && !$this->validateTournoiFormat($data['format'], $data['equipes'])) {
                    return false;
                } elseif (!isset($data['format'])) {
                    // Récupérer le format actuel
                    $current = $this->getTournoiById($idTournoi);
                    if ($current && !$this->validateTournoiFormat($current['format'], $data['equipes'])) {
                        return false;
                    }
                }
                
                $updates[] = "equipes = ?";
                $params[] = json_encode($data['equipes'], JSON_UNESCAPED_UNICODE);
            }
            
            if (isset($data['champion'])) {
                $updates[] = "champion = ?";
                $params[] = $data['champion'];
            }
            
            if (empty($updates)) {
                return false;
            }
            
            $params[] = $idTournoi;
            $sql = "UPDATE tournoi SET " . implode(", ", $updates) . " WHERE idTournoi = ?";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Erreur mise à jour tournoi: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Supprimer un tournoi
     * @param int $idTournoi
     * @return bool Succès de l'opération
     */
    public function deleteTournoi($idTournoi) {
        try {
            // Les matchs seront supprimés en cascade grâce à la FK
            $sql = "DELETE FROM tournoi WHERE idTournoi = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$idTournoi]);
        } catch (PDOException $e) {
            error_log("Erreur suppression tournoi: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupérer un tournoi avec tous ses matchs
     * @param int $idTournoi
     * @return array|false Données du tournoi avec matchs ou false
     */
    public function getTournoiWithMatches($idTournoi) {
        try {
            $tournoi = $this->getTournoiById($idTournoi);
            if (!$tournoi) {
                return false;
            }
            
            // Récupérer les matchs
            require_once __DIR__ . '/Match.php';
            $matchModel = new TournamentMatch($this->db);
            $tournoi['matchs'] = $matchModel->getMatchesByTournoi($idTournoi);
            
            return $tournoi;
        } catch (Exception $e) {
            error_log("Erreur récupération tournoi avec matchs: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtenir les statistiques d'un tournoi
     * @param int $idTournoi
     * @return array Statistiques
     */
    public function getTournoiStats($idTournoi) {
        try {
            $sql = "SELECT 
                        COUNT(m.idMatch) as total_matchs,
                        COUNT(CASE WHEN m.gagnant IS NOT NULL THEN 1 END) as matchs_termines,
                        COUNT(CASE WHEN m.gagnant IS NULL THEN 1 END) as matchs_a_venir,
                        COUNT(CASE WHEN m.nextMatchId IS NULL THEN 1 END) as matchs_finaux
                    FROM tournoi t
                    LEFT JOIN matchs m ON t.idTournoi = m.idTournoi
                    WHERE t.idTournoi = ?";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$idTournoi]);
            $stats = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $tournoi = $this->getTournoiById($idTournoi);
            if ($tournoi) {
                $stats['nombre_equipes'] = count($tournoi['equipes']);
                $stats['format'] = $tournoi['format'];
                $stats['champion'] = $tournoi['champion'];
            }
            
            return $stats;
        } catch (PDOException $e) {
            error_log("Erreur récupération stats tournoi: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Définir le champion d'un tournoi
     * @param int $idTournoi
     * @param string $equipe Nom de l'équipe championne
     * @return bool Succès de l'opération
     */
    public function setChampion($idTournoi, $equipe) {
        return $this->updateTournoi($idTournoi, ['champion' => $equipe]);
    }

    /**
     * Valider le format et le nombre d'équipes
     * @param string $format Format du tournoi (ex: "8 équipes", "16 équipes")
     * @param array $equipes Liste des équipes
     * @return bool True si valide
     */
    public function validateTournoiFormat($format, $equipes) {
        // Extraire le nombre d'équipes du format
        preg_match('/(\d+)/', $format, $matches);
        $nombreEquipesAttendu = isset($matches[1]) ? (int)$matches[1] : 0;
        
        // Formats supportés: 8 et 16 équipes uniquement
        if (!in_array($nombreEquipesAttendu, [8, 16])) {
            return false;
        }
        
        // Vérifier que le nombre d'équipes correspond
        $nombreEquipesReel = count($equipes);
        if ($nombreEquipesReel !== $nombreEquipesAttendu) {
            return false;
        }
        
        // Vérifier que les noms d'équipes sont uniques
        if (count($equipes) !== count(array_unique($equipes))) {
            return false;
        }
        
        // Vérifier que les équipes ne sont pas vides
        foreach ($equipes as $equipe) {
            if (empty(trim($equipe))) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Générer le bracket automatiquement pour un tournoi
     * @param int $idTournoi
     * @param array|null $equipes Liste des équipes (optionnel, récupéré depuis la BD si null)
     * @return bool|array True si succès, ou tableau d'erreurs
     */
    public function generateBracket($idTournoi, $equipes = null) {
        try {
            // Récupérer le tournoi
            $tournoi = $this->getTournoiById($idTournoi);
            if (!$tournoi) {
                return ['error' => 'Tournoi introuvable'];
            }
            
            // Utiliser les équipes fournies ou celles du tournoi
            if ($equipes === null) {
                $equipes = $tournoi['equipes'];
            }
            // Randomiser l'ordre des équipes pour le bracket
            $equipes = array_values($equipes);
            shuffle($equipes);
            
            // Valider le format
            if (!$this->validateTournoiFormat($tournoi['format'], $equipes)) {
                return ['error' => 'Format invalide ou nombre d\'équipes incorrect'];
            }
            
            // Vérifier qu'il n'y a pas déjà des matchs
            require_once __DIR__ . '/Match.php';
            $matchModel = new TournamentMatch($this->db);
            $existingMatches = $matchModel->getMatchesByTournoi($idTournoi);
            if (!empty($existingMatches)) {
                return ['error' => 'Le bracket existe déjà. Supprimez les matchs existants pour regénérer.'];
            }
            
            // Extraire le nombre d'équipes
            preg_match('/(\d+)/', $tournoi['format'], $matches);
            $nombreEquipes = (int)$matches[1];
            
            // Générer le bracket selon le format
            $this->db->beginTransaction();
            
            try {
                if ($nombreEquipes === 8) {
                    $result = $this->generateBracket8Equipes($idTournoi, $equipes, $matchModel);
                } elseif ($nombreEquipes === 16) {
                    $result = $this->generateBracket16Equipes($idTournoi, $equipes, $matchModel);
                } else {
                    throw new Exception('Format non supporté');
                }
                
                if (isset($result['error'])) {
                    $this->db->rollBack();
                    return $result;
                }
                
                $this->db->commit();
                return true;
            } catch (Exception $e) {
                $this->db->rollBack();
                error_log("Erreur génération bracket: " . $e->getMessage());
                return ['error' => 'Erreur lors de la génération du bracket: ' . $e->getMessage()];
            }
        } catch (PDOException $e) {
            error_log("Erreur génération bracket: " . $e->getMessage());
            return ['error' => 'Erreur base de données'];
        }
    }

    /**
     * Générer le bracket pour 8 équipes
     * Structure: 4 quarts → 2 demi-finales → 1 finale
     */
    private function generateBracket8Equipes($idTournoi, $equipes, $matchModel) {
        // Round 1: Quarts de finale (4 matchs)
        $quarts = [];
        for ($i = 0; $i < 4; $i++) {
            $matchData = [
                'idTournoi' => $idTournoi,
                'equipe' => $equipes[$i * 2],
                'equipeAdv' => $equipes[$i * 2 + 1],
                'idReservation' => null, // À lier plus tard
                'round' => 1
            ];
            $idMatch = $matchModel->createMatch($matchData);
            if (!$idMatch) {
                return ['error' => 'Erreur création match quart de finale'];
            }
            $quarts[] = $idMatch;
        }
        
        // Round 2: Demi-finales (2 matchs)
        $demis = [];
        for ($i = 0; $i < 2; $i++) {
            $matchData = [
                'idTournoi' => $idTournoi,
                'equipe' => 'Winner Q' . ($i * 2 + 1),
                'equipeAdv' => 'Winner Q' . ($i * 2 + 2),
                'idReservation' => null,
                'round' => 2
            ];
            $idMatch = $matchModel->createMatch($matchData);
            if (!$idMatch) {
                return ['error' => 'Erreur création match demi-finale'];
            }
            $demis[] = $idMatch;
            
            // Lier les quarts aux demis
            $matchModel->setNextMatch($quarts[$i * 2], $idMatch);
            $matchModel->setNextMatch($quarts[$i * 2 + 1], $idMatch);
        }
        
        // Round 3: Finale (1 match)
        $matchData = [
            'idTournoi' => $idTournoi,
            'equipe' => 'Winner SF1',
            'equipeAdv' => 'Winner SF2',
            'idReservation' => null,
            'round' => 3
        ];
        $idFinale = $matchModel->createMatch($matchData);
        if (!$idFinale) {
            return ['error' => 'Erreur création match finale'];
        }
        
        // Lier les demis à la finale
        $matchModel->setNextMatch($demis[0], $idFinale);
        $matchModel->setNextMatch($demis[1], $idFinale);
        
        return true;
    }

    /**
     * Générer le bracket pour 16 équipes
     * Structure: 8 huitièmes → 4 quarts → 2 demi-finales → 1 finale
     */
    private function generateBracket16Equipes($idTournoi, $equipes, $matchModel) {
        // Round 1: Huitièmes de finale (8 matchs)
        $huitiemes = [];
        for ($i = 0; $i < 8; $i++) {
            $matchData = [
                'idTournoi' => $idTournoi,
                'equipe' => $equipes[$i * 2],
                'equipeAdv' => $equipes[$i * 2 + 1],
                'idReservation' => null,
                'round' => 1
            ];
            $idMatch = $matchModel->createMatch($matchData);
            if (!$idMatch) {
                return ['error' => 'Erreur création match huitième de finale'];
            }
            $huitiemes[] = $idMatch;
        }
        
        // Round 2: Quarts de finale (4 matchs)
        $quarts = [];
        for ($i = 0; $i < 4; $i++) {
            $matchData = [
                'idTournoi' => $idTournoi,
                'equipe' => 'Winner H' . ($i * 2 + 1),
                'equipeAdv' => 'Winner H' . ($i * 2 + 2),
                'idReservation' => null,
                'round' => 2
            ];
            $idMatch = $matchModel->createMatch($matchData);
            if (!$idMatch) {
                return ['error' => 'Erreur création match quart de finale'];
            }
            $quarts[] = $idMatch;
            
            // Lier les huitièmes aux quarts
            $matchModel->setNextMatch($huitiemes[$i * 2], $idMatch);
            $matchModel->setNextMatch($huitiemes[$i * 2 + 1], $idMatch);
        }
        
        // Round 3: Demi-finales (2 matchs)
        $demis = [];
        for ($i = 0; $i < 2; $i++) {
            $matchData = [
                'idTournoi' => $idTournoi,
                'equipe' => 'Winner Q' . ($i * 2 + 1),
                'equipeAdv' => 'Winner Q' . ($i * 2 + 2),
                'idReservation' => null,
                'round' => 3
            ];
            $idMatch = $matchModel->createMatch($matchData);
            if (!$idMatch) {
                return ['error' => 'Erreur création match demi-finale'];
            }
            $demis[] = $idMatch;
            
            // Lier les quarts aux demis
            $matchModel->setNextMatch($quarts[$i * 2], $idMatch);
            $matchModel->setNextMatch($quarts[$i * 2 + 1], $idMatch);
        }
        
        // Round 4: Finale (1 match)
        $matchData = [
            'idTournoi' => $idTournoi,
            'equipe' => 'Winner SF1',
            'equipeAdv' => 'Winner SF2',
            'idReservation' => null,
            'round' => 4
        ];
        $idFinale = $matchModel->createMatch($matchData);
        if (!$idFinale) {
            return ['error' => 'Erreur création match finale'];
        }
        
        // Lier les demis à la finale
        $matchModel->setNextMatch($demis[0], $idFinale);
        $matchModel->setNextMatch($demis[1], $idFinale);
        
        return true;
    }
}
?>







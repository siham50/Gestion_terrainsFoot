<?php
// classes/Match.php

require_once __DIR__ . '/Database.php';

class TournamentMatch {
    private $db;

    public function __construct($database = null) {
        // Accept PDO connection or use getDB() if not provided
        $this->db = $database ? $database : getDB();
    }

    /**
     * Créer un nouveau match
     * @param array $data Données du match
     * @return int|false ID du match créé ou false en cas d'erreur
     */
    public function createMatch($data) {
        try {
            $sql = "INSERT INTO matchs 
                    (equipe, equipeAdv, score, gagnant, idTournoi, nextMatchId) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $data['equipe'] ?? null,
                $data['equipeAdv'] ?? null,
                $data['score'] ?? null,
                $data['gagnant'] ?? null,
                $data['idTournoi'] ?? null,
                $data['nextMatchId'] ?? null
            ]);
            
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Erreur création match: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupérer un match par son ID
     * @param int $idMatch
     * @return array|false Données du match ou false
     */
    public function getMatchById($idMatch) {
        try {
            $sql = "SELECT m.*, 
                           r.idReservation AS idReservation,
                           t.nom as terrain_nom,
                           r.dateReservation,
                           ch.heure_debut,
                           ch.heure_fin
                    FROM matchs m
                    LEFT JOIN reservation r ON r.idMatch = m.idMatch
                    LEFT JOIN terrain t ON r.idTerrain = t.idTerrain
                    LEFT JOIN creneaux_horaires ch ON r.idCreneau = ch.idCreneau
                    WHERE m.idMatch = ?";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$idMatch]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur récupération match: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupérer tous les matchs d'un tournoi
     * @param int $idTournoi
     * @return array Liste des matchs
     */
    public function getMatchesByTournoi($idTournoi) {
        try {
            $sql = "SELECT m.*, 
                           r.idReservation AS idReservation,
                           t.nom as terrain_nom,
                           r.dateReservation,
                           ch.heure_debut,
                           ch.heure_fin
                    FROM matchs m
                    LEFT JOIN reservation r ON r.idMatch = m.idMatch
                    LEFT JOIN terrain t ON r.idTerrain = t.idTerrain
                    LEFT JOIN creneaux_horaires ch ON r.idCreneau = ch.idCreneau
                    WHERE m.idTournoi = ?
                    ORDER BY m.idMatch ASC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$idTournoi]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur récupération matchs tournoi: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Récupérer les matchs d'un round spécifique
     * @param int $idTournoi
     * @param int $round Numéro du round
     * @return array Liste des matchs
     */
    public function getMatchesByRound($idTournoi, $round) {
        try {
            // Note: La colonne 'round' n'existe pas dans la table actuelle
            // On peut déterminer le round en fonction de nextMatchId
            // Pour l'instant, on retourne tous les matchs et on filtre côté application
            $sql = "SELECT m.*, 
                           r.idReservation AS idReservation,
                           t.nom as terrain_nom,
                           r.dateReservation,
                           ch.heure_debut,
                           ch.heure_fin
                    FROM matchs m
                    LEFT JOIN reservation r ON r.idMatch = m.idMatch
                    LEFT JOIN terrain t ON r.idTerrain = t.idTerrain
                    LEFT JOIN creneaux_horaires ch ON r.idCreneau = ch.idCreneau
                    WHERE m.idTournoi = ?
                    ORDER BY m.idMatch ASC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$idTournoi]);
            $allMatches = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Filtrer par round (déterminer le round en fonction de la structure)
            // Cette logique peut être améliorée si on ajoute une colonne 'round' à la table
            return $allMatches;
        } catch (PDOException $e) {
            error_log("Erreur récupération matchs round: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Mettre à jour un match
     * @param int $idMatch
     * @param array $data Données à mettre à jour
     * @return bool Succès de l'opération
     */
    public function updateMatch($idMatch, $data) {
        try {
            $updates = [];
            $params = [];
            
            if (isset($data['equipe'])) {
                $updates[] = "equipe = ?";
                $params[] = $data['equipe'];
            }
            
            if (isset($data['equipeAdv'])) {
                $updates[] = "equipeAdv = ?";
                $params[] = $data['equipeAdv'];
            }
            
            if (isset($data['score'])) {
                $updates[] = "score = ?";
                $params[] = $data['score'];
            }
            
            if (isset($data['gagnant'])) {
                $updates[] = "gagnant = ?";
                $params[] = $data['gagnant'];
            }
            
            if (isset($data['nextMatchId'])) {
                $updates[] = "nextMatchId = ?";
                $params[] = $data['nextMatchId'];
            }
            
            // Note: plus de mise à jour de idReservation ici; liaison faite côté table reservation
            
            if (empty($updates)) {
                return false;
            }
            
            $params[] = $idMatch;
            $sql = "UPDATE matchs SET " . implode(", ", $updates) . " WHERE idMatch = ?";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Erreur mise à jour match: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Mettre à jour le score et le gagnant d'un match
     * @param int $idMatch
     * @param string $score Score au format "X-Y"
     * @param string|null $gagnant Nom de l'équipe gagnante (optionnel, calculé si null)
     * @return bool|array True si succès, ou tableau d'erreur
     */
    public function updateScore($idMatch, $score, $gagnant = null) {
        try {
            // Valider le format du score
            if (!preg_match('/^\d+-\d+$/', $score)) {
                return ['error' => 'Format de score invalide. Utilisez le format "X-Y"'];
            }
            
            // Extraire les scores
            list($scoreEquipe1, $scoreEquipe2) = explode('-', $score);
            
            // Récupérer le match
            $match = $this->getMatchById($idMatch);
            if (!$match) {
                return ['error' => 'Match introuvable'];
            }
            
            // Déterminer le gagnant si non fourni
            if ($gagnant === null) {
                if ($scoreEquipe1 > $scoreEquipe2) {
                    $gagnant = $match['equipe'];
                } elseif ($scoreEquipe2 > $scoreEquipe1) {
                    $gagnant = $match['equipeAdv'];
                } else {
                    return ['error' => 'Score nul non autorisé. Utilisez les prolongations ou tirs au but.'];
                }
            }
            
            // Mettre à jour le match
            $updateData = [
                'score' => $score,
                'gagnant' => $gagnant
            ];
            
            if (!$this->updateMatch($idMatch, $updateData)) {
                return ['error' => 'Erreur lors de la mise à jour du match'];
            }
            
            // Si le match a un nextMatchId, mettre à jour le match suivant
            if ($match['nextMatchId']) {
                $this->propagateWinnerToNextMatch($match['nextMatchId'], $gagnant, $match);
            } else {
                // C'est la finale, mettre à jour le champion du tournoi
                require_once __DIR__ . '/Tournoi.php';
                $tournoiModel = new Tournoi($this->db);
                $tournoiModel->setChampion($match['idTournoi'], $gagnant);
            }
            
            return true;
        } catch (Exception $e) {
            error_log("Erreur mise à jour score: " . $e->getMessage());
            return ['error' => 'Erreur lors de la mise à jour du score'];
        }
    }

    /**
     * Propager le gagnant vers le match suivant
     * @param int $nextMatchId ID du match suivant
     * @param string $winner Nom de l'équipe gagnante
     * @param array $currentMatch Données du match actuel
     */
    private function propagateWinnerToNextMatch($nextMatchId, $winner, $currentMatch) {
        try {
            $nextMatch = $this->getMatchById($nextMatchId);
            if (!$nextMatch) {
                return;
            }
            
            // Déterminer quelle position (equipe ou equipeAdv) doit être mise à jour
            // On vérifie les placeholders "Winner X" dans les noms d'équipes
            $updateData = [];
            
            // Trouver tous les matchs qui pointent vers ce match suivant
            $sql = "SELECT idMatch FROM matchs WHERE nextMatchId = ? ORDER BY idMatch ASC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$nextMatchId]);
            $predecessors = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            // Si le match actuel est le premier prédecesseur, mettre à jour equipe
            // Sinon, mettre à jour equipeAdv
            if (!empty($predecessors) && $predecessors[0] == $currentMatch['idMatch']) {
                // Premier match → met à jour equipe
                if (strpos($nextMatch['equipe'], 'Winner') !== false || 
                    preg_match('/Winner (Q|H|SF|F)\d+/', $nextMatch['equipe'])) {
                    $updateData['equipe'] = $winner;
                }
            } else {
                // Deuxième match → met à jour equipeAdv
                if (strpos($nextMatch['equipeAdv'], 'Winner') !== false || 
                    preg_match('/Winner (Q|H|SF|F)\d+/', $nextMatch['equipeAdv'])) {
                    $updateData['equipeAdv'] = $winner;
                }
            }
            
            // Fallback: si aucune logique n'a fonctionné, essayer de détecter automatiquement
            if (empty($updateData)) {
                // Si equipe contient un placeholder et equipeAdv est déjà rempli, mettre à jour equipe
                if ((strpos($nextMatch['equipe'], 'Winner') !== false) && 
                    !empty($nextMatch['equipeAdv']) && 
                    strpos($nextMatch['equipeAdv'], 'Winner') === false) {
                    $updateData['equipe'] = $winner;
                } 
                // Sinon, si equipeAdv contient un placeholder
                elseif (strpos($nextMatch['equipeAdv'], 'Winner') !== false) {
                    $updateData['equipeAdv'] = $winner;
                }
                // Sinon, mettre à jour equipe par défaut si elle contient un placeholder
                elseif (strpos($nextMatch['equipe'], 'Winner') !== false) {
                    $updateData['equipe'] = $winner;
                }
            }
            
            if (!empty($updateData)) {
                $this->updateMatch($nextMatchId, $updateData);
            }
        } catch (Exception $e) {
            error_log("Erreur propagation gagnant: " . $e->getMessage());
        }
    }

    /**
     * Récupérer le match suivant
     * @param int $idMatch
     * @return array|false Données du match suivant ou false
     */
    public function getNextMatch($idMatch) {
        try {
            $match = $this->getMatchById($idMatch);
            if (!$match || !$match['nextMatchId']) {
                return false;
            }
            
            return $this->getMatchById($match['nextMatchId']);
        } catch (Exception $e) {
            error_log("Erreur récupération match suivant: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Lier un match à une réservation
     * @param int $idMatch
     * @param int $idReservation
     * @return bool Succès de l'opération
     */
    public function linkToReservation($idMatch, $idReservation) {
        try {
            $sql = "UPDATE reservation SET idMatch = ? WHERE idReservation = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$idMatch, $idReservation]);
        } catch (PDOException $e) {
            error_log("Erreur liaison réservation->match: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Définir le match suivant pour un match
     * @param int $idMatch
     * @param int $nextMatchId
     * @return bool Succès de l'opération
     */
    public function setNextMatch($idMatch, $nextMatchId) {
        return $this->updateMatch($idMatch, ['nextMatchId' => $nextMatchId]);
    }

    /**
     * Récupérer un match avec les informations de réservation
     * @param int $idMatch
     * @return array|false Données du match avec réservation ou false
     */
    public function getMatchWithReservation($idMatch) {
        // Cette méthode est déjà implémentée dans getMatchById
        return $this->getMatchById($idMatch);
    }

    /**
     * Supprimer un match
     * @param int $idMatch
     * @return bool Succès de l'opération
     */
    public function deleteMatch($idMatch) {
        try {
            $sql = "DELETE FROM matchs WHERE idMatch = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$idMatch]);
        } catch (PDOException $e) {
            error_log("Erreur suppression match: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Supprimer tous les matchs d'un tournoi
     * @param int $idTournoi
     * @return bool Succès de l'opération
     */
    public function deleteMatchesByTournoi($idTournoi) {
        try {
            $sql = "DELETE FROM matchs WHERE idTournoi = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$idTournoi]);
        } catch (PDOException $e) {
            error_log("Erreur suppression matchs tournoi: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtenir la structure du bracket pour un tournoi
     * Organise les matchs par rounds
     * @param int $idTournoi
     * @return array Structure du bracket organisée par rounds
     */
    public function getBracketStructure($idTournoi) {
        try {
            $matches = $this->getMatchesByTournoi($idTournoi);
            
            // Organiser les matchs par rounds
            // Round 1: matchs sans matchs précédents pointant vers eux
            // Round N: matchs pointés par des matchs du round N-1
            
            $bracket = [];
            $processed = [];
            
            // Round 1: matchs initiaux (ceux qui n'ont pas de matchs pointant vers eux)
            $round1 = [];
            foreach ($matches as $match) {
                $hasPredecessor = false;
                foreach ($matches as $other) {
                    if ($other['nextMatchId'] == $match['idMatch']) {
                        $hasPredecessor = true;
                        break;
                    }
                }
                if (!$hasPredecessor) {
                    $round1[] = $match;
                    $processed[] = $match['idMatch'];
                }
            }
            $bracket[1] = $round1;
            
            // Rounds suivants: trouver les matchs pointés par les matchs du round précédent
            $currentRound = 2;
            $previousRoundMatches = $round1;
            
            while (!empty($previousRoundMatches)) {
                $currentRoundMatches = [];
                foreach ($previousRoundMatches as $prevMatch) {
                    if ($prevMatch['nextMatchId']) {
                        foreach ($matches as $match) {
                            if ($match['idMatch'] == $prevMatch['nextMatchId'] && 
                                !in_array($match['idMatch'], $processed)) {
                                $currentRoundMatches[] = $match;
                                $processed[] = $match['idMatch'];
                            }
                        }
                    }
                }
                
                if (empty($currentRoundMatches)) {
                    break;
                }
                
                $bracket[$currentRound] = $currentRoundMatches;
                $previousRoundMatches = $currentRoundMatches;
                $currentRound++;
            }
            
            return $bracket;
        } catch (Exception $e) {
            error_log("Erreur récupération structure bracket: " . $e->getMessage());
            return [];
        }
    }
}
?>


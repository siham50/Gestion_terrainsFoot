<?php
// controllers/TournoiController.php

require_once __DIR__ . '/../classes/Tournoi.php';
require_once __DIR__ . '/../classes/Match.php';
require_once __DIR__ . '/../config/database.php';

class TournoiController {
    private $tournoiModel;
    private $matchModel;

    public function __construct($database = null) {
        $db = $database ? $database : getDB();
        $this->tournoiModel = new Tournoi($db);
        $this->matchModel = new TournamentMatch($db);
    }

    /**
     * Lister tous les tournois (publique)
     * @return array Liste des tournois
     */
    public function listTournois() {
        return $this->tournoiModel->getAllTournois();
    }

    /**
     * Afficher un tournoi avec son bracket
     * @param int $idTournoi
     * @return array|false Données du tournoi avec matchs ou false
     */
    public function viewTournoi($idTournoi) {
        $tournoi = $this->tournoiModel->getTournoiWithMatches($idTournoi);
        if (!$tournoi) {
            return false;
        }
        
        // Organiser les matchs par rounds
        $tournoi['bracket'] = $this->matchModel->getBracketStructure($idTournoi);
        $tournoi['stats'] = $this->tournoiModel->getTournoiStats($idTournoi);
        
        return $tournoi;
    }

    /**
     * Récupérer les tournois de l'utilisateur connecté
     * @param int $idUtilisateur
     * @return array Liste des tournois
     */
    public function mesTournois($idUtilisateur) {
        return $this->tournoiModel->getTournoisByUser($idUtilisateur);
    }

    /**
     * Créer un nouveau tournoi
     * @param bool $returnArray Si true, retourne un tableau pour AJAX
     * @return array|bool Tableau de réponse ou booléen
     */
    public function createTournoi($returnArray = false) {
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            $response = [
                'success' => false,
                'message' => 'Vous devez être connecté pour créer un tournoi'
            ];
            if ($returnArray) return $response;
            $_SESSION['tournoi_feedback'] = $response;
            return false;
        }

        // Récupérer les données du formulaire
        $tournoiData = [
            'format' => $_POST['format'] ?? null,
            'equipes' => $this->parseEquipes($_POST['equipes'] ?? []),
            'idUtilisateur' => $_SESSION['user_id']
        ];

        // Validation des données
        $errors = $this->validateTournoiData($tournoiData);
        if (!empty($errors)) {
            $response = [
                'success' => false,
                'message' => 'Données invalides',
                'errors' => $errors
            ];
            if ($returnArray) return $response;
            $_SESSION['tournoi_feedback'] = $response;
            return false;
        }

        try {
            // Créer le tournoi
            $idTournoi = $this->tournoiModel->createTournoi($tournoiData);

            if ($idTournoi) {
                // Générer automatiquement le bracket
                $bracketResult = $this->tournoiModel->generateBracket($idTournoi);
                
                if ($bracketResult === true) {
                    $response = [
                        'success' => true,
                        'message' => 'Tournoi créé avec succès! Le bracket a été généré automatiquement.',
                        'idTournoi' => $idTournoi
                    ];
                } else {
                    // Tournoi créé mais erreur de génération du bracket
                    $response = [
                        'success' => true,
                        'message' => 'Tournoi créé mais erreur lors de la génération du bracket: ' . ($bracketResult['error'] ?? 'Erreur inconnue'),
                        'idTournoi' => $idTournoi,
                        'warning' => true
                    ];
                }
                
                if ($returnArray) return $response;
                $_SESSION['tournoi_feedback'] = $response;
                return true;
            } else {
                $response = [
                    'success' => false,
                    'message' => 'Erreur lors de la création du tournoi'
                ];
                if ($returnArray) return $response;
                $_SESSION['tournoi_feedback'] = $response;
                return false;
            }

        } catch (Exception $e) {
            $response = [
                'success' => false,
                'message' => 'Erreur technique: ' . $e->getMessage()
            ];
            if ($returnArray) return $response;
            $_SESSION['tournoi_feedback'] = $response;
            return false;
        }
    }

    /**
     * Parser les équipes depuis le formulaire
     * @param mixed $equipes Données brutes des équipes
     * @return array Liste des équipes
     */
    private function parseEquipes($equipes) {
        if (is_string($equipes)) {
            // Si c'est une chaîne, essayer de la décoder (JSON) ou la diviser
            $decoded = json_decode($equipes, true);
            if ($decoded !== null) {
                return $decoded;
            }
            // Sinon, diviser par virgule ou retour à la ligne
            $equipes = preg_split('/[,\n\r]+/', $equipes);
            $equipes = array_map('trim', $equipes);
            $equipes = array_filter($equipes, function($e) { return !empty($e); });
            return array_values($equipes);
        }
        
        if (is_array($equipes)) {
            // Filtrer les valeurs vides
            $equipes = array_map('trim', $equipes);
            $equipes = array_filter($equipes, function($e) { return !empty($e); });
            return array_values($equipes);
        }
        
        return [];
    }

    /**
     * Valider les données du tournoi
     * @param array $data Données à valider
     * @return array Liste des erreurs
     */
    private function validateTournoiData($data) {
        $errors = [];

        if (empty($data['format'])) {
            $errors[] = 'Format du tournoi requis';
        } elseif (!preg_match('/(8|16)\s*équipes?/i', $data['format'])) {
            $errors[] = 'Format invalide. Seuls les formats 8 et 16 équipes sont supportés.';
        }

        if (empty($data['equipes']) || !is_array($data['equipes'])) {
            $errors[] = 'Liste des équipes requise';
        } else {
            $nombreEquipes = count($data['equipes']);
            preg_match('/(\d+)/', $data['format'], $matches);
            $nombreAttendu = isset($matches[1]) ? (int)$matches[1] : 0;
            
            if ($nombreEquipes !== $nombreAttendu) {
                $errors[] = "Le nombre d'équipes ($nombreEquipes) ne correspond pas au format ($nombreAttendu équipes)";
            }
            
            // Vérifier les doublons
            if (count($data['equipes']) !== count(array_unique($data['equipes']))) {
                $errors[] = 'Les noms d\'équipes doivent être uniques';
            }
            
            // Vérifier les noms vides
            foreach ($data['equipes'] as $index => $equipe) {
                if (empty(trim($equipe))) {
                    $errors[] = "L'équipe #" . ($index + 1) . " ne peut pas être vide";
                }
            }
        }

        return $errors;
    }

    /**
     * Mettre à jour le score d'un match
     * @param int $idMatch
     * @param string $score
     * @param string|null $gagnant
     * @return array Résultat de l'opération
     */
    public function updateMatchScore($idMatch, $score, $gagnant = null) {
        // Vérifier les permissions (admin ou organisateur du tournoi)
        if (!isset($_SESSION['user_id'])) {
            return [
                'success' => false,
                'message' => 'Vous devez être connecté'
            ];
        }

        try {
            $match = $this->matchModel->getMatchById($idMatch);
            if (!$match) {
                return [
                    'success' => false,
                    'message' => 'Match introuvable'
                ];
            }

            // Exiger une réservation liée au match avant de permettre la mise à jour du score
            if (empty($match['idReservation'])) {
                return [
                    'success' => false,
                    'message' => 'Ce match n\'a pas encore de réservation. Réservez d\'abord le match pour saisir le score.'
                ];
            }

            // Vérifier que l'heure de début de la réservation est passée
            $dateReservation = $match['dateReservation'] ?? null;
            $heureFin = $match['heure_fin'] ?? null;
            if (!$dateReservation || !$heureFin) {
                return [
                    'success' => false,
                    'message' => 'Informations de réservation incomplètes (date/heure).'
                ];
            }

            try {
                $reservationEnd = new DateTime($dateReservation . ' ' . $heureFin);
                $now = new DateTime('now');
            } catch (Exception $e) {
                return [
                    'success' => false,
                    'message' => 'Format de date/heure de réservation invalide'
                ];
            }

            if ($now <= $reservationEnd) {
                return [
                    'success' => false,
                    'message' => 'Vous ne pouvez saisir le score qu\'après la fin du match.'
                ];
            }

            // Vérifier les permissions (organisateur uniquement)
            $tournoi = $this->tournoiModel->getTournoiById($match['idTournoi']);
            $isOrganisateur = $tournoi && $tournoi['idUtilisateur'] == $_SESSION['user_id'];

            if (!$isOrganisateur) {
                return [
                    'success' => false,
                    'message' => 'Vous n\'avez pas les permissions pour modifier ce match'
                ];
            }

            // Mettre à jour le score
            $result = $this->matchModel->updateScore($idMatch, $score, $gagnant);

            if ($result === true) {
                return [
                    'success' => true,
                    'message' => 'Score mis à jour avec succès'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => $result['error'] ?? 'Erreur lors de la mise à jour du score'
                ];
            }

        } catch (Exception $e) {
            error_log("Erreur mise à jour score: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erreur technique: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Générer ou regénérer le bracket d'un tournoi
     * @param int $idTournoi
     * @return array Résultat de l'opération
     */
    public function generateBracket($idTournoi) {
        // Vérifier les permissions
        if (!isset($_SESSION['user_id'])) {
            return [
                'success' => false,
                'message' => 'Vous devez être connecté'
            ];
        }

        try {
            $tournoi = $this->tournoiModel->getTournoiById($idTournoi);
            if (!$tournoi) {
                return [
                    'success' => false,
                    'message' => 'Tournoi introuvable'
                ];
            }

            // Vérifier les permissions (organisateur uniquement)
            $isOrganisateur = $tournoi['idUtilisateur'] == $_SESSION['user_id'];

            if (!$isOrganisateur) {
                return [
                    'success' => false,
                    'message' => 'Vous n\'avez pas les permissions pour générer le bracket'
                ];
            }

            // Vérifier s'il y a déjà des matchs
            $existingMatches = $this->matchModel->getMatchesByTournoi($idTournoi);
            if (!empty($existingMatches)) {
                // Supprimer les matchs existants si demandé
                if (isset($_POST['force']) && $_POST['force'] === 'true') {
                    $this->matchModel->deleteMatchesByTournoi($idTournoi);
                } else {
                    return [
                        'success' => false,
                        'message' => 'Le bracket existe déjà. Utilisez l\'option "forcer" pour le regénérer.'
                    ];
                }
            }

            // Générer le bracket
            $result = $this->tournoiModel->generateBracket($idTournoi);

            if ($result === true) {
                return [
                    'success' => true,
                    'message' => 'Bracket généré avec succès'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => $result['error'] ?? 'Erreur lors de la génération du bracket'
                ];
            }

        } catch (Exception $e) {
            error_log("Erreur génération bracket: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erreur technique: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Récupérer les données JSON d'un tournoi
     * @param int $idTournoi
     * @return array Données du tournoi
     */
    public function getTournoiData($idTournoi) {
        $tournoi = $this->viewTournoi($idTournoi);
        if (!$tournoi) {
            return [
                'success' => false,
                'message' => 'Tournoi introuvable'
            ];
        }

        return [
            'success' => true,
            'data' => $tournoi
        ];
    }

    /**
     * Récupérer les données du bracket pour affichage
     * @param int $idTournoi
     * @return array Structure du bracket
     */
    public function getBracketData($idTournoi) {
        $bracket = $this->matchModel->getBracketStructure($idTournoi);
        return [
            'success' => true,
            'bracket' => $bracket
        ];
    }


    public function getTournoiStats($idTournoi) {
        return $this->tournoiModel->getTournoiStats($idTournoi);
    }
}

// ========== GESTION DES ROUTES AJAX ==========

if (isset($_GET['action'])) {
    session_start();
    $controller = new TournoiController();
    
    switch ($_GET['action']) {
        // Actions publiques
        case 'list_tournois':
            header('Content-Type: application/json');
            $tournois = $controller->listTournois();
            echo json_encode(['success' => true, 'data' => $tournois]);
            break;
            
        case 'get_tournoi_data':
            header('Content-Type: application/json');
            if (isset($_GET['id'])) {
                $result = $controller->getTournoiData((int)$_GET['id']);
                echo json_encode($result);
            } else {
                echo json_encode(['success' => false, 'message' => 'ID manquant']);
            }
            break;
            
        case 'get_bracket_data':
            header('Content-Type: application/json');
            if (isset($_GET['id'])) {
                $result = $controller->getBracketData((int)$_GET['id']);
                echo json_encode($result);
            } else {
                echo json_encode(['success' => false, 'message' => 'ID manquant']);
            }
            break;
            
        case 'create':
            header('Content-Type: application/json');
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $result = $controller->createTournoi(true);
                echo json_encode($result);
            } else {
                echo json_encode(['success' => false, 'message' => 'Méthode POST requise']);
            }
            break;
            
        case 'mes_tournois':
            header('Content-Type: application/json');
            if (isset($_SESSION['user_id'])) {
                $tournois = $controller->mesTournois($_SESSION['user_id']);
                echo json_encode(['success' => true, 'data' => $tournois]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Non connecté']);
            }
            break;
            
        case 'update_score':
            header('Content-Type: application/json');
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idMatch']) && isset($_POST['score'])) {
                $result = $controller->updateMatchScore(
                    (int)$_POST['idMatch'],
                    $_POST['score'],
                    $_POST['gagnant'] ?? null
                );
                echo json_encode($result);
            } else {
                echo json_encode(['success' => false, 'message' => 'Données manquantes']);
            }
            break;
            
        case 'generate_bracket':
            header('Content-Type: application/json');
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idTournoi'])) {
                $result = $controller->generateBracket((int)$_POST['idTournoi']);
                echo json_encode($result);
            } else {
                echo json_encode(['success' => false, 'message' => 'ID manquant']);
            }
            break;
            
        // Actions admin
        case 'admin_list_tournois':
            header('Content-Type: application/json');
            $result = $controller->adminListTournois();
            if (is_array($result) && isset($result['success'])) {
                echo json_encode($result);
            } else {
                echo json_encode(['success' => true, 'data' => $result]);
            }
            break;
            
        case 'admin_edit_tournoi':
            header('Content-Type: application/json');
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idTournoi'])) {
                $data = [
                    'format' => $_POST['format'] ?? null,
                    'equipes' => isset($_POST['equipes']) ? json_decode($_POST['equipes'], true) : null,
                    'champion' => $_POST['champion'] ?? null
                ];
                $data = array_filter($data, function($v) { return $v !== null; });
                $result = $controller->adminEditTournoi((int)$_POST['idTournoi'], $data);
                echo json_encode($result);
            } else {
                echo json_encode(['success' => false, 'message' => 'Données manquantes']);
            }
            break;
            
        case 'admin_delete_tournoi':
            header('Content-Type: application/json');
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idTournoi'])) {
                $result = $controller->adminDeleteTournoi((int)$_POST['idTournoi']);
                echo json_encode($result);
            } else {
                echo json_encode(['success' => false, 'message' => 'ID manquant']);
            }
            break;
            
        case 'admin_manage_matches':
            header('Content-Type: application/json');
            if (isset($_GET['id'])) {
                $result = $controller->adminManageMatches((int)$_GET['id']);
                echo json_encode($result);
            } else {
                echo json_encode(['success' => false, 'message' => 'ID manquant']);
            }
            break;
            
        default:
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Action non reconnue']);
    }
    exit;
}

// Gestion du formulaire POST (soumission directe depuis le formulaire)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_GET['action']) && isset($_POST['create_tournoi'])) {
    session_start();
    
    $controller = new TournoiController();
    $controller->createTournoi();
    
    // Rediriger vers la page des tournois
    header('Location: ../views/public/MesTournois.php');
    exit;
}
?>







<?php
// controllers/ReservationController.php

require_once __DIR__ . '/../classes/Reservation.php';
require_once __DIR__ . '/../classes/Utilisateur.php';
require_once __DIR__ . '/../config/database.php';

class ReservationController {
    private $reservationModel;
    private $utilisateurModel;

    public function __construct($database = null) {
        $db = $database ? $database : getDB();
        $this->reservationModel = new Reservation($db);
        if (class_exists('Utilisateur')) {
            $this->utilisateurModel = new Utilisateur($db);
        }
    }

    // Récupérer une réservation d'un utilisateur (sécurité incluse)
    public function getReservationForUser($idReservation, $idUtilisateur) {
        return $this->reservationModel->getReservationByIdForUser($idReservation, $idUtilisateur);
    }

    private function isLockedLessThan48h($reservation) {
        if (!$reservation) return true;
        $date = $reservation['dateReservation'];
        $time = $reservation['heure_debut'] ?? '00:00:00';
        $reservationDateTime = strtotime($date . ' ' . $time);
        $now = time();
        $diffHours = ($reservationDateTime - $now) / 3600;
        return $diffHours < 48;
    }

    // Traiter la création d'une réservation (retourne un tableau pour AJAX ou booléen pour POST classique)
    public function createReservation($returnArray = false) {
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            $response = [
                'success' => false,
                'message' => 'Vous devez être connecté pour effectuer une réservation'
            ];
            if ($returnArray) return $response;
            $_SESSION['reservation_feedback'] = $response;
            return false;
        }

        // Récupérer les données du formulaire
        $reservationData = [
            'idTerrain' => $_POST['idTerrain'] ?? null,
            'idUtilisateur' => $_SESSION['user_id'],
            'dateReservation' => $_POST['dateReservation'] ?? null,
            'idCreneau' => $_POST['idCreneau'] ?? null,
            'demande' => $_POST['demande'] ?? '',
            'ballon' => isset($_POST['ballon']) ? 1 : 0,
            'arbitre' => isset($_POST['arbitre']) ? 1 : 0,
            'maillot' => isset($_POST['maillot']) ? 1 : 0,
            'douche' => isset($_POST['douche']) ? 1 : 0,
            // Nouveau: lier directement à un match de tournoi si fourni
            'idMatch' => isset($_POST['idMatch']) && $_POST['idMatch'] !== '' ? (int)$_POST['idMatch'] : null
        ];

        // Validation des données
        $errors = $this->validateReservationData($reservationData);
        if (!empty($errors)) {
            $response = [
                'success' => false,
                'message' => 'Données invalides',
                'errors' => $errors
            ];
            if ($returnArray) return $response;
            $_SESSION['reservation_feedback'] = $response;
            return false;
        }

        try {
            // Vérifier la disponibilité
            $disponible = $this->reservationModel->checkDisponibilite(
                $reservationData['idTerrain'],
                $reservationData['dateReservation'],
                $reservationData['idCreneau']
            );

            if (!$disponible) {
                $response = [
                    'success' => false,
                    'message' => 'Ce créneau n\'est plus disponible'
                ];
                if ($returnArray) return $response;
                $_SESSION['reservation_feedback'] = $response;
                return false;
            }

            // Créer la réservation
            $success = $this->reservationModel->createReservation($reservationData);

            if ($success) {
                $response = [
                    'success' => true,
                    'message' => 'Réservation effectuée avec succès!'
                ];
                if ($returnArray) return $response;
                $_SESSION['reservation_feedback'] = $response;
                return true;
            } else {
                $response = [
                    'success' => false,
                    'message' => 'Erreur lors de la réservation'
                ];
                if ($returnArray) return $response;
                $_SESSION['reservation_feedback'] = $response;
                return false;
            }

        } catch (Exception $e) {
            $response = [
                'success' => false,
                'message' => 'Erreur technique: ' . $e->getMessage()
            ];
            if ($returnArray) return $response;
            $_SESSION['reservation_feedback'] = $response;
            return false;
        }
    }

    // Mettre à jour une réservation existante
    public function updateReservation($idReservation) {
        if (!isset($_SESSION['user_id'])) {
            return ['success' => false, 'message' => 'Vous devez être connecté'];
        }

        // Charger la réservation et vérifier la propriété
        $reservation = $this->getReservationForUser($idReservation, $_SESSION['user_id']);
        if (!$reservation) {
            return ['success' => false, 'message' => 'Réservation introuvable'];
        }
        // Verrou 48h
        if ($this->isLockedLessThan48h($reservation)) {
            return ['success' => false, 'message' => 'Modification impossible à moins de 48h du match'];
        }

        $updateData = [
            'dateReservation' => $_POST['dateReservation'] ?? null,
            'idCreneau' => $_POST['idCreneau'] ?? null,
            'demande' => $_POST['demande'] ?? '',
            'ballon' => isset($_POST['ballon']) ? 1 : 0,
            'arbitre' => isset($_POST['arbitre']) ? 1 : 0,
            'maillot' => isset($_POST['maillot']) ? 1 : 0,
            'douche' => isset($_POST['douche']) ? 1 : 0
        ];

        // Validation minimale
        $errors = [];
        if (empty($updateData['dateReservation'])) $errors[] = 'Date de réservation requise';
        if (empty($updateData['idCreneau'])) $errors[] = 'Créneau horaire requis';
        if (!empty($errors)) {
            return ['success' => false, 'message' => 'Données invalides', 'errors' => $errors];
        }
        if (strtotime($updateData['dateReservation']) < strtotime(date('Y-m-d'))) {
            return ['success' => false, 'message' => 'La date ne peut pas être dans le passé'];
        }

        // Vérifier la disponibilité (en excluant cette réservation)
        $disponible = $this->reservationModel->checkDisponibiliteExcluant(
            $reservation['idTerrain'],
            $updateData['dateReservation'],
            $updateData['idCreneau'],
            $idReservation
        );
        if (!$disponible) {
            return ['success' => false, 'message' => 'Ce créneau n\'est plus disponible'];
        }

        // Mise à jour
        $ok = $this->reservationModel->updateReservation($idReservation, $_SESSION['user_id'], $updateData);
        if (!$ok) {
            return ['success' => false, 'message' => 'Erreur lors de la mise à jour'];
        }

        return ['success' => true, 'message' => 'Réservation mise à jour avec succès'];
    }

    // Annuler une réservation avec vérification 48h
    public function cancelReservationWithCheck($idReservation, $idUtilisateur) {
        $reservation = $this->getReservationForUser($idReservation, $idUtilisateur);
        if (!$reservation) {
            return ['success' => false, 'message' => 'Réservation introuvable'];
        }
        if ($this->isLockedLessThan48h($reservation)) {
            return ['success' => false, 'message' => 'Annulation impossible à moins de 48h du match'];
        }
        $success = $this->reservationModel->cancelReservation($idReservation, $idUtilisateur);
        return ['success' => $success, 'message' => $success ? 'Réservation annulée' : 'Erreur lors de l\'annulation'];
    }

    // Valider les données de réservation
    private function validateReservationData($data) {
        $errors = [];

        if (empty($data['idTerrain'])) {
            $errors[] = 'Terrain non spécifié';
        }

        if (empty($data['dateReservation'])) {
            $errors[] = 'Date de réservation requise';
        } elseif (strtotime($data['dateReservation']) < strtotime(date('Y-m-d'))) {
            $errors[] = 'La date de réservation ne peut pas être dans le passé';
        }

        if (empty($data['idCreneau'])) {
            $errors[] = 'Créneau horaire requis';
        }

        return $errors;
    }

    // Récupérer les réservations de l'utilisateur (pour d'autres fonctionnalités)
    public function getUserReservations($idUtilisateur) {
        return $this->reservationModel->getUserReservations($idUtilisateur);
    }

    // Récupérer les réservations à venir de l'utilisateur
    public function getUpcomingReservations($idUtilisateur) {
        return $this->reservationModel->getUpcomingReservations($idUtilisateur);
    }

    // Récupérer les statistiques des réservations de l'utilisateur
    public function getUserReservationStats($idUtilisateur) {
        return $this->reservationModel->getUserReservationStats($idUtilisateur);
    }

    // Obtenir toutes les données pour la page MesReservations
    public function getMesReservationsData($idUtilisateur) {
        $upcomingReservations = $this->getUpcomingReservations($idUtilisateur);
        $stats = $this->getUserReservationStats($idUtilisateur);
        
        return [
            'reservations' => $upcomingReservations,
            'stats' => $stats
        ];
    }

    // Annuler une réservation (pour d'autres fonctionnalités)
    public function cancelReservation($idReservation, $idUtilisateur) {
        return $this->reservationModel->cancelReservation($idReservation, $idUtilisateur);
    }
}

// Gestion des routes AJAX (si vous en avez encore besoin pour d'autres fonctionnalités)
if (isset($_GET['action'])) {
    session_start();
    $controller = new ReservationController();
    
    switch ($_GET['action']) {
        case 'create':
            header('Content-Type: application/json');
            $result = $controller->createReservation(true);
            echo json_encode($result);
            break;
        case 'get_reservation':
            header('Content-Type: application/json');
            if (isset($_SESSION['user_id']) && isset($_GET['idReservation'])) {
                $res = $controller->getReservationForUser((int)$_GET['idReservation'], $_SESSION['user_id']);
                if ($res) {
                    echo json_encode(['success' => true, 'data' => $res]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Réservation introuvable']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Paramètres manquants']);
            }
            break;
        case 'get_mes_reservations_data':
            header('Content-Type: application/json');
            if (isset($_SESSION['user_id'])) {
                $data = $controller->getMesReservationsData($_SESSION['user_id']);
                echo json_encode(['success' => true, 'data' => $data]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Non connecté']);
            }
            break;
        case 'get_user_reservations':
            header('Content-Type: application/json');
            if (isset($_SESSION['user_id'])) {
                $reservations = $controller->getUserReservations($_SESSION['user_id']);
                echo json_encode(['success' => true, 'data' => $reservations]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Non connecté']);
            }
            break;
        case 'update':
            header('Content-Type: application/json');
            if (isset($_SESSION['user_id']) && isset($_POST['idReservation'])) {
                $result = $controller->updateReservation((int)$_POST['idReservation']);
                echo json_encode($result);
            } else {
                echo json_encode(['success' => false, 'message' => 'Paramètres manquants']);
            }
            break;
        case 'cancel':
            header('Content-Type: application/json');
            if (isset($_SESSION['user_id']) && isset($_POST['idReservation'])) {
                $result = $controller->cancelReservationWithCheck((int)$_POST['idReservation'], $_SESSION['user_id']);
                echo json_encode($result);
            } else {
                echo json_encode(['success' => false, 'message' => 'Données manquantes']);
            }
            break;
        default:
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Action non reconnue']);
    }
    exit;
}

// Gestion du formulaire POST (soumission directe depuis le formulaire de réservation)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_GET['action'])) {
    session_start();
    
    $controller = new ReservationController();
    $controller->createReservation();
    
    // Rediriger vers la page d'accueil
    header('Location: /Gestion_terrainsFoot/views/public/Home.php');
    exit;
}
?>

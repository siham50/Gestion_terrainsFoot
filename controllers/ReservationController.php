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
            'douche' => isset($_POST['douche']) ? 1 : 0
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
        case 'cancel':
            header('Content-Type: application/json');
            if (isset($_SESSION['user_id']) && isset($_POST['idReservation'])) {
                $success = $controller->cancelReservation($_POST['idReservation'], $_SESSION['user_id']);
                echo json_encode(['success' => $success, 'message' => $success ? 'Réservation annulée' : 'Erreur']);
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

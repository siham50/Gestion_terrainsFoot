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

    // Traiter la création d'une réservation
    public function createReservation() {
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['reservation_feedback'] = [
                'success' => false,
                'message' => 'Vous devez être connecté pour effectuer une réservation'
            ];
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
            $_SESSION['reservation_feedback'] = [
                'success' => false,
                'message' => 'Données invalides',
                'errors' => $errors
            ];
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
                $_SESSION['reservation_feedback'] = [
                    'success' => false,
                    'message' => 'Ce créneau n\'est plus disponible'
                ];
                return false;
            }

            // Créer la réservation
            $success = $this->reservationModel->createReservation($reservationData);

            if ($success) {
                $_SESSION['reservation_feedback'] = [
                    'success' => true,
                    'message' => 'Réservation effectuée avec succès!'
                ];
                return true;
            } else {
                $_SESSION['reservation_feedback'] = [
                    'success' => false,
                    'message' => 'Erreur lors de la réservation'
                ];
                return false;
            }

        } catch (Exception $e) {
            $_SESSION['reservation_feedback'] = [
                'success' => false,
                'message' => 'Erreur technique: ' . $e->getMessage()
            ];
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

    // Annuler une réservation (pour d'autres fonctionnalités)
    public function cancelReservation($idReservation, $idUtilisateur) {
        return $this->reservationModel->cancelReservation($idReservation, $idUtilisateur);
    }
}

// Gestion du formulaire POST (soumission directe depuis le formulaire)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();
    
    $controller = new ReservationController();
    $controller->createReservation();
    
    // Rediriger vers la page d'accueil
    header('Location: /Gestion_terrainsFoot/views/public/Home.php');
    exit;
}

// Gestion des routes AJAX (si vous en avez encore besoin pour d'autres fonctionnalités)
if (isset($_GET['action']) && $_GET['action'] !== 'create') {
    session_start();
    $controller = new ReservationController();
    
    switch ($_GET['action']) {
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
?>

<?php
// controllers/NewsletterController.php

require_once __DIR__ . '/../classes/Newsletter.php';
require_once __DIR__ . '/../classes/Database.php';

class NewsletterController {
    private $newsletterModel;

    public function __construct($database = null) {
        $db = $database ? $database : getDB();
        $this->newsletterModel = new Newsletter($db);
    }

    /**
     * Récupérer les données pour la page Newsletter
     */
    public function getNewsletterData($idUtilisateur) {
        $notifications = $this->newsletterModel->getAllNotifications($idUtilisateur);
        $unreadCount = $this->newsletterModel->countUnreadNotifications($idUtilisateur);
        
        return [
            'notifications' => $notifications,
            'unreadCount' => $unreadCount
        ];
    }

    /**
     * Récupérer seulement les nouvelles notifications (non lues)
     */
    public function getNewNotifications($idUtilisateur, $lastCheckDate = null) {
        if ($lastCheckDate) {
            return $this->newsletterModel->getNewNotificationsSince($idUtilisateur, $lastCheckDate);
        }
        return $this->newsletterModel->getUnreadNotifications($idUtilisateur);
    }

    /**
     * Marquer une notification comme lue
     */
    public function markNotificationAsRead($idNotification, $idUtilisateur) {
        return $this->newsletterModel->markAsRead($idNotification, $idUtilisateur);
    }

    /**
     * Marquer toutes les notifications comme lues
     */
    public function markAllAsRead($idUtilisateur) {
        return $this->newsletterModel->markAllAsRead($idUtilisateur);
    }
}

// Gestion des routes AJAX
if (isset($_GET['action'])) {
    session_start();
    
    if (!isset($_SESSION['user_id'])) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Non connecté']);
        exit;
    }
    
    $controller = new NewsletterController();
    $idUtilisateur = $_SESSION['user_id'];
    
    switch ($_GET['action']) {
        case 'get_newsletter_data':
            header('Content-Type: application/json');
            $data = $controller->getNewsletterData($idUtilisateur);
            echo json_encode(['success' => true, 'data' => $data]);
            break;
            
        case 'get_new_notifications':
            header('Content-Type: application/json');
            $lastCheckDate = $_GET['last_check'] ?? null;
            $notifications = $controller->getNewNotifications($idUtilisateur, $lastCheckDate);
            $data = $controller->getNewsletterData($idUtilisateur);
            $unreadCount = $data['unreadCount'] ?? 0;
            echo json_encode([
                'success' => true, 
                'notifications' => $notifications,
                'unreadCount' => $unreadCount
            ]);
            break;
            
        case 'get_unread_count':
            header('Content-Type: application/json');
            $data = $controller->getNewsletterData($idUtilisateur);
            $unreadCount = $data['unreadCount'] ?? 0;
            echo json_encode(['success' => true, 'count' => $unreadCount]);
            break;
            
        case 'mark_as_read':
            header('Content-Type: application/json');
            if (isset($_POST['id'])) {
                $success = $controller->markNotificationAsRead($_POST['id'], $idUtilisateur);
                echo json_encode(['success' => $success]);
            } else {
                echo json_encode(['success' => false, 'message' => 'ID manquant']);
            }
            break;
            
        case 'mark_all_as_read':
            header('Content-Type: application/json');
            $success = $controller->markAllAsRead($idUtilisateur);
            echo json_encode(['success' => $success]);
            break;
            
        default:
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Action non reconnue']);
    }
    exit;
}
?>


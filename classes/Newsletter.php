<?php
// classes/Newsletter.php

require_once __DIR__ . '/../config/database.php';

class Newsletter {
    private $db;

    public function __construct($database = null) {
        $this->db = $database ? $database : getDB();
    }

    /**
     * Créer une notification
     */
    public function createNotification($type, $titre, $message, $idUtilisateur = null) {
        $sql = "INSERT INTO newsletter (type, titre, message, id_utilisateur, date_creation, lu) 
                VALUES (?, ?, ?, ?, NOW(), 0)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$type, $titre, $message, $idUtilisateur]);
    }

    /**
     * Récupérer les notifications non lues d'un utilisateur
     */
    public function getUnreadNotifications($idUtilisateur, $limit = null) {
        $sql = "SELECT * FROM newsletter 
                WHERE (id_utilisateur IS NULL OR id_utilisateur = ?) 
                AND lu = 0 
                ORDER BY date_creation DESC";
        
        if ($limit) {
            $sql .= " LIMIT " . intval($limit);
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$idUtilisateur]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupérer toutes les notifications d'un utilisateur
     */
    public function getAllNotifications($idUtilisateur, $limit = 50) {
        $sql = "SELECT * FROM newsletter 
                WHERE id_utilisateur IS NULL OR id_utilisateur = ? 
                ORDER BY date_creation DESC 
                LIMIT ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$idUtilisateur, $limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Compter les notifications non lues
     */
    public function countUnreadNotifications($idUtilisateur) {
        $sql = "SELECT COUNT(*) as count FROM newsletter 
                WHERE (id_utilisateur IS NULL OR id_utilisateur = ?) 
                AND lu = 0";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$idUtilisateur]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] ?? 0;
    }

    /**
     * Marquer une notification comme lue
     */
    public function markAsRead($idNotification, $idUtilisateur) {
        $sql = "UPDATE newsletter 
                SET lu = 1 
                WHERE id = ? 
                AND (id_utilisateur IS NULL OR id_utilisateur = ?)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$idNotification, $idUtilisateur]);
    }

    /**
     * Marquer toutes les notifications comme lues
     */
    public function markAllAsRead($idUtilisateur) {
        $sql = "UPDATE newsletter 
                SET lu = 1 
                WHERE (id_utilisateur IS NULL OR id_utilisateur = ?) 
                AND lu = 0";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$idUtilisateur]);
    }

    /**
     * Récupérer les nouvelles notifications depuis une date
     */
    public function getNewNotificationsSince($idUtilisateur, $lastCheckDate) {
        $sql = "SELECT * FROM newsletter 
                WHERE (id_utilisateur IS NULL OR id_utilisateur = ?) 
                AND date_creation > ? 
                AND lu = 0 
                ORDER BY date_creation DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$idUtilisateur, $lastCheckDate]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>


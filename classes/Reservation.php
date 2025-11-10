<?php
// classes/Reservation.php

require_once __DIR__ . '/../config/database.php';

class Reservation {
    private $db;

    public function __construct($database = null) {
        // Accept PDO connection or use getDB() if not provided
        $this->db = $database ? $database : getDB();
    }

    // Vérifier la disponibilité d'un créneau
    public function checkDisponibilite($idTerrain, $dateReservation, $idCreneau) {
        $sql = "SELECT COUNT(*) as count 
                FROM reservation 
                WHERE idTerrain = ? 
                AND dateReservation = ? 
                AND idCreneau = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$idTerrain, $dateReservation, $idCreneau]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['count'] == 0;
    }

    // Créer une réservation
    public function createReservation($reservationData) {
        $sql = "INSERT INTO reservation 
                (idTerrain, idUtilisateur, dateReservation, idCreneau, demande, 
                 ballon, arbitre, maillot, douche, dateCreation) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $reservationData['idTerrain'],
            $reservationData['idUtilisateur'],
            $reservationData['dateReservation'],
            $reservationData['idCreneau'],
            $reservationData['demande'] ?? '',
            $reservationData['ballon'] ?? 0,
            $reservationData['arbitre'] ?? 0,
            $reservationData['maillot'] ?? 0,
            $reservationData['douche'] ?? 0
        ]);
    }

    // Récupérer les réservations d'un utilisateur
    public function getUserReservations($idUtilisateur) {
        $sql = "SELECT r.*, t.nom as terrain_nom, t.taille, t.type,
                       ch.heure_debut, ch.heure_fin
                FROM reservation r
                JOIN terrain t ON r.idTerrain = t.idTerrain
                JOIN creneaux_horaires ch ON r.idCreneau = ch.idCreneau
                WHERE r.idUtilisateur = ?
                ORDER BY r.dateReservation DESC, ch.heure_debut DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$idUtilisateur]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Annuler une réservation
    public function cancelReservation($idReservation, $idUtilisateur) {
        $sql = "DELETE FROM reservation 
                WHERE idReservation = ? AND idUtilisateur = ?";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$idReservation, $idUtilisateur]);
    }
}
?>
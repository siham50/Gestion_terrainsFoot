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

    // Récupérer les réservations d'un utilisateur avec toutes les informations
    public function getUserReservations($idUtilisateur) {
        $sql = "SELECT r.*, 
                       t.nom as terrain_nom, t.taille, t.type, t.photoT, t.prix as terrain_prix,
                       ch.heure_debut, ch.heure_fin,
                       COALESCE(f.montantTerrain, t.prix, 0) as montantTerrain,
                       COALESCE(f.montantService, 0) as montantService,
                       (COALESCE(f.montantTerrain, t.prix, 0) + COALESCE(f.montantService, 0)) as montantTotal
                FROM reservation r
                JOIN terrain t ON r.idTerrain = t.idTerrain
                JOIN creneaux_horaires ch ON r.idCreneau = ch.idCreneau
                LEFT JOIN facture f ON r.idReservation = f.idReservation
                WHERE r.idUtilisateur = ?
                ORDER BY r.dateReservation DESC, ch.heure_debut DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$idUtilisateur]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer les réservations à venir d'un utilisateur
    public function getUpcomingReservations($idUtilisateur) {
        $sql = "SELECT r.*, 
                       t.nom as terrain_nom, t.taille, t.type, t.photoT, t.prix as terrain_prix,
                       ch.heure_debut, ch.heure_fin,
                       COALESCE(f.montantTerrain, t.prix, 0) as montantTerrain,
                       COALESCE(f.montantService, 0) as montantService,
                       (COALESCE(f.montantTerrain, t.prix, 0) + COALESCE(f.montantService, 0)) as montantTotal
                FROM reservation r
                JOIN terrain t ON r.idTerrain = t.idTerrain
                JOIN creneaux_horaires ch ON r.idCreneau = ch.idCreneau
                LEFT JOIN facture f ON r.idReservation = f.idReservation
                WHERE r.idUtilisateur = ? 
                  AND (r.dateReservation > CURDATE() 
                       OR (r.dateReservation = CURDATE() AND ch.heure_debut > CURTIME()))
                ORDER BY r.dateReservation ASC, ch.heure_debut ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$idUtilisateur]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtenir les statistiques des réservations d'un utilisateur
    public function getUserReservationStats($idUtilisateur) {
        $sql = "SELECT 
                    COUNT(*) as total_reservations,
                    COUNT(CASE WHEN r.dateReservation > CURDATE() 
                               OR (r.dateReservation = CURDATE() AND ch.heure_debut > CURTIME()) 
                          THEN 1 END) as reservations_avenir,
                    SUM(TIMESTAMPDIFF(HOUR, ch.heure_debut, ch.heure_fin)) as total_heures,
                    SUM(CASE WHEN MONTH(r.dateReservation) = MONTH(CURDATE()) 
                             AND YEAR(r.dateReservation) = YEAR(CURDATE()) 
                        THEN TIMESTAMPDIFF(HOUR, ch.heure_debut, ch.heure_fin) ELSE 0 END) as heures_ce_mois,
                    COUNT(CASE WHEN MONTH(r.dateReservation) = MONTH(CURDATE()) 
                               AND YEAR(r.dateReservation) = YEAR(CURDATE()) 
                          THEN 1 END) as reservations_ce_mois,
                    MIN(CASE WHEN r.dateReservation >= CURDATE() 
                             OR (r.dateReservation = CURDATE() AND ch.heure_debut > CURTIME())
                        THEN r.dateReservation END) as prochaine_date
                FROM reservation r
                JOIN creneaux_horaires ch ON r.idCreneau = ch.idCreneau
                WHERE r.idUtilisateur = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$idUtilisateur]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
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
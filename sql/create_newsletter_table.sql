-- Table pour stocker les notifications/newsletter
CREATE TABLE IF NOT EXISTS `newsletter` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(50) NOT NULL DEFAULT 'general' COMMENT 'Type de notification: nouveau_terrain, modification_terrain, nouveau_tournoi, modification_prix, general',
  `titre` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `id_utilisateur` int(11) DEFAULT NULL COMMENT 'NULL = notification globale pour tous, sinon ID utilisateur spécifique',
  `date_creation` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `lu` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 = non lu, 1 = lu',
  PRIMARY KEY (`id`),
  KEY `idx_utilisateur_lu` (`id_utilisateur`, `lu`),
  KEY `idx_date_creation` (`date_creation`),
  KEY `idx_type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exemples de notifications
INSERT INTO `newsletter` (`type`, `titre`, `message`, `id_utilisateur`, `date_creation`, `lu`) VALUES
('nouveau_terrain', 'Nouveau terrain disponible', 'Un nouveau terrain synthétique a été ajouté à notre complexe sportif. Réservez dès maintenant !', NULL, NOW(), 0),
('modification_prix', 'Mise à jour des tarifs', 'Les tarifs des terrains ont été mis à jour. Consultez les nouveaux prix sur la page d\'accueil.', NULL, NOW(), 0),
('nouveau_tournoi', 'Nouveau tournoi annoncé', 'Un nouveau tournoi de football à 7 est maintenant ouvert aux inscriptions. Inscrivez-vous dès aujourd\'hui !', NULL, NOW(), 0);


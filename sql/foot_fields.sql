-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mar. 11 nov. 2025 à 17:43
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `foot_fields`
--

-- --------------------------------------------------------

--
-- Structure de la table `creneaux_horaires`
--

CREATE TABLE `creneaux_horaires` (
  `idCreneau` int(11) NOT NULL,
  `heure_debut` time NOT NULL,
  `heure_fin` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Déchargement des données de la table `creneaux_horaires`
--

INSERT INTO `creneaux_horaires` (`idCreneau`, `heure_debut`, `heure_fin`) VALUES
(1, '08:00:00', '09:00:00'),
(2, '09:00:00', '10:00:00'),
(3, '10:00:00', '11:00:00'),
(4, '11:00:00', '12:00:00'),
(5, '12:00:00', '13:00:00'),
(6, '13:00:00', '14:00:00'),
(7, '14:00:00', '15:00:00'),
(8, '15:00:00', '16:00:00'),
(9, '16:00:00', '17:00:00'),
(10, '17:00:00', '18:00:00'),
(11, '18:00:00', '19:00:00'),
(12, '19:00:00', '20:00:00'),
(13, '20:00:00', '21:00:00'),
(14, '21:00:00', '22:00:00'),
(15, '22:00:00', '23:00:00');

-- --------------------------------------------------------

--
-- Structure de la table `facture`
--

CREATE TABLE `facture` (
  `idFacture` int(11) NOT NULL,
  `idReservation` int(11) DEFAULT NULL,
  `montantTerrain` decimal(10,2) DEFAULT NULL,
  `montantService` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Structure de la table `matchs`
--

CREATE TABLE `matchs` (
  `idMatch` int(11) NOT NULL,
  `equipe` varchar(100) DEFAULT NULL,
  `equipeAdv` varchar(100) DEFAULT NULL,
  `score` varchar(20) DEFAULT NULL,
  `gagnant` varchar(100) DEFAULT NULL,
  `idTournoi` int(11) DEFAULT NULL,
  `nextMatchId` int(11) DEFAULT NULL,
  `idReservation` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Structure de la table `message`
--

CREATE TABLE `message` (
  `idNouvelle` int(11) NOT NULL,
  `type` varchar(30) DEFAULT NULL,
  `titre` varchar(100) DEFAULT NULL,
  `corps` text DEFAULT NULL,
  `photoM` varchar(255) DEFAULT NULL,
  `dateCreation` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Structure de la table `prix`
--

CREATE TABLE `prix` (
  `categorie` varchar(50) NOT NULL,
  `reference` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `prix` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Structure de la table `reservation`
--

CREATE TABLE `reservation` (
  `idReservation` int(11) NOT NULL,
  `idTerrain` int(11) DEFAULT NULL,
  `idUtilisateur` int(11) DEFAULT NULL,
  `dateReservation` date DEFAULT NULL,
  `idCreneau` int(11) DEFAULT NULL,
  `demande` text DEFAULT NULL,
  `ballon` tinyint(1) DEFAULT NULL,
  `arbitre` tinyint(1) DEFAULT NULL,
  `maillot` tinyint(1) DEFAULT NULL,
  `douche` tinyint(1) DEFAULT NULL,
  `dateCreation` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Déchargement des données de la table `reservation`
--

INSERT INTO `reservation` (`idReservation`, `idTerrain`, `idUtilisateur`, `dateReservation`, `idCreneau`, `demande`, `ballon`, `arbitre`, `maillot`, `douche`, `dateCreation`) VALUES
(2, 9, 3, '2025-12-10', 7, '', 1, 0, 0, 0, '2025-11-11 16:30:14');

-- --------------------------------------------------------

--
-- Structure de la table `terrain`
--

CREATE TABLE `terrain` (
  `idTerrain` int(11) NOT NULL,
  `nom` varchar(50) DEFAULT NULL,
  `taille` varchar(30) DEFAULT NULL,
  `type` varchar(30) DEFAULT NULL,
  `prix` decimal(10,2) DEFAULT NULL,
  `disponible` tinyint(1) DEFAULT NULL,
  `photoT` varchar(255) DEFAULT NULL,
  `date_modification` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Déchargement des données de la table `terrain`
--

INSERT INTO `terrain` (`idTerrain`, `nom`, `taille`, `type`, `prix`, `disponible`, `photoT`, `date_modification`) VALUES
(6, 'Terrain B - Synthétique', '7x7', 'foot7', 75.00, 1, 'terrain_b.jpg', '2025-11-11 16:02:05'),
(7, 'Terrain C - Naturel', '11x11', 'foot11', 120.00, 1, 'terrain_c.jpg', '2025-11-11 16:02:05'),
(8, 'Terrain D - Synthétique', '5x5', 'foot5', 45.00, 0, 'terrain_d.jpg', '2025-11-11 16:02:05'),
(9, 'Terrain E - Couvert', '7x7', 'foot7', 85.00, 1, 'terrain_e.jpg', '2025-11-11 16:02:05');

-- --------------------------------------------------------

--
-- Structure de la table `terrain_creneaux`
--

CREATE TABLE `terrain_creneaux` (
  `idTerrain` int(11) NOT NULL,
  `idCreneau` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Déchargement des données de la table `terrain_creneaux`
--

INSERT INTO `terrain_creneaux` (`idTerrain`, `idCreneau`) VALUES
(6, 1),
(6, 2),
(6, 3),
(6, 4),
(6, 5),
(6, 6),
(6, 7),
(6, 8),
(6, 9),
(6, 10),
(6, 11),
(6, 12),
(6, 13),
(6, 14),
(6, 15),
(7, 1),
(7, 2),
(7, 3),
(7, 4),
(7, 5),
(7, 6),
(7, 7),
(7, 8),
(7, 9),
(7, 10),
(7, 11),
(7, 12),
(7, 13),
(7, 14),
(7, 15),
(8, 1),
(8, 2),
(8, 3),
(8, 4),
(8, 5),
(8, 6),
(8, 7),
(8, 8),
(8, 9),
(8, 10),
(8, 11),
(8, 12),
(8, 13),
(8, 14),
(8, 15),
(9, 1),
(9, 2),
(9, 3),
(9, 4),
(9, 5),
(9, 6),
(9, 7),
(9, 8),
(9, 9),
(9, 10),
(9, 11),
(9, 12),
(9, 13),
(9, 14),
(9, 15);

-- --------------------------------------------------------

--
-- Structure de la table `tournoi`
--

CREATE TABLE `tournoi` (
  `idTournoi` int(11) NOT NULL,
  `format` varchar(50) DEFAULT NULL,
  `equipes` text DEFAULT NULL,
  `champion` varchar(100) DEFAULT NULL,
  `idUtilisateur` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

CREATE TABLE `utilisateur` (
  `idUtilisateur` int(11) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `telephone` varchar(20) NOT NULL,
  `adresse` varchar(150) DEFAULT NULL,
  `role` varchar(30) DEFAULT NULL,
  `etat` varchar(20) DEFAULT 'actif'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Déchargement des données de la table `utilisateur`
--

INSERT INTO `utilisateur` (`idUtilisateur`, `nom`, `prenom`, `email`, `password`, `telephone`, `adresse`, `role`, `etat`) VALUES
(1, 'hariss', 'houssam', 'hariss.houssam@etu.uae.ac.ma', '$2y$10$ChwF9znskA.IomIZsjuKReGyZu7TQ0Ug7X7McSiS8LEU3j2GdtCTq', '+33762760701', '90500', 'client', 'actif'),
(2, 'hariss', 'houssam', 'elfadil.assel@etu.uae.ac.ma', '$2y$10$Y02DF3vdgK.xH78Gnn1e9embwIo8O16GfFdRYJJkeIw0N70FLOFs2', '+33762760702', '90500', 'client', 'actif'),
(3, 'hariss', 'houssam', 'harissh963@gmail.com', '$2y$10$iKbJb3KqVhOt5bcgv6k6w.vSW1jn6lJzwNXD1o2sCQS9IPTWhC2Ju', '+33762760704', '90500', 'client', 'actif');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `creneaux_horaires`
--
ALTER TABLE `creneaux_horaires`
  ADD PRIMARY KEY (`idCreneau`),
  ADD KEY `heure_debut` (`heure_debut`);

--
-- Index pour la table `facture`
--
ALTER TABLE `facture`
  ADD PRIMARY KEY (`idFacture`),
  ADD KEY `idReservation` (`idReservation`);

--
-- Index pour la table `matchs`
--
ALTER TABLE `matchs`
  ADD PRIMARY KEY (`idMatch`),
  ADD KEY `idTournoi` (`idTournoi`),
  ADD KEY `nextMatchId` (`nextMatchId`),
  ADD KEY `idReservation` (`idReservation`);

--
-- Index pour la table `message`
--
ALTER TABLE `message`
  ADD PRIMARY KEY (`idNouvelle`);

--
-- Index pour la table `prix`
--
ALTER TABLE `prix`
  ADD PRIMARY KEY (`categorie`);

--
-- Index pour la table `reservation`
--
ALTER TABLE `reservation`
  ADD PRIMARY KEY (`idReservation`),
  ADD KEY `idTerrain` (`idTerrain`),
  ADD KEY `idUtilisateur` (`idUtilisateur`),
  ADD KEY `idCreneau` (`idCreneau`),
  ADD KEY `dateReservation` (`dateReservation`);

--
-- Index pour la table `terrain`
--
ALTER TABLE `terrain`
  ADD PRIMARY KEY (`idTerrain`);

--
-- Index pour la table `terrain_creneaux`
--
ALTER TABLE `terrain_creneaux`
  ADD PRIMARY KEY (`idTerrain`,`idCreneau`),
  ADD KEY `idCreneau` (`idCreneau`);

--
-- Index pour la table `tournoi`
--
ALTER TABLE `tournoi`
  ADD PRIMARY KEY (`idTournoi`),
  ADD KEY `idUtilisateur` (`idUtilisateur`);

--
-- Index pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD PRIMARY KEY (`idUtilisateur`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `creneaux_horaires`
--
ALTER TABLE `creneaux_horaires`
  MODIFY `idCreneau` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT pour la table `facture`
--
ALTER TABLE `facture`
  MODIFY `idFacture` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `matchs`
--
ALTER TABLE `matchs`
  MODIFY `idMatch` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `message`
--
ALTER TABLE `message`
  MODIFY `idNouvelle` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `reservation`
--
ALTER TABLE `reservation`
  MODIFY `idReservation` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `terrain`
--
ALTER TABLE `terrain`
  MODIFY `idTerrain` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT pour la table `tournoi`
--
ALTER TABLE `tournoi`
  MODIFY `idTournoi` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  MODIFY `idUtilisateur` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `facture`
--
ALTER TABLE `facture`
  ADD CONSTRAINT `facture_ibfk_1` FOREIGN KEY (`idReservation`) REFERENCES `reservation` (`idReservation`) ON DELETE CASCADE;

--
-- Contraintes pour la table `matchs`
--
ALTER TABLE `matchs`
  ADD CONSTRAINT `matchs_ibfk_1` FOREIGN KEY (`idTournoi`) REFERENCES `tournoi` (`idTournoi`) ON DELETE CASCADE,
  ADD CONSTRAINT `matchs_ibfk_2` FOREIGN KEY (`nextMatchId`) REFERENCES `matchs` (`idMatch`),
  ADD CONSTRAINT `matchs_ibfk_3` FOREIGN KEY (`idReservation`) REFERENCES `reservation` (`idReservation`);

--
-- Contraintes pour la table `reservation`
--
ALTER TABLE `reservation`
  ADD CONSTRAINT `reservation_ibfk_1` FOREIGN KEY (`idTerrain`) REFERENCES `terrain` (`idTerrain`) ON DELETE CASCADE,
  ADD CONSTRAINT `reservation_ibfk_2` FOREIGN KEY (`idUtilisateur`) REFERENCES `utilisateur` (`idUtilisateur`) ON DELETE CASCADE,
  ADD CONSTRAINT `reservation_ibfk_3` FOREIGN KEY (`idCreneau`) REFERENCES `creneaux_horaires` (`idCreneau`) ON DELETE SET NULL;

--
-- Contraintes pour la table `terrain_creneaux`
--
ALTER TABLE `terrain_creneaux`
  ADD CONSTRAINT `terrain_creneaux_ibfk_1` FOREIGN KEY (`idTerrain`) REFERENCES `terrain` (`idTerrain`) ON DELETE CASCADE,
  ADD CONSTRAINT `terrain_creneaux_ibfk_2` FOREIGN KEY (`idCreneau`) REFERENCES `creneaux_horaires` (`idCreneau`) ON DELETE CASCADE;

--
-- Contraintes pour la table `tournoi`
--
ALTER TABLE `tournoi`
  ADD CONSTRAINT `tournoi_ibfk_1` FOREIGN KEY (`idUtilisateur`) REFERENCES `utilisateur` (`idUtilisateur`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

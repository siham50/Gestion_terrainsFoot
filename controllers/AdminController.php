<?php

declare(strict_types=1);

namespace Controllers;

use mysqli;
use mysqli_stmt;
use RuntimeException;

/**
 * Regroupe les operations d'administration (statistiques, utilisateurs, terrains, tarifs).
 * Toutes les requetes SQL sont preparees pour limiter les injections.
 */
class AdminController
{
    private mysqli $conn;

    public function __construct(mysqli $connection)
    {
        if ($connection->connect_errno) {
            throw new RuntimeException('Connexion base de donnees indisponible pour l administration.');
        }
        $this->conn = $connection;
    }

    public function getDashboardStats(): array
    {
        $stats = [
            'total_users' => 0,
            'total_admins' => 0,
            'total_terrains' => 0,
            'total_reservations' => 0,
        ];

        $queries = [
            'total_users' => 'SELECT COUNT(*) FROM utilisateur',
            'total_admins' => 'SELECT COUNT(*) FROM utilisateur WHERE role = \'admin\'',
            'total_terrains' => 'SELECT COUNT(*) FROM terrain',
            'total_reservations' => 'SELECT COUNT(*) FROM reservation',
        ];

        foreach ($queries as $key => $sql) {
            $result = $this->conn->query($sql);
            if ($result !== false) {
                $stats[$key] = (int) $result->fetch_row()[0];
                $result->free();
            }
        }

        return $stats;
    }

    public function listUsers(): array
    {
        $sql = 'SELECT idUtilisateur AS id, nom, prenom, email, telephone, adresse, role, etat FROM utilisateur ORDER BY idUtilisateur DESC';
        $result = $this->conn->query($sql);
        if ($result === false) {
            return [];
        }

        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
        $result->free();

        return $users;
    }

    public function createUser(array $data): array
    {
        $required = ['nom', 'prenom', 'email', 'password', 'role', 'telephone'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                return ['success' => false, 'message' => 'Tous les champs obligatoires doivent etre renseignes.'];
            }
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Adresse email invalide.'];
        }

        if (!in_array($data['role'], ['client', 'admin'], true)) {
            return ['success' => false, 'message' => 'Role utilisateur invalide.'];
        }

        if ($this->emailExists($data['email'])) {
            return ['success' => false, 'message' => 'Cet email est deja utilise.'];
        }

        $etat = $data['etat'] ?? 'actif';
        if (!in_array($etat, ['actif', 'inactif', 'suspendu'], true)) {
            $etat = 'actif';
        }

        $sql = 'INSERT INTO utilisateur (nom, prenom, email, telephone, adresse, password, role, etat) VALUES (?, ?, ?, ?, ?, ?, ?, ?)';
        $stmt = $this->conn->prepare($sql);
        if (!$stmt instanceof mysqli_stmt) {
            return ['success' => false, 'message' => 'Erreur preparation requete utilisateur.'];
        }

        $telephone = $data['telephone'] ?? '';
        $adresse = $data['adresse'] ?? null;
        $passwordHash = password_hash((string) $data['password'], PASSWORD_DEFAULT);
        $stmt->bind_param(
            'ssssssss',
            $data['nom'],
            $data['prenom'],
            $data['email'],
            $telephone,
            $adresse,
            $passwordHash,
            $data['role'],
            $etat
        );

        $success = $stmt->execute();
        $stmt->close();

        return $success
            ? ['success' => true, 'message' => 'Utilisateur cree avec succes.']
            : ['success' => false, 'message' => 'Impossible de creer l utilisateur.'];
    }

    public function updateUser(int $id, array $data): array
    {
        if ($id <= 0) {
            return ['success' => false, 'message' => 'Identifiant utilisateur invalide.'];
        }

        foreach (['nom', 'prenom', 'email', 'role', 'telephone'] as $required) {
            if (empty($data[$required])) {
                return ['success' => false, 'message' => 'Tous les champs obligatoires doivent etre renseignes.'];
            }
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Adresse email invalide.'];
        }

        if (!in_array($data['role'], ['client', 'admin'], true)) {
            return ['success' => false, 'message' => 'Role utilisateur invalide.'];
        }

        if ($this->emailExists($data['email'], $id)) {
            return ['success' => false, 'message' => 'Cet email est deja associe a un autre compte.'];
        }

        $etat = $data['etat'] ?? 'actif';
        if (!in_array($etat, ['actif', 'inactif', 'suspendu'], true)) {
            $etat = 'actif';
        }

        $sql = 'UPDATE utilisateur SET nom = ?, prenom = ?, email = ?, telephone = ?, adresse = ?, role = ?, etat = ?';
        $types = 'sssssss';
        $params = [
            $data['nom'],
            $data['prenom'],
            $data['email'],
            $data['telephone'] ?? null,
            $data['adresse'] ?? null,
            $data['role'],
            $etat,
        ];

        if (!empty($data['password'])) {
            $sql .= ', password = ?';
            $types .= 's';
            $params[] = password_hash((string) $data['password'], PASSWORD_DEFAULT);
        }

        $sql .= ' WHERE idUtilisateur = ?';
        $types .= 'i';
        $params[] = $id;

        $stmt = $this->conn->prepare($sql);
        if (!$stmt instanceof mysqli_stmt) {
            return ['success' => false, 'message' => 'Erreur preparation requete utilisateur.'];
        }

        $stmt->bind_param($types, ...$params);
        $success = $stmt->execute();
        $stmt->close();

        return $success
            ? ['success' => true, 'message' => 'Utilisateur mis a jour.']
            : ['success' => false, 'message' => 'Aucune modification appliquee.'];
    }

    public function deleteUser(int $id): array
    {
        if ($id <= 0) {
            return ['success' => false, 'message' => 'Identifiant utilisateur invalide.'];
        }

        $stmt = $this->conn->prepare('DELETE FROM utilisateur WHERE idUtilisateur = ?');
        if (!$stmt instanceof mysqli_stmt) {
            return ['success' => false, 'message' => 'Suppression impossible.'];
        }

        $stmt->bind_param('i', $id);
        $success = $stmt->execute();
        $stmt->close();

        return $success
            ? ['success' => true, 'message' => 'Utilisateur supprime.']
            : ['success' => false, 'message' => 'Aucune suppression effectuee.'];
    }

    public function listTerrains(): array
    {
        $sql = 'SELECT idTerrain AS id, nom, taille, type, prix, photoT AS photo, disponible, date_modification FROM terrain ORDER BY date_modification DESC, idTerrain DESC';
        $result = $this->conn->query($sql);
        if ($result === false) {
            return [];
        }

        $terrains = [];
        while ($row = $result->fetch_assoc()) {
            $terrains[] = $row;
        }
        $result->free();

        return $terrains;
    }

    public function createTerrain(array $data): array
    {
        foreach (['nom', 'taille', 'type'] as $field) {
            if (empty($data[$field])) {
                return ['success' => false, 'message' => 'Veuillez renseigner tous les champs requis pour le terrain.'];
            }
        }

        if (!in_array($data['taille'], ['mini', 'moyen', 'grand'], true)) {
            return ['success' => false, 'message' => 'Taille de terrain invalide.'];
        }

        if (!in_array($data['type'], ['gazon_naturel', 'gazon_artificiel', 'dur'], true)) {
            return ['success' => false, 'message' => 'Type de terrain invalide.'];
        }

        $prix = isset($data['prix']) && $data['prix'] !== '' ? filter_var($data['prix'], FILTER_VALIDATE_FLOAT) : null;
        if ($prix !== null && $prix === false) {
            return ['success' => false, 'message' => 'Prix invalide.'];
        }

        $sql = 'INSERT INTO terrain (nom, taille, type, prix, disponible, photoT) VALUES (?, ?, ?, ?, ?, ?)';
        $stmt = $this->conn->prepare($sql);
        if (!$stmt instanceof mysqli_stmt) {
            return ['success' => false, 'message' => 'Erreur preparation requete terrain.'];
        }

        $photoPath = $data['photo_path'] ?? null;
        $disponible = isset($data['disponible']) ? (int) (bool) $data['disponible'] : 1;
        $stmt->bind_param('sssdis', $data['nom'], $data['taille'], $data['type'], $prix, $disponible, $photoPath);

        $success = $stmt->execute();
        $stmt->close();

        // Créer une notification pour le nouveau terrain
        if ($success) {
            $this->createTerrainNotification($data['nom'], $data['type']);
        }

        return $success
            ? ['success' => true, 'message' => 'Terrain ajoute.']
            : ['success' => false, 'message' => 'Impossible d ajouter le terrain.'];
    }

    /**
     * Créer une notification pour un nouveau terrain
     */
    private function createTerrainNotification(string $nomTerrain, string $typeTerrain): void
    {
        try {
            // Formater le type de terrain pour l'affichage
            $typeFormatted = str_replace('_', ' ', $typeTerrain);
            $typeFormatted = ucwords($typeFormatted);
            
            $titre = 'Nouveau terrain disponible : ' . $nomTerrain;
            $message = 'Un nouveau terrain "' . $nomTerrain . '" (' . $typeFormatted . ') a été ajouté à notre complexe sportif. Réservez dès maintenant !';
            
            // Créer une notification globale (id_utilisateur = NULL) avec mysqli
            $sql = 'INSERT INTO newsletter (type, titre, message, id_utilisateur, date_creation, lu) VALUES (?, ?, ?, NULL, NOW(), 0)';
            $stmt = $this->conn->prepare($sql);
            if ($stmt instanceof mysqli_stmt) {
                $type = 'nouveau_terrain';
                $stmt->bind_param('sss', $type, $titre, $message);
                $stmt->execute();
                $stmt->close();
            }
        } catch (Exception $e) {
            // Logger l'erreur mais ne pas bloquer l'ajout du terrain
            error_log('Erreur création notification terrain: ' . $e->getMessage());
        }
    }

    public function updateTerrain(int $id, array $data): array
    {
        if ($id <= 0) {
            return ['success' => false, 'message' => 'Identifiant terrain invalide.'];
        }

        foreach (['nom', 'taille', 'type'] as $field) {
            if (empty($data[$field])) {
                return ['success' => false, 'message' => 'Tous les champs requis doivent etre fournis.'];
            }
        }

        if (!in_array($data['taille'], ['mini', 'moyen', 'grand'], true)) {
            return ['success' => false, 'message' => 'Taille de terrain invalide.'];
        }

        if (!in_array($data['type'], ['gazon_naturel', 'gazon_artificiel', 'dur'], true)) {
            return ['success' => false, 'message' => 'Type de terrain invalide.'];
        }

        $prix = isset($data['prix']) && $data['prix'] !== '' ? filter_var($data['prix'], FILTER_VALIDATE_FLOAT) : null;
        if ($prix !== null && $prix === false) {
            return ['success' => false, 'message' => 'Prix invalide.'];
        }

        $sql = 'UPDATE terrain SET nom = ?, taille = ?, type = ?, prix = ?, disponible = ?';
        $types = 'sssdi';
        $params = [
            $data['nom'],
            $data['taille'],
            $data['type'],
            $prix,
            isset($data['disponible']) ? (int) (bool) $data['disponible'] : 0,
        ];

        if (array_key_exists('photo_path', $data)) {
            $sql .= ', photoT = ?';
            $types .= 's';
            $params[] = $data['photo_path'];
        }

        $sql .= ' WHERE idTerrain = ?';
        $types .= 'i';
        $params[] = $id;

        $stmt = $this->conn->prepare($sql);
        if (!$stmt instanceof mysqli_stmt) {
            return ['success' => false, 'message' => 'Erreur preparation requete terrain.'];
        }

        $stmt->bind_param($types, ...$params);

        $success = $stmt->execute();
        $stmt->close();

        return $success
            ? ['success' => true, 'message' => 'Terrain mis a jour.']
            : ['success' => false, 'message' => 'Aucune mise a jour appliquee.'];
    }

    public function deleteTerrain(int $id): array
    {
        if ($id <= 0) {
            return ['success' => false, 'message' => 'Identifiant terrain invalide.'];
        }

        $stmt = $this->conn->prepare('DELETE FROM terrain WHERE idTerrain = ?');
        if (!$stmt instanceof mysqli_stmt) {
            return ['success' => false, 'message' => 'Suppression impossible.'];
        }

        $stmt->bind_param('i', $id);
        $success = $stmt->execute();
        $stmt->close();

        return $success
            ? ['success' => true, 'message' => 'Terrain supprime.']
            : ['success' => false, 'message' => 'Aucune suppression effectuee.'];
    }

    public function listPrices(): array
    {
        $sql = 'SELECT categorie, reference, description, prix FROM prix ORDER BY categorie';
        $result = $this->conn->query($sql);
        if ($result === false) {
            return [];
        }

        $items = [];
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }
        $result->free();

        return $items;
    }

    public function createPrice(array $data): array
    {
        if (empty($data['categorie'])) {
            return ['success' => false, 'message' => 'Categorie obligatoire.'];
        }

        $prixValeur = filter_var($data['prix'], FILTER_VALIDATE_FLOAT);
        if ($prixValeur === false) {
            return ['success' => false, 'message' => 'Montant de tarif invalide.'];
        }

        $categorie = strtolower(trim((string) $data['categorie']));
        if (!in_array($categorie, ['terrain', 'service'], true)) {
            return ['success' => false, 'message' => 'Categorie autorisee : terrain ou service.'];
        }

        $sql = 'INSERT INTO prix (categorie, reference, description, prix) VALUES (?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE reference = VALUES(reference), description = VALUES(description), prix = VALUES(prix)';
        $stmt = $this->conn->prepare($sql);
        if (!$stmt instanceof mysqli_stmt) {
            return ['success' => false, 'message' => 'Erreur preparation requete tarif.'];
        }

        $description = $data['description'] ?? null;
        $reference = $data['reference'] ?? null;
        $stmt->bind_param('sssd', $categorie, $reference, $description, $prixValeur);

        $success = $stmt->execute();
        $stmt->close();

        return $success
            ? ['success' => true, 'message' => 'Tarif enregistre.']
            : ['success' => false, 'message' => 'Impossible d enregistrer le tarif.'];
    }

    public function updatePrice(string $currentCategorie, array $data): array
    {
        $currentCategorie = trim($currentCategorie);
        if ($currentCategorie === '') {
            return ['success' => false, 'message' => 'Categorie source invalide.'];
        }

        if (empty($data['categorie'])) {
            return ['success' => false, 'message' => 'Nouvelle categorie requise.'];
        }

        $prixValeur = filter_var($data['prix'], FILTER_VALIDATE_FLOAT);
        if ($prixValeur === false) {
            return ['success' => false, 'message' => 'Montant de tarif invalide.'];
        }

        $nextCategorie = strtolower(trim((string) $data['categorie']));
        if (!in_array($nextCategorie, ['terrain', 'service'], true)) {
            return ['success' => false, 'message' => 'Categorie autorisee : terrain ou service.'];
        }

        $sql = 'UPDATE prix SET categorie = ?, reference = ?, description = ?, prix = ? WHERE categorie = ?';
        $stmt = $this->conn->prepare($sql);
        if (!$stmt instanceof mysqli_stmt) {
            return ['success' => false, 'message' => 'Erreur preparation requete tarif.'];
        }

        $description = $data['description'] ?? null;
        $reference = $data['reference'] ?? null;
        $stmt->bind_param('sssds', $nextCategorie, $reference, $description, $prixValeur, $currentCategorie);

        $success = $stmt->execute();
        $stmt->close();

        return $success
            ? ['success' => true, 'message' => 'Tarif mis a jour.']
            : ['success' => false, 'message' => 'Aucune mise a jour appliquee.'];
    }

    public function deletePrice(string $categorie): array
    {
        $categorie = trim($categorie);
        if ($categorie === '') {
            return ['success' => false, 'message' => 'Categorie invalide.'];
        }

        $stmt = $this->conn->prepare('DELETE FROM prix WHERE categorie = ?');
        if (!$stmt instanceof mysqli_stmt) {
            return ['success' => false, 'message' => 'Suppression impossible.'];
        }

        $stmt->bind_param('s', $categorie);
        $success = $stmt->execute();
        $stmt->close();

        return $success
            ? ['success' => true, 'message' => 'Tarif supprime.']
            : ['success' => false, 'message' => 'Aucune suppression effectuee.'];
    }

    private function emailExists(string $email, ?int $excludeId = null): bool
    {
        if ($excludeId) {
            $stmt = $this->conn->prepare('SELECT COUNT(*) FROM utilisateur WHERE email = ? AND idUtilisateur != ?');
            if (!$stmt instanceof mysqli_stmt) {
                return false;
            }
            $stmt->bind_param('si', $email, $excludeId);
        } else {
            $stmt = $this->conn->prepare('SELECT COUNT(*) FROM utilisateur WHERE email = ?');
            if (!$stmt instanceof mysqli_stmt) {
                return false;
            }
            $stmt->bind_param('s', $email);
        }

        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        return (int) $count > 0;
    }
}

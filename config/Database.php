<?php
// config/database.php

require_once 'config.php';

class Database {
    private static $instance = null;
    private $conn;
    
    private $host = DB_HOST;
    private $db_name = DB_NAME;
    private $username = DB_USER;
    private $password = DB_PASS;
    private $charset = DB_CHARSET;
    private $options = DB_OPTIONS;
    
    // Constructeur privé pour le pattern Singleton
    private function __construct() {
        $this->connect();
    }
    
    // Méthode pour obtenir l'instance unique
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }
    
    // Méthode de connexion
    private function connect() {
        try {
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=" . $this->charset;
            $this->conn = new PDO($dsn, $this->username, $this->password, $this->options);
            
            // Définir le fuseau horaire
            $this->conn->exec("SET time_zone = '+00:00'");
            
        } catch(PDOException $exception) {
            // En environnement de production, logger l'erreur au lieu de l'afficher
            error_log("Erreur de connexion BD: " . $exception->getMessage());
            throw new Exception("Erreur de connexion à la base de données");
        }
    }
    
    // Méthode pour obtenir la connexion
    public function getConnection() {
        // Vérifier si la connexion est toujours active
        try {
            $this->conn->query('SELECT 1');
        } catch (PDOException $e) {
            $this->connect();
        }
        
        return $this->conn;
    }
    
    // Méthode pour exécuter une requête
    public function query($sql, $params = []) {
        try {
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch(PDOException $e) {
            error_log("Erreur requête SQL: " . $e->getMessage());
            throw new Exception("Erreur lors de l'exécution de la requête");
        }
    }
    
    // Récupérer un seul enregistrement
    public function fetchOne($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetch();
    }
    
    // Récupérer tous les enregistrements
    public function fetchAll($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }
    
    // Insérer et retourner l'ID
    public function insert($sql, $params = []) {
        $this->query($sql, $params);
        return $this->getConnection()->lastInsertId();
    }
    
    // Mettre à jour
    public function update($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }
    
    // Supprimer
    public function delete($sql, $params = []) {
        return $this->update($sql, $params);
    }
    
    // Démarrer une transaction
    public function beginTransaction() {
        return $this->getConnection()->beginTransaction();
    }
    
    // Valider une transaction
    public function commit() {
        return $this->getConnection()->commit();
    }
    
    // Annuler une transaction
    public function rollBack() {
        return $this->getConnection()->rollBack();
    }
}

// Fonctions globales pour un accès facile

/**
 * Obtenir la connexion à la base de données
 */
function getDB() {
    return Database::getInstance()->getConnection();
}

/**
 * Exécuter une requête et récupérer un seul résultat
 */
function dbFetchOne($sql, $params = []) {
    return Database::getInstance()->fetchOne($sql, $params);
}

/**
 * Exécuter une requête et récupérer tous les résultats
 */
function dbFetchAll($sql, $params = []) {
    return Database::getInstance()->fetchAll($sql, $params);
}

/**
 * Insérer des données et retourner l'ID
 */
function dbInsert($sql, $params = []) {
    return Database::getInstance()->insert($sql, $params);
}

/**
 * Mettre à jour des données
 */
function dbUpdate($sql, $params = []) {
    return Database::getInstance()->update($sql, $params);
}

/**
 * Supprimer des données
 */
function dbDelete($sql, $params = []) {
    return Database::getInstance()->delete($sql, $params);
}

// Initialisation de la connexion
try {
    $database = Database::getInstance();
    $db = $database->getConnection();
} catch (Exception $e) {
    // Gérer l'erreur de connexion
    die("Impossible de se connecter à la base de données. Veuillez réessayer plus tard.");
}
?>
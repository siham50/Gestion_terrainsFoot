<?php

require_once __DIR__ . '/../config/config.php';

class Database {
    private static $instance = null;
    private $conn;
    
    private $host = DB_HOST;
    private $db_name = DB_NAME;
    private $username = DB_USER;
    private $password = DB_PASS;
    private $charset = DB_CHARSET;
    private $options = DB_OPTIONS;
    
    private function __construct() {
        $this->connect();
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }
    
    private function connect() {
        try {
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=" . $this->charset;
            $this->conn = new PDO($dsn, $this->username, $this->password, $this->options);
            $this->conn->exec("SET time_zone = '+00:00'");
        } catch(PDOException $exception) {
            error_log("Erreur de connexion BD: " . $exception->getMessage());
            throw new Exception("Erreur de connexion à la base de données");
        }
    }
    
    public function getConnection() {
        try {
            $this->conn->query('SELECT 1');
        } catch (PDOException $e) {
            $this->connect();
        }
        return $this->conn;
    }
    
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
    
    public function fetchOne($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetch();
    }
    
    public function fetchAll($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }
    
    public function insert($sql, $params = []) {
        $this->query($sql, $params);
        return $this->getConnection()->lastInsertId();
    }
    
    public function update($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }
    
    public function delete($sql, $params = []) {
        return $this->update($sql, $params);
    }
    
    public function beginTransaction() {
        return $this->getConnection()->beginTransaction();
    }
    
    public function commit() {
        return $this->getConnection()->commit();
    }
    
    public function rollBack() {
        return $this->getConnection()->rollBack();
    }
}

function getDB() {
    return Database::getInstance()->getConnection();
}

function dbFetchOne($sql, $params = []) {
    return Database::getInstance()->fetchOne($sql, $params);
}

function dbFetchAll($sql, $params = []) {
    return Database::getInstance()->fetchAll($sql, $params);
}

function dbInsert($sql, $params = []) {
    return Database::getInstance()->insert($sql, $params);
}

function dbUpdate($sql, $params = []) {
    return Database::getInstance()->update($sql, $params);
}

function dbDelete($sql, $params = []) {
    return Database::getInstance()->delete($sql, $params);
}

try {
    $database = Database::getInstance();
    $db = $database->getConnection();
} catch (Exception $e) {
    die("Impossible de se connecter à la base de données. Veuillez réessayer plus tard.");
}
?>
<?php
class Utilisateur {
    private $conn;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function emailExists($email) {
        $sql = "SELECT idUtilisateur FROM utilisateur WHERE email = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }
    
    public function phoneExists($phone) {
        if (empty($phone)) return false;
        
        $sql = "SELECT idUtilisateur FROM utilisateur WHERE telephone = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $phone);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }
    
    public function create($data) {
        $sql = "INSERT INTO utilisateur (nom, prenom, email, telephone, adresse, password, role, etat) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssssssss", 
            $data['nom'], 
            $data['prenom'], 
            $data['email'], 
            $data['telephone'], 
            $data['adresse'], 
            $data['password'], 
            $data['role'], 
            $data['etat']
        );
        
        if ($stmt->execute()) {
            return $this->conn->insert_id;
        }
        return false;
    }
    
    public function getByEmail($email) {
        $sql = "SELECT * FROM utilisateur WHERE email = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    
    public function updatePassword($user_id, $hashed_password) {
        $sql = "UPDATE utilisateur SET password = ? WHERE idUtilisateur = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $hashed_password, $user_id);
        return $stmt->execute();
    }
    
    public function validateLogin($email, $password) {
        $user = $this->getByEmail($email);
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }
}
?>
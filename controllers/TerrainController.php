<?php
// controllers/TerrainController.php

require_once __DIR__ . '/../classes/Terrain.php';
require_once __DIR__ . '/../config/database.php';

class TerrainController {
    private $terrain;
    private $conn;

    public function __construct() {
        $this->terrain = new Terrain();
        $this->conn = getDB();
    }

    // Afficher la page d'accueil
    public function index() {
        // Test des données avant d'afficher
        $testData = $this->terrain->testConnection();
        
        // Marquer que le contenu est affiché
        $GLOBALS['contentDisplayed'] = true;
        
        // Passer les données de test à la vue si nécessaire
        require_once __DIR__ . '/../views/public/Home.php';
    }

    // API pour AJAX - obtenir les données des terrains - VERSION DÉBOGAGE
    public function getTerrainsData() {
        // Headers pour CORS et JSON
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST');
        header('Access-Control-Allow-Headers: Content-Type');

        try {
            // Test de connexion d'abord
            $test = $this->terrain->testConnection();
            
            if (!$test['success']) {
                throw new Exception("Erreur connexion BD: " . $test['error']);
            }

            // Récupération des données
            $data = $this->terrain->getDataForAjax();
            
            // Réponse de débogage détaillée
            $response = [
                'success' => true,
                'data' => $data,
                'debug_info' => [
                    'timestamp' => date('Y-m-d H:i:s'),
                    'terrains_total' => $test['terrains_count'],
                    'prix_total' => $test['prix_count'],
                    'disponibles_count' => count($data['disponibles']),
                    'indisponibles_count' => count($data['indisponibles']),
                    'memory_usage' => round(memory_get_usage() / 1024 / 1024, 2) . ' MB'
                ],
                'sample_data' => [
                    'first_terrain' => !empty($data['disponibles']) ? $data['disponibles'][0] : 'Aucun terrain disponible',
                    'first_indispo' => !empty($data['indisponibles']) ? $data['indisponibles'][0] : 'Aucun terrain indisponible'
                ]
            ];

            echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            exit;
            
        } catch (Exception $e) {
            // Réponse d'erreur détaillée
            $errorResponse = [
                'success' => false,
                'message' => 'Erreur lors de la récupération des données',
                'error' => $e->getMessage(),
                'debug' => [
                    'timestamp' => date('Y-m-d H:i:s'),
                    'file' => __FILE__,
                    'line' => $e->getLine()
                ]
            ];
            
            error_log("Erreur getTerrainsData: " . $e->getMessage());
            echo json_encode($errorResponse, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            exit;
        }
    }

    // API pour AJAX - ajouter un terrain
    public function addTerrain() {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        
        try {
            if ($_POST) {
                $this->terrain->nom = $_POST['nom'] ?? '';
                $this->terrain->taille = $_POST['taille'] ?? '';
                $this->terrain->type = $_POST['type'] ?? '';
                $this->terrain->disponible = 1;
                $this->terrain->photoT = $_POST['photoT'] ?? 'default.jpg';
                
                if (empty($this->terrain->nom) || empty($this->terrain->taille)) {
                    throw new Exception("Nom et taille sont obligatoires");
                }
                
                $id = $this->terrain->create();
                
                if ($id) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Terrain ajouté avec succès',
                        'id' => $id
                    ]);
                } else {
                    throw new Exception("Erreur lors de l'insertion en base");
                }
            } else {
                throw new Exception("Données POST manquantes");
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Erreur lors de l\'ajout du terrain',
                'error' => $e->getMessage()
            ]);
        }
    }

    // API pour AJAX - supprimer un terrain
    public function deleteTerrain() {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        
        try {
            if ($_POST && isset($_POST['idTerrain'])) {
                $idTerrain = intval($_POST['idTerrain']);
                
                if ($idTerrain <= 0) {
                    throw new Exception("ID de terrain invalide");
                }
                
                $success = $this->terrain->delete($idTerrain);
                
                if ($success) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Terrain supprimé avec succès'
                    ]);
                } else {
                    throw new Exception("Erreur lors de la suppression");
                }
            } else {
                throw new Exception("ID de terrain manquant");
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Erreur lors de la suppression du terrain',
                'error' => $e->getMessage()
            ]);
        }
    }

// API pour AJAX - mettre à jour la disponibilité
public function updateDisponibilite() {
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');
    
    try {
        if ($_POST && isset($_POST['idTerrain']) && isset($_POST['disponible'])) {
            $idTerrain = intval($_POST['idTerrain']);
            $disponible = intval($_POST['disponible']) ? 1 : 0;
            
            // Mettre à jour avec la date de modification
            $query = "UPDATE terrain 
                      SET disponible = :disponible, 
                          date_modification = NOW()  -- ← Met à jour la date
                      WHERE idTerrain = :idTerrain";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':disponible', $disponible);
            $stmt->bindParam(':idTerrain', $idTerrain);
            $success = $stmt->execute();
            
            if ($success) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Disponibilité mise à jour avec succès'
                ]);
            } else {
                throw new Exception("Erreur lors de la mise à jour");
            }
        } else {
            throw new Exception("Données manquantes");
        }
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Erreur lors de la mise à jour',
            'error' => $e->getMessage()
        ]);
    }
}

    // NOUVELLE MÉTHODE : Test de débogage
    public function testDebug() {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        
        try {
            $testData = $this->terrain->testConnection();
            echo json_encode($testData, JSON_PRETTY_PRINT);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ], JSON_PRETTY_PRINT);
        }
    }
}

// Gestion des routes pour AJAX
if (isset($_GET['action'])) {
    $controller = new TerrainController();
    
    switch ($_GET['action']) {
        case 'get_terrains_data':
            $controller->getTerrainsData();
            break;
        case 'add_terrain':
            $controller->addTerrain();
            break;
        case 'delete_terrain':
            $controller->deleteTerrain();
            break;
        case 'update_disponibilite':
            $controller->updateDisponibilite();
            break;
        case 'test_debug':
            $controller->testDebug();
            break;
    }
}
?>
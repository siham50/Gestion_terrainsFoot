<?php
// index.php - Point d'entrée principal avec routage

// Inclure la configuration de la base de données
require_once __DIR__ . '/config/database.php';

// Définir la page par défaut
$page = isset($_GET['page']) ? $_GET['page'] : 'home';

// Gestion des routes AJAX
if (isset($_GET['action'])) {
    require_once __DIR__ . '/controllers/TerrainController.php';
    $controller = new TerrainController();
    
    switch ($_GET['action']) {
        case 'get_terrains_data':
            $controller->getTerrainsData();
            exit;
        case 'add_terrain':
            $controller->addTerrain();
            exit;
        case 'delete_terrain':
            $controller->deleteTerrain();
            exit;
        case 'update_disponibilite':
            $controller->updateDisponibilite();
            exit;
    }
}

// Routage des pages principales
switch ($page) {
    case 'home':
        require_once __DIR__ . '/controllers/TerrainController.php';
        $controller = new TerrainController();
        $controller->index();
        break;
        
   case 'reservation':
    $GLOBALS['contentDisplayed'] = true;
    if (file_exists(__DIR__ . '/views/public/Reservation_form.php')) {
        include __DIR__ . '/views/public/Reservation_form.php';
    } else {
        echo '<div class="ft-container"><h1>Réservation</h1><p>Page en construction</p></div>';
    }
    break;
        
    case 'mes-reservations':
        $GLOBALS['contentDisplayed'] = true;
        if (file_exists(__DIR__ . '/views/public/MesReservations.php')) {
            include __DIR__ . '/views/public/MesReservations.php';
        } else {
            echo '<div class="ft-container"><h1>Mes Réservations</h1><p>Page en construction</p></div>';
        }
        break;
        
    case 'tournois':
        $GLOBALS['contentDisplayed'] = true;
        if (file_exists(__DIR__ . '/views/public/Tournois.php')) {
            include __DIR__ . '/views/public/Tournois.php';
        } else {
            echo '<div class="ft-container"><h1>Tournois</h1><p>Page en construction</p></div>';
        }
        break;
        
    case 'newsletter':
        $GLOBALS['contentDisplayed'] = true;
        if (file_exists(__DIR__ . '/views/public/Newsletter.php')) {
            include __DIR__ . '/views/public/Newsletter.php';
        } else {
            echo '<div class="ft-container"><h1>Newsletter</h1><p>Page en construction</p></div>';
        }
        break;
        
    case 'login':
        $GLOBALS['contentDisplayed'] = true;
        if (file_exists(__DIR__ . '/views/auth/Login.php')) {
            include __DIR__ . '/views/auth/Login.php';
        } else {
            echo '<div class="ft-container"><h1>Connexion</h1><p>Page en construction</p></div>';
        }
        break;
        
    case 'register':
        $GLOBALS['contentDisplayed'] = true;
        if (file_exists(__DIR__ . '/views/auth/Register.php')) {
            include __DIR__ . '/views/auth/Register.php';
        } else {
            echo '<div class="ft-container"><h1>Inscription</h1><p>Page en construction</p></div>';
        }
        break;
        
    case 'admin':
        $GLOBALS['contentDisplayed'] = true;
        if (file_exists(__DIR__ . '/views/admin/Dashboard.php')) {
            include __DIR__ . '/views/admin/Dashboard.php';
        } else {
            echo '<div class="ft-container"><h1>Administration</h1><p>Page en construction</p></div>';
        }
        break;
        
    case 'admin-ajouter-terrain':
        $GLOBALS['contentDisplayed'] = true;
        if (file_exists(__DIR__ . '/views/admin/Ajouter_terrain.php')) {
            include __DIR__ . '/views/admin/Ajouter_terrain.php';
        } else {
            echo '<div class="ft-container"><h1>Ajouter un Terrain</h1><p>Page en construction</p></div>';
        }
        break;
        
    case 'admin-modifier-prix':
        $GLOBALS['contentDisplayed'] = true;
        if (file_exists(__DIR__ . '/views/admin/Modifier_prix.php')) {
            include __DIR__ . '/views/admin/Modifier_prix.php';
        } else {
            echo '<div class="ft-container"><h1>Modifier les Prix</h1><p>Page en construction</p></div>';
        }
        break;
        
    case 'admin-reservations':
        $GLOBALS['contentDisplayed'] = true;
        if (file_exists(__DIR__ . '/views/admin/Liste_reservations.php')) {
            include __DIR__ . '/views/admin/Liste_reservations.php';
        } else {
            echo '<div class="ft-container"><h1>Réservations Admin</h1><p>Page en construction</p></div>';
        }
        break;
        
    default:
        // Page 404 si la page n'existe pas
        http_response_code(404);
        $GLOBALS['contentDisplayed'] = true;
        if (file_exists(__DIR__ . '/views/errors/404.php')) {
            include __DIR__ . '/views/errors/404.php';
        } else {
            echo '<div class="ft-container"><h1>404 - Page non trouvée</h1><p>La page que vous recherchez n\'existe pas.</p></div>';
        }
        break;
}
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>FootTime<?php echo $page !== 'home' ? ' - ' . htmlspecialchars(ucfirst(str_replace('-', ' ', $page))) : ''; ?></title>
  <link rel="stylesheet" href="assets/css/Style.css">
  
  <!-- Charger CSS admin si page admin -->
  <?php if (strpos($page, 'admin') === 0): ?>
  <link rel="stylesheet" href="assets/css/Admin.css">
  <?php endif; ?>
  
  <!-- Script AJAX pour les fonctionnalités en temps réel -->
  <script>
  // Configuration globale pour AJAX - 1 SECONDE
  const AJAX_CONFIG = {
      updateInterval: 1000,
      controllerUrl: 'index.php'
  };
  </script>
</head>
<body>
  <?php 
  // Ne pas afficher la navbar sur les pages d'authentification
  if ($page !== 'login' && $page !== 'register'): 
      require __DIR__ . '/includes/Navbar.php'; 
  endif; 
  ?>

  <div class="ft-shell">
    <main class="ft-content" aria-label="Contenu principal">
      <!-- Le contenu spécifique à chaque page sera chargé ici via le routage -->
      <?php
      // Le contenu est déjà affiché par les contrôleurs/vues inclus plus haut
      // Cette section sert de fallback si aucune vue n'a été affichée
      if (!isset($contentDisplayed)) {
          echo '<div class="ft-container">';
          echo '<h1>Bienvenue sur FootTime</h1>';
          echo '<p>Page: ' . htmlspecialchars($page) . '</p>';
          echo '</div>';
      }
      ?>
    </main>
  </div>

  <?php 
  // Ne pas afficher le footer sur les pages d'authentification
  if ($page !== 'login' && $page !== 'register'): 
      require __DIR__ . '/includes/Footer.php'; 
  endif; 
  ?>

  <!-- Scripts JavaScript -->
  <script src="assets/js/main.js"></script>
  
  <!-- Charger scripts spécifiques -->
  <?php if ($page === 'home'): ?>
  <script src="assets/js/Disponibilite.js"></script>
  <?php endif; ?>
  
  <?php if ($page === 'newsletter'): ?>
  <script src="assets/js/Newsletter.js"></script>
  <?php endif; ?>
  
  <!-- Script AJAX pour les mises à jour en temps réel (uniquement sur home) -->
  <script>
  // Fonction pour charger les données des terrains via AJAX
  function loadTerrainsData() {
      if (typeof XMLHttpRequest === 'undefined') return;
      
      const xhr = new XMLHttpRequest();
      xhr.open('GET', AJAX_CONFIG.controllerUrl + '?action=get_terrains_data', true);
      xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
      
      xhr.onreadystatechange = function() {
          if (xhr.readyState === 4 && xhr.status === 200) {
              try {
                  const response = JSON.parse(xhr.responseText);
                  if (response.success && typeof updateTerrainsUI === 'function') {
                      updateTerrainsUI(response.data);
                  }
              } catch (e) {
                  console.error('Erreur parsing JSON:', e);
              }
          }
      };
      
      xhr.send();
  }

  // Démarrer les mises à jour automatiques si on est sur la page d'accueil
  document.addEventListener('DOMContentLoaded', function() {
      <?php if ($page === 'home'): ?>
      // Mise à jour périodique toutes les 1 secondes pour la page d'accueil
      setInterval(loadTerrainsData, AJAX_CONFIG.updateInterval);
      
      // Premier chargement
      setTimeout(loadTerrainsData, 500);
      <?php endif; ?>
  });
  </script>
</body>
</html>
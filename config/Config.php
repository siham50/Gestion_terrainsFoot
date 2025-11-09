<?php
// config/config.php

// Configuration de la base de données
define('DB_HOST', 'localhost');
define('DB_NAME', 'foot_fields');
define('DB_USER', 'root'); 
define('DB_PASS', ''); 
define('DB_CHARSET', 'utf8mb4');

// Options PDO
define('DB_OPTIONS', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
]);

// Chemins de l'application
define('ROOT_PATH', dirname(dirname(__FILE__)));
define('CONFIG_PATH', __DIR__);

// Statuts et constantes de l'application
define('USER_ROLE_CLIENT', 'client');
define('USER_ROLE_ADMIN', 'admin');
define('USER_ACTIVE', 'actif');
define('USER_INACTIVE', 'inactif');
?>
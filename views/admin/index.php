<?php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/../../config/conn.php';
require_once __DIR__ . '/../../controllers/AdminController.php';

use Controllers\AdminController;

if (!isset($_SESSION['user_id'], $_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../views/public/login.php?error=' . urlencode('Acces reserve aux administrateurs.'));
    exit();
}

if (!function_exists('ft_null_string')) {
    function ft_null_string(?string $value): ?string
    {
        $value = trim((string) $value);
        return $value === '' ? null : $value;
    }
}

if (!function_exists('ft_removeTerrainPhoto')) {
    function ft_removeTerrainPhoto(?string $relativePath): void
    {
        if (!$relativePath) {
            return;
        }

        $baseDir = realpath(__DIR__ . '/../../');
        if ($baseDir === false) {
            return;
        }

        $normalized = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, ltrim($relativePath, '/\\'));
        $fullPath = $baseDir . DIRECTORY_SEPARATOR . $normalized;
        $fullPathReal = realpath($fullPath) ?: $fullPath;

        if (strpos($fullPathReal, $baseDir) !== 0) {
            return;
        }

        if (is_file($fullPathReal)) {
            @unlink($fullPathReal);
        }

        // Nettoyer l'ancien emplacement (views/assets) si nécessaire
        $legacyPath = $baseDir . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . $normalized;
        if (is_file($legacyPath)) {
            @unlink($legacyPath);
        }
    }
}

if (!function_exists('ft_handleTerrainUpload')) {
    function ft_handleTerrainUpload(string $fieldName, ?string $previousPath = null): array
    {
        if (!isset($_FILES[$fieldName]) || $_FILES[$fieldName]['error'] === UPLOAD_ERR_NO_FILE) {
            return [
                'success' => true,
                'hasFile' => false,
                'path' => $previousPath,
                'previous' => $previousPath,
            ];
        }

        $file = $_FILES[$fieldName];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'Erreur lors du telechargement de l image (code ' . $file['error'] . ').'];
        }

        $allowedMime = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
        ];

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = $finfo ? finfo_file($finfo, $file['tmp_name']) : null;
        if ($finfo) {
            finfo_close($finfo);
        }

        if (!$mime || !isset($allowedMime[$mime])) {
            return ['success' => false, 'message' => 'Format d image non supporte.'];
        }

        $baseDir = realpath(__DIR__ . '/../../');
        if ($baseDir === false) {
            return ['success' => false, 'message' => 'Répertoire de base introuvable.'];
        }

        $uploadDir = $baseDir . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'terrains';
        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0775, true)) {
            return ['success' => false, 'message' => 'Impossible de preparer le dossier d upload.'];
        }

        try {
            $token = bin2hex(random_bytes(16));
        } catch (Throwable $exception) {
            $token = uniqid('terrain_', true);
        }

        $filename = 'terrain_' . $token . '.' . $allowedMime[$mime];
        $targetPath = $uploadDir . DIRECTORY_SEPARATOR . $filename;

        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            return ['success' => false, 'message' => 'Impossible d enregistrer le fichier telecharge.'];
        }

        $relativePath = 'assets/uploads/terrains/' . $filename;

        return [
            'success' => true,
            'hasFile' => true,
            'path' => $relativePath,
            'previous' => $previousPath,
        ];
    }
}

$adminController = new AdminController($conn);
$feedback = null;

$allowedSections = ['users', 'terrains', 'prices'];
$section = $_GET['section'] ?? 'users';
if (!in_array($section, $allowedSections, true)) {
    $section = 'users';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $postedSection = $_POST['section'] ?? $section;
    if (in_array($postedSection, $allowedSections, true)) {
        $section = $postedSection;
    }

    switch ($action) {
        case 'create_user':
            $feedback = $adminController->createUser([
                'nom' => trim($_POST['nom'] ?? ''),
                'prenom' => trim($_POST['prenom'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'telephone' => trim($_POST['telephone'] ?? ''),
                'adresse' => ft_null_string($_POST['adresse'] ?? null),
                'password' => $_POST['password'] ?? '',
                'role' => $_POST['role'] ?? 'client',
                'etat' => $_POST['etat'] ?? 'actif',
            ]);
            break;

        case 'update_user':
            $feedback = $adminController->updateUser(
                (int) ($_POST['id'] ?? 0),
                [
                    'nom' => trim($_POST['nom'] ?? ''),
                    'prenom' => trim($_POST['prenom'] ?? ''),
                    'email' => trim($_POST['email'] ?? ''),
                    'telephone' => trim($_POST['telephone'] ?? ''),
                    'adresse' => ft_null_string($_POST['adresse'] ?? null),
                    'password' => $_POST['password'] ?? '',
                    'role' => $_POST['role'] ?? 'client',
                    'etat' => $_POST['etat'] ?? 'actif',
                ]
            );
            break;

        case 'delete_user':
            $userId = (int) ($_POST['id'] ?? 0);
            if ($userId === (int) ($_SESSION['user_id'] ?? 0)) {
                $feedback = ['success' => false, 'message' => 'Vous ne pouvez pas supprimer votre propre compte actif.'];
            } else {
                $feedback = $adminController->deleteUser($userId);
            }
            break;

        case 'create_terrain':
            $upload = ft_handleTerrainUpload('photo');
            if (!$upload['success']) {
                $feedback = $upload;
                break;
            }

            $feedback = $adminController->createTerrain([
                'nom' => trim($_POST['nom'] ?? ''),
                'taille' => $_POST['taille'] ?? '',
                'type' => $_POST['type'] ?? '',
                'prix' => $_POST['prix'] ?? null,
                'photo_path' => $upload['hasFile'] ? $upload['path'] : null,
                'disponible' => isset($_POST['disponible']) ? 1 : 0,
            ]);

            if (!($feedback['success'] ?? false) && $upload['hasFile']) {
                ft_removeTerrainPhoto($upload['path']);
            }
            break;

        case 'update_terrain':
            $terrainId = (int) ($_POST['id'] ?? 0);
            $currentPhoto = $_POST['current_photo'] ?? null;
            $clearPhoto = isset($_POST['clear_photo']);

            if ($clearPhoto) {
                $upload = [
                    'success' => true,
                    'hasFile' => false,
                    'path' => $currentPhoto,
                    'previous' => $currentPhoto,
                ];
            } else {
                $upload = ft_handleTerrainUpload('photo', $currentPhoto);
                if (!$upload['success']) {
                    $feedback = $upload;
                    break;
                }
            }

            $payload = [
                'nom' => trim($_POST['nom'] ?? ''),
                'taille' => $_POST['taille'] ?? '',
                'type' => $_POST['type'] ?? '',
                'prix' => $_POST['prix'] ?? null,
                'disponible' => isset($_POST['disponible']) ? 1 : 0,
            ];

            if ($clearPhoto) {
                $payload['photo_path'] = null;
            } elseif ($upload['hasFile']) {
                $payload['photo_path'] = $upload['path'];
            }

            $feedback = $adminController->updateTerrain($terrainId, $payload);

            if ($feedback['success'] ?? false) {
                if ($clearPhoto && $currentPhoto) {
                    ft_removeTerrainPhoto($currentPhoto);
                } elseif ($upload['hasFile'] && $upload['previous']) {
                    ft_removeTerrainPhoto($upload['previous']);
                }
            } elseif ($upload['hasFile']) {
                ft_removeTerrainPhoto($upload['path']);
            }
            break;

        case 'delete_terrain':
            $photoPath = $_POST['photo'] ?? null;
            $feedback = $adminController->deleteTerrain((int) ($_POST['id'] ?? 0));
            if (($feedback['success'] ?? false) && $photoPath) {
                ft_removeTerrainPhoto($photoPath);
            }
            break;

        case 'create_price':
            $feedback = $adminController->createPrice([
                'categorie' => trim($_POST['categorie'] ?? ''),
                'reference' => ft_null_string($_POST['reference'] ?? null),
                'description' => ft_null_string($_POST['description'] ?? null),
                'prix' => $_POST['prix'] ?? '',
            ]);
            break;

        case 'update_price':
            $feedback = $adminController->updatePrice(
                trim($_POST['categorie_original'] ?? ''),
                [
                    'categorie' => trim($_POST['categorie'] ?? ''),
                    'reference' => ft_null_string($_POST['reference'] ?? null),
                    'description' => ft_null_string($_POST['description'] ?? null),
                    'prix' => $_POST['prix'] ?? '',
                ]
            );
            break;

        case 'delete_price':
            $feedback = $adminController->deletePrice(trim($_POST['categorie'] ?? ''));
            break;

        default:
            $feedback = ['success' => false, 'message' => 'Action non reconnue.'];
    }
    
    // Redirection après POST pour éviter la double soumission (pattern POST-Redirect-GET)
    if (isset($feedback['success']) && $feedback['success']) {
        $redirectUrl = 'index.php?section=' . urlencode($section);
        if (isset($feedback['message'])) {
            $_SESSION['admin_feedback'] = $feedback;
        }
        header('Location: ' . $redirectUrl);
        exit;
    }
}

// Récupérer le feedback de la session si présent (après redirection)
if (isset($_SESSION['admin_feedback'])) {
    $feedback = $_SESSION['admin_feedback'];
    unset($_SESSION['admin_feedback']);
}

$users = $adminController->listUsers();
$terrains = $adminController->listTerrains();
$prices = $adminController->listPrices();
$currentSection = $section;
$currentPage = 'admin';
$pageTitle = 'Administration | Foot Fields';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?></title>
    <link rel="stylesheet" href="../../assets/css/Style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <meta name="theme-color" content="#103e36">
    <style>
        .ft-admin-layout {
            display: flex;
            min-height: 100vh;
            background: linear-gradient(135deg, #f8fafb 0%, #f0f4f8 100%);
        }
        .ft-admin-sidebar {
            width: 280px;
            background: linear-gradient(180deg, #0d3a32 0%, #0a2e28 50%, #062420 100%);
            color: white;
            padding: 0;
            position: fixed;
            height: 100vh;
            left: 0;
            top: 0;
            box-shadow: 4px 0 20px rgba(0,0,0,0.15);
            overflow-y: auto;
            z-index: 1000;
        }
        .ft-admin-sidebar-header {
            padding: 30px 20px;
            background: linear-gradient(135deg, rgba(43,217,151,0.1) 0%, rgba(43,217,151,0.05) 100%);
            border-bottom: 2px solid rgba(43,217,151,0.3);
            text-align: center;
        }
        .ft-admin-sidebar-header h2 {
            margin: 0;
            font-size: 20px;
            font-weight: 700;
            background: linear-gradient(135deg, #2bd997 0%, #1bc983 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .ft-admin-sidebar-header p {
            margin: 8px 0 0 0;
            font-size: 12px;
            color: rgba(43,217,151,0.8);
            font-weight: 500;
        }
        .ft-admin-nav {
            padding: 20px 0;
        }
        .ft-admin-nav-item {
            display: flex;
            align-items: center;
            padding: 14px 20px;
            color: rgba(255,255,255,0.75);
            text-decoration: none;
            cursor: pointer;
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            border-left: 4px solid transparent;
            font-size: 14px;
            font-weight: 500;
        }
        .ft-admin-nav-item:hover {
            background: linear-gradient(90deg, rgba(43,217,151,0.15) 0%, rgba(43,217,151,0.05) 100%);
            color: #2bd997;
            border-left-color: #2bd997;
            padding-left: 24px;
        }
        .ft-admin-nav-item.active {
            background: linear-gradient(90deg, rgba(43,217,151,0.2) 0%, rgba(43,217,151,0.08) 100%);
            color: #2bd997;
            border-left-color: #1bc983;
            font-weight: 600;
            box-shadow: inset -2px 0 8px rgba(43,217,151,0.1);
        }
        .ft-admin-nav-item i {
            margin-right: 14px;
            font-size: 17px;
            transition: transform 0.3s ease;
        }
        .ft-admin-nav-item:hover i {
            transform: translateX(2px);
        }
        .ft-admin-content-wrapper {
            margin-left: 280px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        .ft-admin-topbar {
            background: linear-gradient(90deg, #ffffff 0%, #f9fbfc 100%);
            padding: 16px 30px;
            box-shadow: 0 2px 12px rgba(13,58,50,0.08);
            border-bottom: 1px solid #e8eef4;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .ft-admin-topbar-title {
            font-size: 16px;
            font-weight: 600;
            background: linear-gradient(135deg, #0d3a32 0%, #1bc983 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .ft-admin-main {
            flex: 1;
            padding: 30px;
            overflow-y: auto;
        }
        @media (max-width: 768px) {
            .ft-admin-sidebar {
                width: 250px;
            }
            .ft-admin-content-wrapper {
                margin-left: 250px;
            }
            .ft-admin-main {
                padding: 15px;
            }
        }
        @media (max-width: 576px) {
            .ft-admin-sidebar {
                width: 200px;
            }
            .ft-admin-content-wrapper {
                margin-left: 200px;
            }
        }
    </style>
</head>
<body>
<div class="ft-admin-layout">
    <!-- Sidebar Navigation -->
    <aside class="ft-admin-sidebar">
        <div class="ft-admin-sidebar-header">
            <h2>FootFields</h2>
            <p>Administration</p>
        </div>
        <nav class="ft-admin-nav">
            <a href="?section=users" class="ft-admin-nav-item <?php echo $currentSection === 'users' ? 'active' : ''; ?>">
                <i class="bi bi-people-fill"></i>
                <span>Utilisateurs</span>
            </a>
            <a href="?section=terrains" class="ft-admin-nav-item <?php echo $currentSection === 'terrains' ? 'active' : ''; ?>">
                <i class="bi bi-geo-alt-fill"></i>
                <span>Terrains</span>
            </a>
            <a href="?section=prices" class="ft-admin-nav-item <?php echo $currentSection === 'prices' ? 'active' : ''; ?>">
                <i class="bi bi-tag-fill"></i>
                <span>Tarifs</span>
            </a>
            <hr style="margin: 15px 0; border: none; border-top: 1px solid rgba(255,255,255,0.1);">
            <a href="../../views/public/Home.php" class="ft-admin-nav-item">
                <i class="bi bi-house-door-fill"></i>
                <span>Retour au site</span>
            </a>
            <a href="../../views/public/logout.php" class="ft-admin-nav-item">
                <i class="bi bi-box-arrow-right"></i>
                <span>Déconnexion</span>
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="ft-admin-content-wrapper">
        <div class="ft-admin-topbar">
            <div class="ft-admin-topbar-title">
                <?php 
                    $sectionLabels = ['users' => 'Utilisateurs', 'terrains' => 'Terrains', 'prices' => 'Tarifs'];
                    echo 'Gestion : ' . ($sectionLabels[$currentSection] ?? 'Administration');
                ?>
            </div>
            <div style="font-size: 12px; color: #666;">
                <?php echo date('d/m/Y H:i'); ?>
            </div>
        </div>
        <main class="ft-admin-main">
            <?php require __DIR__ . '/Dashboard.php'; ?>
        </main>
    </div>
</div>

<?php require __DIR__ . '/../../includes/Footer.php'; ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ftFieldIcons = {
        nom: 'bi-person-vcard',
        prenom: 'bi-person-lines-fill',
        email: 'bi-envelope-open',
        telephone: 'bi-telephone',
        adresse: 'bi-geo-alt',
        password: 'bi-lock',
        role: 'bi-diagram-3',
        etat: 'bi-shield-check',
        taille: 'bi-aspect-ratio',
        type: 'bi-grid-3x3-gap',
        categorie: 'bi-tags',
        reference: 'bi-collection',
        description: 'bi-chat-square-text',
        prix: 'bi-cash-coin',
        photo: 'bi-card-image'
    };

    Object.keys(ftFieldIcons).forEach(function (name) {
        var iconClass = ftFieldIcons[name];
        var fields = document.querySelectorAll('[name="' + name + '"]');
        fields.forEach(function (field) {
            if (!field || field.closest('.ft-input')) {
                return;
            }

            var tag = (field.tagName || '').toLowerCase();
            if (tag === 'input') {
                var type = (field.getAttribute('type') || '').toLowerCase();
                if (type === 'hidden' || type === 'checkbox' || type === 'radio') {
                    return;
                }
                if (type === 'file' && name !== 'photo') {
                    return;
                }
            }

            var parent = field.parentElement;
            if (!parent) {
                return;
            }

            var wrapper = document.createElement('div');
            wrapper.className = 'ft-input';

            var icon = document.createElement('i');
            icon.className = 'ft-input-icon bi ' + iconClass;
            icon.setAttribute('aria-hidden', 'true');

            parent.insertBefore(wrapper, field);
            wrapper.appendChild(icon);
            wrapper.appendChild(field);
        });
    });
    
    // Empêcher la double soumission des formulaires
    var forms = document.querySelectorAll('form[method="post"]');
    forms.forEach(function(form) {
        form.addEventListener('submit', function(e) {
            var submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn && !submitBtn.disabled) {
                submitBtn.disabled = true;
                submitBtn.textContent = submitBtn.textContent + '...';
            }
        });
    });
});
</script>
</body>
</html>

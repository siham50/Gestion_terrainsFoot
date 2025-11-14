<?php
// views/public/MesTournois.php
$GLOBALS['contentDisplayed'] = true;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Charger les données depuis le contrôleur
require_once __DIR__ . '/../../controllers/TournoiController.php';
require_once __DIR__ . '/../../classes/Database.php';

$controller = new TournoiController();

// Récupérer les tournois de l'utilisateur
$mesTournois = $controller->mesTournois($_SESSION['user_id']);

// Séparer uniquement MES tournois par statut
$tournoisActifs = [];
$tournoisTermines = [];

foreach ($mesTournois as $tournoi) {
    $stats = $controller->getTournoiStats($tournoi['idTournoi']);
    $tournoi['stats'] = $stats;
    
    if (!empty($tournoi['champion'])) {
        $tournoisTermines[] = $tournoi;
    } else {
        $tournoisActifs[] = $tournoi;
    }
}

// Afficher les messages de feedback
$tournoiFeedback = $_SESSION['tournoi_feedback'] ?? null;
if ($tournoiFeedback !== null) {
    unset($_SESSION['tournoi_feedback']);
}
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FootTime - Mes Tournois</title>
    <link rel="stylesheet" href="../../assets/css/Style.css">
</head>
<body>
    <?php require '../../includes/Navbar.php'; ?>
    <div class="ft-shell">
        <main class="ft-content" aria-label="Contenu principal">
            <section class="ft-page">
                <header class="ft-page-header">
                    <h1 class="ft-h1">Mes Tournois</h1>
                    <p class="ft-sub">Gérez vos tournois et suivez les résultats</p>
    </header>

                <?php if ($tournoiFeedback): ?>
                    <div class="ft-alert <?php echo $tournoiFeedback['success'] ? 'ft-alert-success' : 'ft-alert-error'; ?>" style="padding: 16px; margin-bottom: 20px; border-radius: 12px; border: 1px solid <?php echo $tournoiFeedback['success'] ? '#1a6a58' : '#623b3b'; ?>; background: <?php echo $tournoiFeedback['success'] ? 'rgba(43,217,151,.12)' : 'rgba(255,0,0,.1)'; ?>;">
                        <div style="font-weight: 600; margin-bottom: 8px;"><?php echo htmlspecialchars($tournoiFeedback['message'], ENT_QUOTES, 'UTF-8'); ?></div>
                        <?php if (!$tournoiFeedback['success'] && !empty($tournoiFeedback['errors']) && is_array($tournoiFeedback['errors'])): ?>
                            <ul style="margin: 8px 0 0 20px; padding: 0;">
                                <?php foreach ($tournoiFeedback['errors'] as $error): ?>
                                    <li><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <!-- Bouton créer un tournoi -->
                <div style="margin-bottom: 24px;">
                    <a href="CreerTournoi.php" class="ft-btn ft-btn-primary" style="display: inline-flex; align-items: center; gap: 8px;">
                        <svg class="ft-ic" viewBox="0 0 24 24" style="width: 20px; height: 20px;">
                            <path d="M12 5v14M5 12h14"/>
                        </svg>
                        Créer un tournoi
                    </a>
                </div>

                

                <!-- Tournois actifs (mes tournois) -->
                <?php if (!empty($tournoisActifs)): ?>
                    <div class="ft-section-head" style="margin-top: 48px;">
                        <h2 class="ft-h2">Tournois Actifs</h2>
                        <span class="ft-muted"><?php echo count($tournoisActifs); ?> tournoi(s)</span>
                    </div>

                    <div class="ft-list">
                        <?php foreach ($tournoisActifs as $tournoi): ?>
                            <?php
                            $stats = $tournoi['stats'] ?? [];
                            $nombreEquipes = count($tournoi['equipes'] ?? []);
                            $matchsTermines = $stats['matchs_termines'] ?? 0;
                            $totalMatchs = $stats['total_matchs'] ?? 0;
                            $organisateur = ($tournoi['organisateur_nom'] ?? '') . ' ' . ($tournoi['organisateur_prenom'] ?? '');
                            ?>
                            <article class="ft-card ft-booking">
                                <div class="ft-booking-header">
                                    <div>
                                        <h3 class="ft-booking-title"><?php echo htmlspecialchars($tournoi['format'] ?? 'Tournoi'); ?></h3>
                                        <p class="ft-muted">Organisé par <?php echo htmlspecialchars(trim($organisateur) ?: 'Inconnu'); ?></p>
                                    </div>
                                    <span class="ft-chip ft-chip-ok">En cours</span>
                                </div>
                                
                                <div class="ft-booking-body">
                                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 12px;">
                                        <div>
                                            <div class="ft-muted" style="font-size: 12px;">Matchs</div>
                                            <div style="font-weight: 600;"><?php echo $matchsTermines; ?> / <?php echo $totalMatchs; ?></div>
                                        </div>
                                        <div>
                                            <div class="ft-muted" style="font-size: 12px;">Équipes</div>
                                            <div style="font-weight: 600;"><?php echo $nombreEquipes; ?></div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="ft-booking-footer">
                                    <a href="Tournoi.php?id=<?php echo $tournoi['idTournoi']; ?>" class="ft-btn ft-btn-secondary">
                                        Voir le bracket
                                    </a>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- Tournois terminés -->
                <?php if (!empty($tournoisTermines)): ?>
                    <div class="ft-section-head" style="margin-top: 48px;">
                        <h2 class="ft-h2">Tournois Terminés</h2>
                        <span class="ft-muted"><?php echo count($tournoisTermines); ?> tournoi(s)</span>
                    </div>

                    <div class="ft-list">
                        <?php foreach ($tournoisTermines as $tournoi): ?>
                            <?php
                            $stats = $tournoi['stats'] ?? [];
                            $nombreEquipes = count($tournoi['equipes'] ?? []);
                            $organisateur = ($tournoi['organisateur_nom'] ?? '') . ' ' . ($tournoi['organisateur_prenom'] ?? '');
                            ?>
                            <article class="ft-card ft-booking">
                                <div class="ft-booking-header">
                                    <div>
                                        <h3 class="ft-booking-title"><?php echo htmlspecialchars($tournoi['format'] ?? 'Tournoi'); ?></h3>
                                        <p class="ft-muted">Organisé par <?php echo htmlspecialchars(trim($organisateur) ?: 'Inconnu'); ?></p>
                                    </div>
                                    <span class="ft-chip ft-chip-warning">Terminé</span>
                                </div>
                                
                                <div class="ft-booking-body">
                                    <div style="margin-bottom: 12px;">
                                        <strong>🏆 Champion: </strong>
                                        <span style="color: var(--ft-accent); font-weight: 600;"><?php echo htmlspecialchars($tournoi['champion'] ?? 'N/A'); ?></span>
                                    </div>
                                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 12px;">
                                        <div>
                                            <div class="ft-muted" style="font-size: 12px;">Équipes</div>
                                            <div style="font-weight: 600;"><?php echo $nombreEquipes; ?></div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="ft-booking-footer">
                                    <a href="Tournoi.php?id=<?php echo $tournoi['idTournoi']; ?>" class="ft-btn ft-btn-secondary">
                                        Voir le bracket
                                    </a>
            </div>
                            </article>
        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
    </section>
        </main>
    </div>
    <?php require '../../includes/Footer.php'; ?>
</body>
</html>

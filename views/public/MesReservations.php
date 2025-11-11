<?php
// views/public/MesReservations.php
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
require_once __DIR__ . '/../../controllers/ReservationController.php';
require_once __DIR__ . '/../../config/database.php';

$controller = new ReservationController();
$data = $controller->getMesReservationsData($_SESSION['user_id']);

$reservations = $data['reservations'] ?? [];
$stats = $data['stats'] ?? [];

// Formater les statistiques
$reservationsAvenir = $stats['reservations_avenir'] ?? 0;
$totalHeures = $stats['total_heures'] ?? 0;
$heuresCeMois = $stats['heures_ce_mois'] ?? 0;
$totalReservations = $stats['total_reservations'] ?? 0;
$reservationsCeMois = $stats['reservations_ce_mois'] ?? 0;
$prochaineDate = $stats['prochaine_date'] ?? null;

// Formater la prochaine date
$prochaineDateFormatee = $prochaineDate ? date('d M', strtotime($prochaineDate)) : 'Aucune';

// Formater les heures
function formatHeures($heures) {
    if ($heures == 0) return '0h';
    $h = floor($heures);
    $m = round(($heures - $h) * 60);
    if ($m == 0) {
        return $h . 'h';
    }
    return $h . 'h' . str_pad($m, 2, '0', STR_PAD_LEFT);
}
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FootTime - Mes réservations</title>
    <link rel="stylesheet" href="../../assets/css/Style.css">
</head>
<body>
    <?php require '../../includes/Navbar.php'; ?>
    <div class="ft-shell">
        <main class="ft-content" aria-label="Contenu principal">
            <section class="ft-page">
                <header class="ft-page-header">
                    <h1 class="ft-h1">Mes réservations</h1>
                    <p class="ft-sub">Gérez vos réservations de terrains</p>
                </header>

                <div class="ft-stats">
                    <div class="ft-card ft-stat">
                        <div class="ft-stat-icon">
                            <svg class="ft-ic" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M8 2v4M16 2v4M3 10h18"/></svg>
                        </div>
                        <div class="ft-stat-body">
                            <div class="ft-stat-value"><?php echo htmlspecialchars($reservationsAvenir); ?></div>
                            <div class="ft-stat-label">Réservations à venir</div>
                            <div class="ft-stat-sub">Prochaine: <?php echo htmlspecialchars($prochaineDateFormatee); ?></div>
                        </div>
                    </div>
                    <div class="ft-card ft-stat">
                        <div class="ft-stat-icon">
                             <svg class="ft-ic" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                        </div>
                        <div class="ft-stat-body">
                            <div class="ft-stat-value"><?php echo formatHeures($heuresCeMois); ?></div>
                            <div class="ft-stat-label">Heures réservées</div>
                            <div class="ft-stat-sub">Ce mois</div>
                        </div>
                    </div>
                    <div class="ft-card ft-stat">
                        <div class="ft-stat-icon">
                            <svg class="ft-ic" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M9 12l2 2 4-4"/></svg>
                        </div>
                        <div class="ft-stat-body">
                            <div class="ft-stat-value"><?php echo htmlspecialchars($totalReservations); ?></div>
                            <div class="ft-stat-label">Réservations totales</div>
                            <div class="ft-stat-sub"><?php echo $reservationsCeMois > 0 ? '+' . $reservationsCeMois . ' ce mois' : 'Aucune ce mois'; ?></div>
                        </div>
                    </div>
                </div>

                <div class="ft-section-head">
                    <h2 class="ft-h2">Réservations à venir</h2>
                    <span class="ft-muted"><?php echo count($reservations); ?> réservation(s)</span>
                </div>

                <div class="ft-list">
                    <?php if (empty($reservations)): ?>
                        <div class="ft-card">
                            <p class="ft-muted" style="text-align: center; padding: 2rem;">
                                Aucune réservation à venir. 
                                <a href="Home.php" style="color: #2bd997; text-decoration: underline;">Réserver un terrain</a>
                            </p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($reservations as $reservation): ?>
                            <?php
                            // Formater la date
                            $dateReservation = date('d M Y', strtotime($reservation['dateReservation']));
                            
                            // Formater l'heure
                            $heureDebut = date('H:i', strtotime($reservation['heure_debut']));
                            $heureFin = date('H:i', strtotime($reservation['heure_fin']));
                            
                            // Déterminer le statut
                            $isPast = (strtotime($reservation['dateReservation']) < strtotime(date('Y-m-d'))) ||
                                      (strtotime($reservation['dateReservation']) == strtotime(date('Y-m-d')) && 
                                       strtotime($reservation['heure_debut']) < strtotime(date('H:i:s')));
                            $status = $isPast ? 'Terminée' : 'Confirmée';
                            $statusClass = $isPast ? 'ft-chip-warning' : 'ft-chip-ok';
                            
                            // Prix
                           $prix = $reservation['montantTotal'] ?? $reservation['montantTerrain'] ?? $reservation['terrain_prix'] ?? 'N/A';
                            $prixDisplay = is_numeric($prix) ? number_format($prix, 2, ',', ' ') . ' MAD' : $prix;
                            
                            // Options supplémentaires
                            $options = [];
                            if ($reservation['ballon']) $options[] = 'Ballon';
                            if ($reservation['arbitre']) $options[] = 'Arbitre';
                            if ($reservation['maillot']) $options[] = 'Maillots';
                            if ($reservation['douche']) $options[] = 'Douche';
                            ?>
                    <article class="ft-card ft-booking">
                        <div class="ft-booking-main">
                                    <h3 class="ft-title"><?php echo htmlspecialchars($reservation['terrain_nom']); ?></h3>
                            <div class="ft-badges">
                                        <span class="ft-badge"><?php echo htmlspecialchars($reservation['taille']); ?></span>
                                        <?php if (!empty($reservation['type'])): ?>
                                            <span class="ft-badge"><?php echo htmlspecialchars($reservation['type']); ?></span>
                                        <?php endif; ?>
                            </div>
                            <ul class="ft-meta">
                                        <li>
                                            <svg class="ft-ic" viewBox="0 0 24 24">
                                                <rect x="3" y="4" width="18" height="18" rx="2"/>
                                                <path d="M8 2v4M16 2v4M3 10h18"/>
                                            </svg>
                                            <?php echo htmlspecialchars($dateReservation); ?>
                                        </li>
                                        <li>
                                            <svg class="ft-ic" viewBox="0 0 24 24">
                                                <circle cx="12" cy="12" r="9"/>
                                                <path d="M12 7v5l3 2"/>
                                            </svg>
                                            <?php echo htmlspecialchars($heureDebut); ?> - <?php echo htmlspecialchars($heureFin); ?>
                                        </li>
                                        <?php if (!empty($options)): ?>
                                            <li>
                                                <svg class="ft-ic" viewBox="0 0 24 24">
                                                    <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                                                    <path d="M2 17l10 5 10-5M2 12l10 5 10-5"/>
                                                </svg>
                                                <?php echo htmlspecialchars(implode(', ', $options)); ?>
                                            </li>
                                        <?php endif; ?>
                            </ul>
                                    <?php if (!empty($reservation['demande'])): ?>
                                        <p class="ft-muted" style="margin-top: 0.5rem; font-style: italic;">
                                            "<?php echo htmlspecialchars($reservation['demande']); ?>"
                                        </p>
                                    <?php endif; ?>
                        </div>
                        <div class="ft-booking-aside">
                                    <div class="ft-price"><?php echo htmlspecialchars($prixDisplay); ?></div>
                                    <?php if (!$isPast): ?>
                            <div class="ft-actions-row">
                                            <button class="ft-btn" onclick="modifierReservation(<?php echo $reservation['idReservation']; ?>)">
                                                <svg class="ft-ic" viewBox="0 0 24 24">
                                                    <path d="M3 12h18"/>
                                                    <path d="M12 3v18"/>
                                                </svg>
                                    Modifier
                                </button>
                                            <button class="ft-btn ft-btn-danger" onclick="annulerReservation(<?php echo $reservation['idReservation']; ?>)">
                                                <svg class="ft-ic" viewBox="0 0 24 24">
                                                    <path d="M19 7l-.9 12.1A2 2 0 0 1 16.1 21H7.9a2 2 0 0 1-2-1.9L5 7"/>
                                                    <path d="M10 11v6M14 11v6"/>
                                                    <path d="M4 7h16"/>
                                                    <path d="M9 7V5a3 3 0 0 1 6 0v2"/>
                                                </svg>
                                    Annuler
                                </button>
                            </div>
                                    <?php endif; ?>
                                    <span class="ft-chip <?php echo $statusClass; ?>">
                                        <svg class="ft-ic" viewBox="0 0 24 24">
                                            <path d="M20 6L9 17l-5-5"/>
                                        </svg>
                                        <?php echo htmlspecialchars($status); ?>
                            </span>
                        </div>
                    </article>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </section>
        </main>
    </div>
<?php require '../../includes/Footer.php'; ?>

<script>
// Configuration AJAX
const RESERVATION_UPDATE_INTERVAL = 3000; // Mise à jour toutes les 3 secondes
let currentReservationIds = []; // Stocker les IDs des réservations actuelles

// Initialiser les IDs des réservations actuelles au chargement
document.addEventListener('DOMContentLoaded', function() {
    initializeReservationIds();
    // Démarrer la mise à jour automatique
    setInterval(updateReservations, RESERVATION_UPDATE_INTERVAL);
});

// Initialiser la liste des IDs de réservations
function initializeReservationIds() {
    const reservationCards = document.querySelectorAll('.ft-booking');
    currentReservationIds = Array.from(reservationCards).map(card => {
        const cancelBtn = card.querySelector('button[onclick*="annulerReservation"]');
        if (cancelBtn) {
            const match = cancelBtn.getAttribute('onclick').match(/annulerReservation\((\d+)\)/);
            return match ? parseInt(match[1]) : null;
        }
        return null;
    }).filter(id => id !== null);
}

// Mettre à jour les réservations via AJAX
function updateReservations() {
    const xhr = new XMLHttpRequest();
    xhr.open('GET', '../../controllers/ReservationController.php?action=get_mes_reservations_data', true);
    
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            try {
                const response = JSON.parse(xhr.responseText);
                if (response.success && response.data) {
                    const newReservations = response.data.reservations || [];
                    const newStats = response.data.stats || {};
                    
                    // Vérifier s'il y a de nouvelles réservations
                    const newReservationIds = newReservations.map(r => r.idReservation);
                    const hasNewReservations = newReservationIds.some(id => !currentReservationIds.includes(id));
                    
                    if (hasNewReservations || newReservations.length !== currentReservationIds.length) {
                        // Mettre à jour la page
                        updateReservationsDisplay(newReservations, newStats);
                        currentReservationIds = newReservationIds;
                    } else {
                        // Mettre à jour seulement les statistiques (elles peuvent changer)
                        updateStatsDisplay(newStats);
                    }
                }
            } catch (e) {
                console.error('Erreur lors de la mise à jour des réservations:', e);
            }
        }
    };
    
    xhr.onerror = function() {
        console.error('Erreur réseau lors de la mise à jour des réservations');
    };
    
    xhr.send();
}

// Mettre à jour l'affichage des réservations
function updateReservationsDisplay(reservations, stats) {
    // Mettre à jour les statistiques
    updateStatsDisplay(stats);
    
    // Mettre à jour la liste des réservations
    const reservationsList = document.querySelector('.ft-list');
    if (!reservationsList) return;
    
    if (reservations.length === 0) {
        reservationsList.innerHTML = `
            <div class="ft-card">
                <p class="ft-muted" style="text-align: center; padding: 2rem;">
                    Aucune réservation à venir. 
                    <a href="Home.php" style="color: #2bd997; text-decoration: underline;">Réserver un terrain</a>
                </p>
            </div>
        `;
        return;
    }
    
    let html = '';
    reservations.forEach(reservation => {
        // Formater la date
        const dateReservation = formatDate(reservation.dateReservation);
        
        // Formater l'heure
        const heureDebut = formatTime(reservation.heure_debut);
        const heureFin = formatTime(reservation.heure_fin);
        
        // Déterminer le statut
        const isPast = isReservationPast(reservation.dateReservation, reservation.heure_debut);
        const status = isPast ? 'Terminée' : 'Confirmée';
        const statusClass = isPast ? 'ft-chip-warning' : 'ft-chip-ok';
        
        // Prix
        const prix = reservation.montantTotal || reservation.montantTerrain || reservation.terrain_prix || 'N/A';
        const prixDisplay = isNumeric(prix) ? formatPrice(prix) + '€' : prix;
        
        // Options supplémentaires
        const options = [];
        if (reservation.ballon) options.push('Ballon');
        if (reservation.arbitre) options.push('Arbitre');
        if (reservation.maillot) options.push('Maillots');
        if (reservation.douche) options.push('Douche');
        
        html += `
                    <article class="ft-card ft-booking">
                        <div class="ft-booking-main">
                    <h3 class="ft-title">${escapeHtml(reservation.terrain_nom)}</h3>
                            <div class="ft-badges">
                        <span class="ft-badge">${escapeHtml(reservation.taille)}</span>
                        ${reservation.type ? `<span class="ft-badge">${escapeHtml(reservation.type)}</span>` : ''}
                            </div>
                            <ul class="ft-meta">
                        <li>
                            <svg class="ft-ic" viewBox="0 0 24 24">
                                <rect x="3" y="4" width="18" height="18" rx="2"/>
                                <path d="M8 2v4M16 2v4M3 10h18"/>
                            </svg>
                            ${dateReservation}
                        </li>
                        <li>
                            <svg class="ft-ic" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="9"/>
                                <path d="M12 7v5l3 2"/>
                            </svg>
                            ${heureDebut} - ${heureFin}
                        </li>
                        ${options.length > 0 ? `
                            <li>
                                <svg class="ft-ic" viewBox="0 0 24 24">
                                    <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                                    <path d="M2 17l10 5 10-5M2 12l10 5 10-5"/>
                                </svg>
                                ${escapeHtml(options.join(', '))}
                            </li>
                        ` : ''}
                            </ul>
                    ${reservation.demande ? `
                        <p class="ft-muted" style="margin-top: 0.5rem; font-style: italic;">
                            "${escapeHtml(reservation.demande)}"
                        </p>
                    ` : ''}
                        </div>
                        <div class="ft-booking-aside">
                    <div class="ft-price">${prixDisplay}</div>
                    ${!isPast ? `
                            <div class="ft-actions-row">
                            <button class="ft-btn" onclick="modifierReservation(${reservation.idReservation})">
                                <svg class="ft-ic" viewBox="0 0 24 24">
                                    <path d="M3 12h18"/>
                                    <path d="M12 3v18"/>
                                </svg>
                                    Modifier
                                </button>
                            <button class="ft-btn ft-btn-danger" onclick="annulerReservation(${reservation.idReservation})">
                                <svg class="ft-ic" viewBox="0 0 24 24">
                                    <path d="M19 7l-.9 12.1A2 2 0 0 1 16.1 21H7.9a2 2 0 0 1-2-1.9L5 7"/>
                                    <path d="M10 11v6M14 11v6"/>
                                    <path d="M4 7h16"/>
                                    <path d="M9 7V5a3 3 0 0 1 6 0v2"/>
                                </svg>
                                    Annuler
                                </button>
                            </div>
                    ` : ''}
                    <span class="ft-chip ${statusClass}">
                        <svg class="ft-ic" viewBox="0 0 24 24">
                            <path d="M20 6L9 17l-5-5"/>
                        </svg>
                        ${status}
                            </span>
                        </div>
                    </article>
        `;
    });
    
    reservationsList.innerHTML = html;
    
    // Mettre à jour le compteur
    const sectionHead = document.querySelector('.ft-section-head .ft-muted');
    if (sectionHead) {
        sectionHead.textContent = `${reservations.length} réservation(s)`;
    }
}

// Mettre à jour l'affichage des statistiques
function updateStatsDisplay(stats) {
    const reservationsAvenir = stats.reservations_avenir || 0;
    const heuresCeMois = stats.heures_ce_mois || 0;
    const totalReservations = stats.total_reservations || 0;
    const reservationsCeMois = stats.reservations_ce_mois || 0;
    const prochaineDate = stats.prochaine_date || null;
    
    // Formater la prochaine date
    const prochaineDateFormatee = prochaineDate ? formatDateShort(prochaineDate) : 'Aucune';
    
    // Mettre à jour les statistiques
    const statValue1 = document.querySelector('.ft-stat:nth-child(1) .ft-stat-value');
    const statSub1 = document.querySelector('.ft-stat:nth-child(1) .ft-stat-sub');
    if (statValue1) statValue1.textContent = reservationsAvenir;
    if (statSub1) statSub1.textContent = `Prochaine: ${prochaineDateFormatee}`;
    
    const statValue2 = document.querySelector('.ft-stat:nth-child(2) .ft-stat-value');
    if (statValue2) statValue2.textContent = formatHeures(heuresCeMois);
    
    const statValue3 = document.querySelector('.ft-stat:nth-child(3) .ft-stat-value');
    const statSub3 = document.querySelector('.ft-stat:nth-child(3) .ft-stat-sub');
    if (statValue3) statValue3.textContent = totalReservations;
    if (statSub3) {
        statSub3.textContent = reservationsCeMois > 0 ? `+${reservationsCeMois} ce mois` : 'Aucune ce mois';
    }
}

// Fonctions utilitaires
function formatDate(dateString) {
    const date = new Date(dateString);
    const months = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'];
    return `${date.getDate()} ${months[date.getMonth()]} ${date.getFullYear()}`;
}

function formatDateShort(dateString) {
    const date = new Date(dateString);
    const months = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'];
    return `${date.getDate()} ${months[date.getMonth()]}`;
}

function formatTime(timeString) {
    if (!timeString) return '';
    const time = timeString.split(':');
    return `${time[0]}:${time[1]}`;
}

function formatPrice(price) {
    return parseFloat(price).toFixed(2).replace('.', ',');
}

function formatHeures(heures) {
    if (heures == 0) return '0h';
    const h = Math.floor(heures);
    const m = Math.round((heures - h) * 60);
    if (m == 0) {
        return h + 'h';
    }
    return h + 'h' + String(m).padStart(2, '0');
}

function isReservationPast(dateReservation, heureDebut) {
    const now = new Date();
    const reservationDate = new Date(dateReservation);
    const [hours, minutes] = heureDebut.split(':');
    reservationDate.setHours(parseInt(hours), parseInt(minutes), 0);
    return reservationDate < now;
}

function isNumeric(value) {
    return !isNaN(value) && !isNaN(parseFloat(value));
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Fonction pour annuler une réservation
function annulerReservation(idReservation) {
    if (!confirm('Êtes-vous sûr de vouloir annuler cette réservation ?')) {
        return;
    }
    
    const formData = new FormData();
    formData.append('idReservation', idReservation);
    
    fetch('../../controllers/ReservationController.php?action=cancel', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Réservation annulée avec succès');
            // Mettre à jour immédiatement
            updateReservations();
        } else {
            alert('Erreur: ' + (data.message || 'Impossible d\'annuler la réservation'));
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors de l\'annulation de la réservation');
    });
}

// Fonction pour modifier une réservation
function modifierReservation(idReservation) {
    // TODO: Implémenter la modification de réservation
    alert('La modification de réservation sera disponible prochainement');
    // Pour l'instant, rediriger vers le formulaire de réservation
    // window.location.href = 'ReservationForm.php?id=' + idReservation;
}
</script>
</body>
</html>

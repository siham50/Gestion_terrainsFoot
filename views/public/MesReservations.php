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
require_once __DIR__ . '/../../classes/Database.php';

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

                            // Verrou 48h
                            $reservationDateTime = strtotime($reservation['dateReservation'] . ' ' . $reservation['heure_debut']);
                            $hoursUntil = ($reservationDateTime - time()) / 3600;
                            $locked48h = !$isPast && $hoursUntil < 48;
                            
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
                                            <button class="ft-btn" <?php echo $locked48h ? 'disabled title="Modification indisponible à moins de 48h"' : ''; ?> onclick="modifierReservation(<?php echo $reservation['idReservation']; ?>)">
                                                <svg class="ft-ic" viewBox="0 0 24 24">
                                                    <path d="M3 12h18"/>
                                                    <path d="M12 3v18"/>
                                                </svg>
                                    Modifier
                                </button>
                                            <button class="ft-btn ft-btn-danger" <?php echo $locked48h ? 'disabled title="Annulation indisponible à moins de 48h"' : ''; ?> onclick="annulerReservation(<?php echo $reservation['idReservation']; ?>)">
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

<!-- Modal de modification de réservation -->
<div class="ft-modal" id="reservationEditModal">
    <div class="ft-modal-content">
        <div class="ft-modal-header">
            <h2 id="reservationEditModalTitle">Modifier la réservation</h2>
            <button class="ft-modal-close" id="reservationEditModalClose">&times;</button>
        </div>
        <div class="ft-modal-body">
            <form id="reservationEditForm">
                <input type="hidden" id="editIdReservation" name="idReservation">
                <input type="hidden" id="editIdTerrain" name="idTerrain">

                <div class="ft-form-grid">
                    <div class="ft-form-group">
                        <label>Terrain sélectionné</label>
                        <input type="text" id="editTerrainName" class="ft-input" disabled>
                    </div>

                    <div class="ft-form-group">
                        <label for="editDateReservation">Date de réservation</label>
                        <input type="date" id="editDateReservation" name="dateReservation" class="ft-input" required>
                    </div>

                    <div class="ft-form-group">
                        <label for="editIdCreneau">Créneau horaire</label>
                        <select id="editIdCreneau" name="idCreneau" class="ft-input" required>
                            <option value="">-- Sélectionner --</option>
                            <option value="1">08:00 - 09:00</option>
                            <option value="2">09:00 - 10:00</option>
                            <option value="3">10:00 - 11:00</option>
                            <option value="4">11:00 - 12:00</option>
                            <option value="5">12:00 - 13:00</option>
                            <option value="6">13:00 - 14:00</option>
                            <option value="7">14:00 - 15:00</option>
                            <option value="8">15:00 - 16:00</option>
                            <option value="9">16:00 - 17:00</option>
                            <option value="10">17:00 - 18:00</option>
                            <option value="11">18:00 - 19:00</option>
                            <option value="12">19:00 - 20:00</option>
                            <option value="13">20:00 - 21:00</option>
                            <option value="14">21:00 - 22:00</option>
                            <option value="15">22:00 - 23:00</option>
                        </select>
                    </div>

                    <div class="ft-form-group">
                        <label>Taille du terrain</label>
                        <input type="text" id="editTailleDisplay" class="ft-input" disabled>
                    </div>

                    <div class="ft-form-group">
                        <label>Type de terrain</label>
                        <input type="text" id="editTypeDisplay" class="ft-input" disabled>
                    </div>

                    <div class="ft-form-group">
                        <label>Options supplémentaires</label>
                        <div class="ft-checkbox-list">
                            <label><input type="checkbox" id="editBallon" name="ballon" value="1"> Ballon</label>
                            <label><input type="checkbox" id="editArbitre" name="arbitre" value="1"> Arbitre</label>
                            <label><input type="checkbox" id="editMaillot" name="maillot" value="1"> Maillots</label>
                            <label><input type="checkbox" id="editDouche" name="douche" value="1"> Douche</label>
                        </div>
                    </div>

                    <div class="ft-form-group" style="grid-column: 1 / -1;">
                        <label for="editDemande">Demande spécifique</label>
                        <textarea id="editDemande" name="demande" class="ft-input" rows="4" placeholder="Commentaires ou requêtes spécifiques..."></textarea>
                    </div>
                </div>

                <div class="ft-modal-actions">
                    <button type="button" class="ft-btn ft-btn-secondary" id="reservationEditCancelBtn">Annuler</button>
                    <button type="submit" class="ft-btn ft-btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
    </div>

<script>
// Configuration AJAX
const RESERVATION_UPDATE_INTERVAL = 3000; // Mise à jour toutes les 3 secondes
let currentReservationIds = []; // Stocker les IDs des réservations actuelles
let forceReservationsRefresh = false; // Forcer un rafraîchissement complet après modification
let currentReservationsDigest = ''; // Détecter les changements de contenu même si les IDs sont identiques

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
    const cacheBuster = '&t=' + Date.now();
    xhr.open('GET', '../../controllers/ReservationController.php?action=get_mes_reservations_data' + cacheBuster, true);
    
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
                    const newDigest = computeReservationsDigest(newReservations);
                    const hasContentChanged = newDigest !== currentReservationsDigest;
                    
                    if (forceReservationsRefresh || hasNewReservations || newReservations.length !== currentReservationIds.length || hasContentChanged) {
                        // Mettre à jour la page
                        updateReservationsDisplay(newReservations, newStats);
                        currentReservationIds = newReservationIds;
                        forceReservationsRefresh = false; // reset
                        currentReservationsDigest = newDigest;
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

// Crée un digest léger des réservations pour détecter les changements de contenu
function computeReservationsDigest(reservations) {
    try {
        // Concaténer les champs qui peuvent changer et impactent l'affichage
        const parts = reservations.map(r => [
            r.idReservation,
            r.dateReservation,
            r.idCreneau,
            r.heure_debut || '',
            r.heure_fin || '',
            r.demande || '',
            r.ballon ? 1 : 0,
            r.arbitre ? 1 : 0,
            r.maillot ? 1 : 0,
            r.douche ? 1 : 0,
            r.montantTotal || r.montantTerrain || r.terrain_prix || ''
        ].join('|'));
        // Simple hash via joining; sufficient to detect diffs
        return parts.join('~');
    } catch (e) {
        return '' + Math.random();
    }
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
        const prixDisplay = isNumeric(prix) ? formatPrice(prix) + ' MAD' : prix;
        
        // Options supplémentaires
        const options = [];
        if (reservation.ballon) options.push('Ballon');
        if (reservation.arbitre) options.push('Arbitre');
        if (reservation.maillot) options.push('Maillots');
        if (reservation.douche) options.push('Douche');
        
        const locked48h = (!isPast) && isLocked48h(reservation.dateReservation, reservation.heure_debut);
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
                            <button class="ft-btn" ${locked48h ? 'disabled title="Modification indisponible à moins de 48h"' : ''} onclick="modifierReservation(${reservation.idReservation})">
                                <svg class="ft-ic" viewBox="0 0 24 24">
                                    <path d="M3 12h18"/>
                                    <path d="M12 3v18"/>
                                </svg>
                                    Modifier
                                </button>
                            <button class="ft-btn ft-btn-danger" ${locked48h ? 'disabled title="Annulation indisponible à moins de 48h"' : ''} onclick="annulerReservation(${reservation.idReservation})">
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

function isLocked48h(dateReservation, heureDebut) {
    const now = new Date();
    const reservationDate = new Date(dateReservation);
    const [hours, minutes] = (heureDebut || '00:00').split(':');
    reservationDate.setHours(parseInt(hours), parseInt(minutes), 0);
    const diffMs = reservationDate - now;
    const diffHours = diffMs / (1000 * 60 * 60);
    return diffHours < 48;
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
    const btn = document.querySelector(`button[onclick="annulerReservation(${idReservation})"]`);
    if (btn && btn.disabled) {
        alert('Annulation indisponible à moins de 48h du match');
        return;
    }
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
    const btn = document.querySelector(`button[onclick="modifierReservation(${idReservation})"]`);
    if (btn && btn.disabled) {
        alert('Modification indisponible à moins de 48h du match');
        return;
    }
    // Récupérer les détails via AJAX (XMLHttpRequest) et ouvrir la modal pré-remplie
    const xhr = new XMLHttpRequest();
    xhr.open('GET', '../../controllers/ReservationController.php?action=get_reservation&idReservation=' + encodeURIComponent(idReservation), true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                try {
                    const data = JSON.parse(xhr.responseText);
                    if (!data.success) {
                        alert(data.message || 'Impossible de charger la réservation');
                        return;
                    }
                    const r = data.data;

                    // Pré-remplir
                    document.getElementById('editIdReservation').value = r.idReservation;
                    document.getElementById('editIdTerrain').value = r.idTerrain;
                    document.getElementById('editTerrainName').value = r.terrain_nom || ('Terrain #' + r.idTerrain);
                    document.getElementById('editTailleDisplay').value = r.taille || '';
                    document.getElementById('editTypeDisplay').value = r.type || '';

                    // Date min = aujourd'hui
                    const today = new Date();
                    const yyyy = today.getFullYear();
                    const mm = String(today.getMonth() + 1).padStart(2, '0');
                    const dd = String(today.getDate()).padStart(2, '0');
                    const dateInput = document.getElementById('editDateReservation');
                    dateInput.min = `${yyyy}-${mm}-${dd}`;

                    document.getElementById('editDateReservation').value = r.dateReservation;
                    document.getElementById('editIdCreneau').value = r.idCreneau;
                    document.getElementById('editDemande').value = r.demande || '';
                    document.getElementById('editBallon').checked = r.ballon == 1;
                    document.getElementById('editArbitre').checked = r.arbitre == 1;
                    document.getElementById('editMaillot').checked = r.maillot == 1;
                    document.getElementById('editDouche').checked = r.douche == 1;

                    document.getElementById('reservationEditModal').classList.add('ft-modal-open');
                } catch (e) {
                    console.error(e);
                    alert('Erreur lors du chargement de la réservation');
                }
            } else {
                alert('Erreur de connexion (' + xhr.status + ')');
            }
        }
    };
    xhr.onerror = function() {
        alert('Erreur réseau lors du chargement');
    };
    xhr.send();
}

// Gestion fermeture du modal de modification
document.getElementById('reservationEditModalClose').addEventListener('click', function() {
    document.getElementById('reservationEditModal').classList.remove('ft-modal-open');
});
document.getElementById('reservationEditCancelBtn').addEventListener('click', function() {
    document.getElementById('reservationEditModal').classList.remove('ft-modal-open');
});
window.addEventListener('click', function(event) {
    const modal = document.getElementById('reservationEditModal');
    if (event.target === modal) modal.classList.remove('ft-modal-open');
});

// Soumission du formulaire de modification via AJAX
document.getElementById('reservationEditForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const form = this;
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    submitBtn.disabled = true;
    submitBtn.textContent = 'Enregistrement...';

    const formData = new FormData(form);

    const xhr = new XMLHttpRequest();
    xhr.open('POST', '../../controllers/ReservationController.php?action=update', true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
            if (xhr.status === 200) {
                try {
                    const data = JSON.parse(xhr.responseText);
                    if (data.success) {
                        // Retour visuel identique au flux de réservation
                        alert('Réservation mise à jour avec succès');
                        document.getElementById('reservationEditModal').classList.remove('ft-modal-open');
                        // Rafraîchit la liste sans recharger la page (forcer re-render même si IDs inchangés)
                        forceReservationsRefresh = true;
                        updateReservations();
                    } else {
                        alert(data.message || 'Erreur lors de la mise à jour');
                    }
                } catch (e) {
                    console.error(e);
                    alert('Erreur lors du traitement de la réponse');
                }
            } else {
                alert('Erreur de connexion (' + xhr.status + ')');
            }
        }
    };
    xhr.onerror = function() {
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
        alert('Erreur réseau lors de la mise à jour');
    };
    xhr.send(formData);
});
</script>
</body>
</html>

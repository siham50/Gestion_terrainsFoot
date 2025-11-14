<?php
// views/public/Newsletter.php
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
require_once __DIR__ . '/../../controllers/NewsletterController.php';
require_once __DIR__ . '/../../classes/Database.php';

$controller = new NewsletterController();
$data = $controller->getNewsletterData($_SESSION['user_id']);

$notifications = $data['notifications'] ?? [];
$unreadCount = $data['unreadCount'] ?? 0;

// Formater les dates
function formatDate($dateString) {
    $date = new DateTime($dateString);
    $now = new DateTime();
    $diff = $now->diff($date);
    
    if ($diff->days == 0) {
        if ($diff->h == 0) {
            if ($diff->i == 0) {
                return 'À l\'instant';
            }
            return 'Il y a ' . $diff->i . ' min';
        }
        return 'Il y a ' . $diff->h . ' h';
    } elseif ($diff->days == 1) {
        return 'Hier';
    } elseif ($diff->days < 7) {
        return 'Il y a ' . $diff->days . ' jours';
    } else {
        $months = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'];
        return $date->format('d') . ' ' . $months[$date->format('n') - 1] . ' ' . $date->format('Y');
    }
}

// Obtenir l'icône selon le type
function getNotificationIcon($type) {
    $icons = [
        'nouveau_terrain' => '<path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5M2 12l10 5 10-5"/>',
        'modification_terrain' => '<path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>',
        'nouveau_tournoi' => '<path d="M8 21h8"/><path d="M12 17v4"/><path d="M7 4h10v5a5 5 0 0 1-10 0V4z"/><path d="M5 6H3a3 3 0 0 0 3 3"/><path d="M19 6h2a3 3 0 0 1-3 3"/>',
        'modification_prix' => '<circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>',
        'general' => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/>'
    ];
    return $icons[$type] ?? $icons['general'];
}
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FootTime - Newsletter</title>
    <link rel="stylesheet" href="../../assets/css/Style.css">
</head>
<body>
    <?php require '../../includes/Navbar.php'; ?>
    <div class="ft-shell">
        <main class="ft-content" aria-label="Contenu principal">
            <section class="ft-page">
                <header class="ft-page-header">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <h1 class="ft-h1">Newsletter</h1>
                            <p class="ft-sub">Restez informé des dernières nouveautés</p>
                        </div>
                        <?php if ($unreadCount > 0): ?>
                            <button class="ft-btn" onclick="markAllAsRead()" style="margin-top: 0;">
                                <svg class="ft-ic" viewBox="0 0 24 24">
                                    <path d="M20 6L9 17l-5-5"/>
                                </svg>
                                Tout marquer comme lu
                            </button>
                        <?php endif; ?>
                    </div>
                </header>

                <?php if ($unreadCount > 0): ?>
                    <div class="ft-card" style="background: rgba(43, 217, 151, 0.1); border-color: #1a6a58; margin-bottom: 18px;">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <svg class="ft-ic" viewBox="0 0 24 24" style="color: var(--ft-accent);">
                                <path d="M6 8a6 6 0 0 1 12 0v4l1.5 2.5c.3.5 0 1.5-.8 1.5H5.3c-.8 0-1.1-1-.8-1.5L6 12V8"/>
                            </svg>
                            <div>
                                <strong style="color: var(--ft-accent);"><?php echo $unreadCount; ?> nouvelle(s) notification(s)</strong>
                                <p class="ft-muted" style="margin: 0; font-size: 13px;">Vous avez des notifications non lues</p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="ft-list" id="notificationsList">
                    <?php if (empty($notifications)): ?>
                        <div class="ft-card">
                            <p class="ft-muted" style="text-align: center; padding: 2rem;">
                                Aucune notification pour le moment.
                            </p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($notifications as $notification): ?>
                            <?php
                            $isUnread = !$notification['lu'];
                            $type = $notification['type'] ?? 'general';
                            ?>
                            <article class="ft-card ft-booking" data-id="<?php echo $notification['id']; ?>" 
                                     style="<?php echo $isUnread ? 'border-left: 3px solid var(--ft-accent);' : ''; ?>">
                                <div class="ft-booking-main">
                                    <div style="display: flex; align-items: start; gap: 12px;">
                                        <div style="width: 40px; height: 40px; display: grid; place-items: center; 
                                                    background: rgba(43, 217, 151, 0.1); border: 1px solid #1a6a58; 
                                                    border-radius: 10px; flex-shrink: 0;">
                                            <svg class="ft-ic" viewBox="0 0 24 24" style="width: 20px; height: 20px;">
                                                <?php echo getNotificationIcon($type); ?>
                                            </svg>
                                        </div>
                                        <div style="flex: 1;">
                                            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                                                <h3 class="ft-title" style="margin: 0;"><?php echo htmlspecialchars($notification['titre']); ?></h3>
                                                <?php if ($isUnread): ?>
                                                    <span class="ft-badge" style="background: var(--ft-accent); color: #083328; font-size: 10px;">Nouveau</span>
                                                <?php endif; ?>
                                            </div>
                                            <p style="margin: 0 0 8px 0; color: var(--ft-text-dim);">
                                                <?php echo htmlspecialchars($notification['message']); ?>
                                            </p>
                                            <div style="display: flex; align-items: center; gap: 12px; font-size: 12px; color: var(--ft-text-dim);">
                                                <span><?php echo formatDate($notification['date_creation']); ?></span>
                                                <span>•</span>
                                                <span style="text-transform: capitalize;"><?php echo str_replace('_', ' ', $type); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php if ($isUnread): ?>
                                    <div class="ft-booking-aside">
                                        <button class="ft-btn" onclick="markAsRead(<?php echo $notification['id']; ?>)">
                                            <svg class="ft-ic" viewBox="0 0 24 24">
                                                <path d="M20 6L9 17l-5-5"/>
                                            </svg>
                                            Marquer comme lu
                                        </button>
                                    </div>
                                <?php endif; ?>
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
const NEWSLETTER_UPDATE_INTERVAL = 2000; // Mise à jour toutes les 2 secondes
let lastCheckDate = new Date().toISOString();
let currentNotificationIds = [];

// Initialiser les IDs des notifications actuelles au chargement
document.addEventListener('DOMContentLoaded', function() {
    initializeNotificationIds();
    // Démarrer la mise à jour automatique
    setInterval(updateNewsletter, NEWSLETTER_UPDATE_INTERVAL);
    
    // Mettre à jour le badge dans la navbar
    updateNotificationBadge();
    setInterval(updateNotificationBadge, NEWSLETTER_UPDATE_INTERVAL);
});

// Initialiser la liste des IDs de notifications
function initializeNotificationIds() {
    const notificationCards = document.querySelectorAll('.ft-booking[data-id]');
    currentNotificationIds = Array.from(notificationCards).map(card => {
        return parseInt(card.getAttribute('data-id'));
    }).filter(id => !isNaN(id));
}

// Mettre à jour les notifications via AJAX (récupère seulement les nouvelles)
function updateNewsletter() {
    const xhr = new XMLHttpRequest();
    xhr.open('GET', '../../controllers/NewsletterController.php?action=get_new_notifications&last_check=' + encodeURIComponent(lastCheckDate), true);
    
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            try {
                const response = JSON.parse(xhr.responseText);
                if (response.success && response.notifications) {
                    const newNotifications = response.notifications || [];
                    
                    // Vérifier s'il y a de nouvelles notifications
                    const newNotificationIds = newNotifications.map(n => parseInt(n.id));
                    const hasNewNotifications = newNotificationIds.some(id => !currentNotificationIds.includes(id));
                    
                    if (hasNewNotifications && newNotifications.length > 0) {
                        // Ajouter seulement les nouvelles notifications en haut
                        addNewNotifications(newNotifications);
                        currentNotificationIds = [...new Set([...newNotificationIds, ...currentNotificationIds])];
                        
                        // Jouer un son de notification
                        playNotificationSound();
                    }
                    
                    // Mettre à jour le badge
                    updateNotificationBadgeCount(response.unreadCount || 0);
                }
            } catch (e) {
                console.error('Erreur lors de la mise à jour de la newsletter:', e);
            }
        }
    };
    
    xhr.onerror = function() {
        console.error('Erreur réseau lors de la mise à jour de la newsletter');
    };
    
    xhr.send();
}

// Ajouter les nouvelles notifications en haut de la liste
function addNewNotifications(notifications) {
    const notificationsList = document.getElementById('notificationsList');
    if (!notificationsList) return;
    
    // Filtrer seulement les nouvelles (pas déjà présentes)
    const existingIds = currentNotificationIds;
    const trulyNew = notifications.filter(n => !existingIds.includes(parseInt(n.id)));
    
    if (trulyNew.length === 0) return;
    
    // Si la liste est vide, remplacer le message
    if (notificationsList.querySelector('.ft-card') && notificationsList.querySelector('.ft-card').textContent.includes('Aucune notification')) {
        notificationsList.innerHTML = '';
    }
    
    // Ajouter les nouvelles notifications en haut
    trulyNew.forEach(notification => {
        const notificationHtml = createNotificationHTML(notification);
        notificationsList.insertAdjacentHTML('afterbegin', notificationHtml);
    });
    
    // Mettre à jour lastCheckDate
    lastCheckDate = new Date().toISOString();
}

// Créer le HTML d'une notification
function createNotificationHTML(notification) {
    const isUnread = !notification.lu;
    const type = notification.type || 'general';
    const dateFormatted = formatDate(notification.date_creation);
    const typeFormatted = type.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
    const icon = getNotificationIcon(type);
    
    return `
        <article class="ft-card ft-booking" data-id="${notification.id}" 
                 style="${isUnread ? 'border-left: 3px solid var(--ft-accent); animation: slideIn 0.3s ease-out;' : ''}">
            <div class="ft-booking-main">
                <div style="display: flex; align-items: start; gap: 12px;">
                    <div style="width: 40px; height: 40px; display: grid; place-items: center; 
                                background: rgba(43, 217, 151, 0.1); border: 1px solid #1a6a58; 
                                border-radius: 10px; flex-shrink: 0;">
                        <svg class="ft-ic" viewBox="0 0 24 24" style="width: 20px; height: 20px;">
                            ${icon}
                        </svg>
                    </div>
                    <div style="flex: 1;">
                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                            <h3 class="ft-title" style="margin: 0;">${escapeHtml(notification.titre)}</h3>
                            ${isUnread ? '<span class="ft-badge" style="background: var(--ft-accent); color: #083328; font-size: 10px;">Nouveau</span>' : ''}
                        </div>
                        <p style="margin: 0 0 8px 0; color: var(--ft-text-dim);">
                            ${escapeHtml(notification.message)}
                        </p>
                        <div style="display: flex; align-items: center; gap: 12px; font-size: 12px; color: var(--ft-text-dim);">
                            <span>${dateFormatted}</span>
                            <span>•</span>
                            <span>${typeFormatted}</span>
                        </div>
                    </div>
                </div>
            </div>
            ${isUnread ? `
                <div class="ft-booking-aside">
                    <button class="ft-btn" onclick="markAsRead(${notification.id})">
                        <svg class="ft-ic" viewBox="0 0 24 24">
                            <path d="M20 6L9 17l-5-5"/>
                        </svg>
                        Marquer comme lu
                    </button>
                </div>
            ` : ''}
        </article>
    `;
}

// Mettre à jour le badge de notification dans la navbar
function updateNotificationBadge() {
    const xhr = new XMLHttpRequest();
    xhr.open('GET', '../../controllers/NewsletterController.php?action=get_unread_count', true);
    
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            try {
                const response = JSON.parse(xhr.responseText);
                if (response.success) {
                    updateNotificationBadgeCount(response.count || 0);
                }
            } catch (e) {
                console.error('Erreur lors de la mise à jour du badge:', e);
            }
        }
    };
    
    xhr.send();
}

// Mettre à jour le compteur du badge
function updateNotificationBadgeCount(count) {
    const badge = document.getElementById('notificationBadge');
    if (count > 0) {
        if (!badge) {
            // Créer le badge s'il n'existe pas
            const notificationBtn = document.querySelector('.ft-icon-btn[aria-label="Notifications"]');
            if (notificationBtn) {
                const badgeEl = document.createElement('span');
                badgeEl.id = 'notificationBadge';
                badgeEl.className = 'ft-notification-badge';
                badgeEl.textContent = count > 99 ? '99+' : count;
                notificationBtn.appendChild(badgeEl);
            }
        } else {
            badge.textContent = count > 99 ? '99+' : count;
            badge.style.display = 'block';
        }
    } else if (badge) {
        badge.style.display = 'none';
    }
}

// Marquer une notification comme lue
function markAsRead(id) {
    const formData = new FormData();
    formData.append('id', id);
    
    fetch('../../controllers/NewsletterController.php?action=mark_as_read', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Mettre à jour l'affichage
            const notificationCard = document.querySelector(`.ft-booking[data-id="${id}"]`);
            if (notificationCard) {
                notificationCard.style.borderLeft = 'none';
                const badge = notificationCard.querySelector('.ft-badge');
                if (badge) badge.remove();
                const markBtn = notificationCard.querySelector('.ft-booking-aside');
                if (markBtn) markBtn.remove();
            }
            // Mettre à jour le badge
            updateNotificationBadge();
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
    });
}

// Marquer toutes les notifications comme lues
function markAllAsRead() {
    fetch('../../controllers/NewsletterController.php?action=mark_all_as_read', {
        method: 'POST'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Recharger la page pour mettre à jour l'affichage
            location.reload();
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
    });
}

// Jouer un son de notification
function playNotificationSound() {
    // Créer un son de notification simple
    const audioContext = new (window.AudioContext || window.webkitAudioContext)();
    const oscillator = audioContext.createOscillator();
    const gainNode = audioContext.createGain();
    
    oscillator.connect(gainNode);
    gainNode.connect(audioContext.destination);
    
    oscillator.frequency.value = 800;
    oscillator.type = 'sine';
    
    gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
    gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.3);
    
    oscillator.start(audioContext.currentTime);
    oscillator.stop(audioContext.currentTime + 0.3);
}

// Fonctions utilitaires
function formatDate(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const diff = now - date;
    const minutes = Math.floor(diff / 60000);
    const hours = Math.floor(minutes / 60);
    const days = Math.floor(hours / 24);
    
    if (minutes < 1) return 'À l\'instant';
    if (minutes < 60) return 'Il y a ' + minutes + ' min';
    if (hours < 24) return 'Il y a ' + hours + ' h';
    if (days === 1) return 'Hier';
    if (days < 7) return 'Il y a ' + days + ' jours';
    
    const months = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'];
    return date.getDate() + ' ' + months[date.getMonth()] + ' ' + date.getFullYear();
}

function getNotificationIcon(type) {
    const icons = {
        'nouveau_terrain': '<path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5M2 12l10 5 10-5"/>',
        'modification_terrain': '<path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>',
        'nouveau_tournoi': '<path d="M8 21h8"/><path d="M12 17v4"/><path d="M7 4h10v5a5 5 0 0 1-10 0V4z"/><path d="M5 6H3a3 3 0 0 0 3 3"/><path d="M19 6h2a3 3 0 0 1-3 3"/>',
        'modification_prix': '<circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>',
        'general': '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/>'
    };
    return icons[type] || icons['general'];
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>

<style>
@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.ft-notification-badge {
    position: absolute;
    top: -4px;
    right: -4px;
    background: #ff4444;
    color: white;
    border-radius: 10px;
    padding: 2px 6px;
    font-size: 10px;
    font-weight: bold;
    min-width: 18px;
    text-align: center;
    line-height: 1.4;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

.ft-icon-btn[aria-label="Notifications"] {
    position: relative;
}
</style>
</body>
</html>


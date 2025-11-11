<?php
?>
<header class="ft-topbar">
  <div class="ft-brand">
    <div class="ft-logo">⚽</div>
    <div class="ft-brand-text">
      <div class="ft-brand-title">FootTime</div>
      <div class="ft-brand-sub">Réservation &amp; Tournois</div>
    </div>
  </div>
  <nav class="ft-actions">
    <button class="ft-icon-btn" aria-label="Notifications" onclick="window.location.href='Newsletter.php'" style="position: relative; cursor: pointer;">
      <svg class="ft-ic" viewBox="0 0 24 24" aria-hidden="true">
        <path d="M6 8a6 6 0 0 1 12 0v4l1.5 2.5c.3.5 0 1.5-.8 1.5H5.3c-.8 0-1.1-1-.8-1.5L6 12V8"/>
        <path d="M9 18a3 3 0 0 0 6 0"/>
      </svg>
      <span id="notificationBadge" class="ft-notification-badge" style="display: none;"></span>
    </button>
    <button class="ft-icon-btn" aria-label="Profil">
      <svg class="ft-ic" viewBox="0 0 24 24" aria-hidden="true">
        <circle cx="12" cy="8" r="4"/>
        <path d="M4 20c2.5-3 5.5-4.5 8-4.5S17.5 17 20 20"/>
      </svg>
    </button>
  </nav>
  <button class="ft-burger" aria-label="Ouvrir le menu">
    <svg class="ft-ic" viewBox="0 0 24 24" aria-hidden="true">
      <path d="M3 6h18M3 12h18M3 18h18"/>
    </svg>
  </button>
  <script>
  (function(){
    var burger = document.currentScript.previousElementSibling;
    var sidebar = document.querySelector('.ft-sidebar');
    if(burger && sidebar){
      burger.addEventListener('click', function(){
        sidebar.classList.toggle('is-open');
      });
    }
  })();
  
  // Mise à jour du badge de notification
  (function(){
    function updateNotificationBadge() {
      var xhr = new XMLHttpRequest();
      xhr.open('GET', '../../controllers/NewsletterController.php?action=get_unread_count', true);
      xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
          try {
            var response = JSON.parse(xhr.responseText);
            if (response.success) {
              var count = response.count || 0;
              var badge = document.getElementById('notificationBadge');
              if (count > 0) {
                if (!badge) {
                  var notificationBtn = document.querySelector('.ft-icon-btn[aria-label="Notifications"]');
                  if (notificationBtn) {
                    badge = document.createElement('span');
                    badge.id = 'notificationBadge';
                    badge.className = 'ft-notification-badge';
                    notificationBtn.appendChild(badge);
                  }
                }
                if (badge) {
                  badge.textContent = count > 99 ? '99+' : count;
                  badge.style.display = 'block';
                }
              } else if (badge) {
                badge.style.display = 'none';
              }
            }
          } catch (e) {
            console.error('Erreur badge:', e);
          }
        }
      };
      xhr.send();
    }
    
    // Mettre à jour au chargement et toutes les 10 secondes
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', updateNotificationBadge);
    } else {
      updateNotificationBadge();
    }
    setInterval(updateNotificationBadge, 10000);
  })();
  </script>
</header>

<aside class="ft-sidebar">
  <div class="ft-sidebar-title">Menu Principal</div>
  <ul class="ft-menu">
    <li><a href="Home.php">
      <svg class="ft-ic" viewBox="0 0 24 24" aria-hidden="true">
        <path d="M3 11l9-7 9 7"/>
        <path d="M5 10v9h14v-9"/>
      </svg>
      Accueil</a></li>
    <li><a href="MesReservations.php">
      <svg class="ft-ic" viewBox="0 0 24 24" aria-hidden="true">
        <rect x="3" y="4" width="18" height="18" rx="2"/>
        <path d="M8 2v4M16 2v4M3 10h18"/>
      </svg>
      Mes réservations</a></li>
    <li><a href="MesTournois.php">
      <svg class="ft-ic" viewBox="0 0 24 24" aria-hidden="true">
        <path d="M8 21h8"/>
        <path d="M12 17v4"/>
        <path d="M7 4h10v5a5 5 0 0 1-10 0V4z"/>
        <path d="M5 6H3a3 3 0 0 0 3 3"/>
        <path d="M19 6h2a3 3 0 0 1-3 3"/>
      </svg>
      Tournois</a></li>
    <li><a href="Newsletter.php">
      <svg class="ft-ic" viewBox="0 0 24 24" aria-hidden="true">
        <rect x="3" y="5" width="18" height="14" rx="2"/>
        <path d="M3 7l9 6 9-6"/>
      </svg>
      Newsletter</a></li>
  </ul>
</aside>

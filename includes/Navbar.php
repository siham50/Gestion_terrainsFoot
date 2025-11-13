<?php
	$isAdmin = strpos($_SERVER['PHP_SELF'] ?? '', '/views/admin/') !== false;
	$publicPrefix = $isAdmin ? '../public/' : '';
	$adminPrefix = $isAdmin ? '' : '../admin/';

	$isLoggedIn = !empty($_SESSION['user_id']);
	$userName = trim($_SESSION['user_name'] ?? 'Utilisateur');
	$userRole = $_SESSION['user_role'] ?? '';
	$isAdminRole = $userRole === 'admin';

	$roleLabels = [
		'admin' => 'Administrateur',
		'client' => 'Membre'
	];
	$profileRoleLabel = $roleLabels[$userRole] ?? ($isLoggedIn ? ucfirst($userRole) : 'Invité');

	$profileInitials = '';
	if ($isLoggedIn && $userName) {
		$parts = preg_split('/\s+/', $userName);
		if ($parts) {
			$first = strtoupper(substr($parts[0], 0, 1));
			$last = '';
			if (count($parts) > 1) {
				$lastPart = end($parts);
				$last = strtoupper(substr($lastPart, 0, 1));
			}
			$profileInitials = $first . $last;
		}
	}
	$profileInitials = $profileInitials ?: strtoupper(substr($userName, 0, 1));

	$profileMenuItems = [];
	if ($isLoggedIn) {
		if ($isAdminRole) {
			$profileMenuItems = [
				[
					'href' => $adminPrefix . 'index.php',
					'label' => 'Tableau de bord',
					'desc' => 'Vue d’ensemble',
					'icon' => '<path d="M4 4h7v7H4z"/><path d="M13 4h7v4h-7z"/><path d="M13 10h7v10h-7z"/><path d="M4 13h7v7H4z"/>'
				],
				[
					'href' => $adminPrefix . 'index.php?section=terrains',
					'label' => 'Terrains & inventaire',
					'desc' => 'Disponibilité, surfaces',
					'icon' => '<path d="M4 6h16l-2 12H6z"/><path d="M9 9l3 3 3-3"/>'
				],
				[
					'href' => $adminPrefix . 'index.php?section=users',
					'label' => 'Utilisateurs',
					'desc' => 'Comptes & rôles',
					'icon' => '<circle cx="9" cy="8" r="3"/><circle cx="17" cy="10" r="3"/><path d="M5 20v-1a4 4 0 0 1 4-4h0"/><path d="M15 20v-1a4 4 0 0 1 3-3.87"/>'
				],
				[
					'href' => $publicPrefix . 'Newsletter.php',
					'label' => 'Notifications',
					'desc' => 'Demandes & alertes',
					'icon' => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/>'
				],
			];
		} else {
			$profileMenuItems = [
				[
					'href' => $publicPrefix . 'MesReservations.php',
					'label' => 'Mes réservations',
					'desc' => 'Suivre mes terrains',
					'icon' => '<rect x="3" y="4" width="18" height="18" rx="2"/><path d="M8 2v4M16 2v4M3 10h18"/>'
				],
				[
					'href' => $publicPrefix . 'MesTournois.php',
					'label' => 'Mes tournois',
					'desc' => 'Classements & équipes',
					'icon' => '<path d="M8 21h8"/><path d="M12 17v4"/><path d="M7 4h10v5a5 5 0 0 1-10 0z"/><path d="M5 6H3a3 3 0 0 0 3 3"/><path d="M19 6h2a3 3 0 0 1-3 3"/>'
				],
				[
					'href' => $publicPrefix . 'Newsletter.php',
					'label' => 'Notifications',
					'desc' => 'Messages & rappels',
					'icon' => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/>'
				],
			];
		}
	}
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
    <button class="ft-icon-btn" aria-label="Notifications" onclick="window.location.href='<?php echo $publicPrefix; ?>Newsletter.php'" style="position: relative; cursor: pointer;">
      <svg class="ft-ic" viewBox="0 0 24 24" aria-hidden="true">
        <path d="M6 8a6 6 0 0 1 12 0v4l1.5 2.5c.3.5 0 1.5-.8 1.5H5.3c-.8 0-1.1-1-.8-1.5L6 12V8"/>
        <path d="M9 18a3 3 0 0 0 6 0"/>
      </svg>
      <span id="notificationBadge" class="ft-notification-badge" style="display: none;"></span>
    </button>
    <div class="ft-profile" style="position: relative;">
      <button class="ft-icon-btn" id="ftProfileBtn" aria-label="Profil" type="button">
        <svg class="ft-ic" viewBox="0 0 24 24" aria-hidden="true">
          <circle cx="12" cy="8" r="4"/>
          <path d="M4 20c2.5-3 5.5-4.5 8-4.5S17.5 17 20 20"/>
        </svg>
      </button>
      <div id="ftProfileMenu" class="ft-profile-menu" aria-hidden="true">
        <?php if ($isLoggedIn): ?>
          <div class="ft-profile-menu__header">
            <div class="ft-profile-avatar"><?php echo htmlspecialchars($profileInitials, ENT_QUOTES, 'UTF-8'); ?></div>
            <div class="ft-profile-menu__identity">
              <div class="ft-profile-name"><?php echo htmlspecialchars($userName, ENT_QUOTES, 'UTF-8'); ?></div>
              <div class="ft-profile-role"><?php echo htmlspecialchars($profileRoleLabel, ENT_QUOTES, 'UTF-8'); ?></div>
            </div>
          </div>
          <?php if (!empty($profileMenuItems)): ?>
            <ul class="ft-profile-menu__list">
              <?php foreach ($profileMenuItems as $item): ?>
                <li>
                  <a class="ft-profile-menu__item" href="<?php echo htmlspecialchars($item['href'], ENT_QUOTES, 'UTF-8'); ?>">
                    <span class="ft-profile-menu__icon">
                      <svg class="ft-ic" viewBox="0 0 24 24" aria-hidden="true"><?php echo $item['icon']; ?></svg>
                    </span>
                    <span class="ft-profile-menu__labels">
                      <span class="ft-profile-menu__label"><?php echo htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8'); ?></span>
                      <?php if (!empty($item['desc'])): ?>
                        <span class="ft-profile-menu__desc"><?php echo htmlspecialchars($item['desc'], ENT_QUOTES, 'UTF-8'); ?></span>
                      <?php endif; ?>
                    </span>
                  </a>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
          <div class="ft-profile-menu__footer">
            <a class="ft-profile-menu__item ft-profile-menu__item--danger" href="<?php echo $publicPrefix; ?>logout.php">
              <span class="ft-profile-menu__icon">
                <svg class="ft-ic" viewBox="0 0 24 24" aria-hidden="true"><path d="M10 17l-5-5 5-5"/><path d="M15 12H5"/><path d="M19 21V3"/></svg>
              </span>
              <span class="ft-profile-menu__labels">
                <span class="ft-profile-menu__label">Se déconnecter</span>
                <span class="ft-profile-menu__desc">Terminer la session</span>
              </span>
            </a>
          </div>
        <?php else: ?>
          <div class="ft-profile-menu__header">
            <div class="ft-profile-avatar">?</div>
            <div class="ft-profile-menu__identity">
              <div class="ft-profile-name">Bienvenue</div>
              <div class="ft-profile-role">Invité</div>
            </div>
          </div>
          <ul class="ft-profile-menu__list">
            <li>
              <a class="ft-profile-menu__item" href="<?php echo $publicPrefix; ?>login.php">
                <span class="ft-profile-menu__icon">
                  <svg class="ft-ic" viewBox="0 0 24 24" aria-hidden="true"><path d="M14 7l5 5-5 5"/><path d="M19 12H9"/><path d="M5 21V3"/></svg>
                </span>
                <span class="ft-profile-menu__labels">
                  <span class="ft-profile-menu__label">Se connecter</span>
                  <span class="ft-profile-menu__desc">Accéder à mon compte</span>
                </span>
              </a>
            </li>
            <li>
              <a class="ft-profile-menu__item" href="<?php echo $publicPrefix; ?>register.php">
                <span class="ft-profile-menu__icon">
                  <svg class="ft-ic" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
                </span>
                <span class="ft-profile-menu__labels">
                  <span class="ft-profile-menu__label">S'inscrire</span>
                  <span class="ft-profile-menu__desc">Créer un nouveau profil</span>
                </span>
              </a>
            </li>
          </ul>
        <?php endif; ?>
      </div>
    </div>
  </nav>
  <button class="ft-burger" aria-label="Ouvrir le menu">
    <svg class="ft-ic" viewBox="0 0 24 24" aria-hidden="true">
      <path d="M3 6h18M3 12h18M3 18h18"/>
    </svg>
  </button>
  <script>
  (function(){
    function initBurger(){
      var burger = document.querySelector('.ft-burger');
      var sidebar = document.querySelector('.ft-sidebar');
      var overlay = document.querySelector('.ft-sidebar-overlay');
      if(!burger || !sidebar){ return; }
      function setOpen(open){
        sidebar.classList.toggle('is-open', open);
        document.body.classList.toggle('ft-sidebar-open', open);
        if(overlay){
          overlay.style.display = open ? 'block' : 'none';
        }
      }
      burger.addEventListener('click', function(){
        var willOpen = !sidebar.classList.contains('is-open');
        setOpen(willOpen);
      });
      if(overlay){
        overlay.addEventListener('click', function(){ setOpen(false); });
      }
      window.addEventListener('resize', function(){
        if(window.innerWidth > 900){
          setOpen(false);
        }
      });
    }
    function initProfile(){
      var btn = document.getElementById('ftProfileBtn');
      var menu = document.getElementById('ftProfileMenu');
      if(!btn || !menu) return;
      function closeMenu(){
        if(!menu.classList.contains('is-open')) return;
        menu.classList.remove('is-open');
        menu.setAttribute('aria-hidden', 'true');
        document.removeEventListener('click', onDoc);
      }
      function openMenu(){
        if(menu.classList.contains('is-open')) return;
        menu.classList.add('is-open');
        menu.setAttribute('aria-hidden', 'false');
        setTimeout(function(){ document.addEventListener('click', onDoc); }, 0);
      }
      function onDoc(e){
        if(!menu.contains(e.target) && e.target !== btn){
          closeMenu();
        }
      }
      btn.addEventListener('click', function(e){
        e.stopPropagation();
        if(menu.classList.contains('is-open')){
          closeMenu();
        } else {
          openMenu();
        }
      });
      menu.addEventListener('click', function(e){
        e.stopPropagation();
      });
      document.addEventListener('keydown', function(e){
        if(e.key === 'Escape'){
          closeMenu();
        }
      });
    }
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', function(){ initBurger(); initProfile(); });
    } else {
      initBurger(); initProfile();
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
    <li><a href="<?php echo $publicPrefix; ?>Home.php">
      <svg class="ft-ic" viewBox="0 0 24 24" aria-hidden="true">
        <path d="M3 11l9-7 9 7"/>
        <path d="M5 10v9h14v-9"/>
      </svg>
      Accueil</a></li>
    <li><a href="<?php echo $publicPrefix; ?>MesReservations.php">
      <svg class="ft-ic" viewBox="0 0 24 24" aria-hidden="true">
        <rect x="3" y="4" width="18" height="18" rx="2"/>
        <path d="M8 2v4M16 2v4M3 10h18"/>
      </svg>
      Mes réservations</a></li>
    <li><a href="<?php echo $publicPrefix; ?>MesTournois.php">
      <svg class="ft-ic" viewBox="0 0 24 24" aria-hidden="true">
        <path d="M8 21h8"/>
        <path d="M12 17v4"/>
        <path d="M7 4h10v5a5 5 0 0 1-10 0V4z"/>
        <path d="M5 6H3a3 3 0 0 0 3 3"/>
        <path d="M19 6h2a3 3 0 0 1-3 3"/>
      </svg>
      Tournois</a></li>
    <li><a href="<?php echo $publicPrefix; ?>Newsletter.php">
      <svg class="ft-ic" viewBox="0 0 24 24" aria-hidden="true">
        <rect x="3" y="5" width="18" height="14" rx="2"/>
        <path d="M3 7l9 6 9-6"/>
      </svg>
      Newsletter</a></li>
  </ul>
</aside>
<div class="ft-sidebar-overlay"></div>

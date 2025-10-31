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
    <button class="ft-icon-btn" aria-label="Notifications">
      <svg class="ft-ic" viewBox="0 0 24 24" aria-hidden="true">
        <path d="M6 8a6 6 0 0 1 12 0v4l1.5 2.5c.3.5 0 1.5-.8 1.5H5.3c-.8 0-1.1-1-.8-1.5L6 12V8"/>
        <path d="M9 18a3 3 0 0 0 6 0"/>
      </svg>
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
  </script>
</header>

<aside class="ft-sidebar">
  <div class="ft-sidebar-title">Menu Principal</div>
  <ul class="ft-menu">
<<<<<<< HEAD
    <li><a href="home.php">
=======
    <li class="<?php echo $currentPage === 'home' ? 'is-active' : ''; ?>"><a href="index.php?page=home">
>>>>>>> origin/main
      <svg class="ft-ic" viewBox="0 0 24 24" aria-hidden="true">
        <path d="M3 11l9-7 9 7"/>
        <path d="M5 10v9h14v-9"/>
      </svg>
      Accueil</a></li>
<<<<<<< HEAD
    <li><a href="MesReservations.php">
=======
    <li class="<?php echo $currentPage === 'reservations' ? 'is-active' : ''; ?>"><a href="index.php?page=reservations">
>>>>>>> origin/main
      <svg class="ft-ic" viewBox="0 0 24 24" aria-hidden="true">
        <rect x="3" y="4" width="18" height="18" rx="2"/>
        <path d="M8 2v4M16 2v4M3 10h18"/>
      </svg>
      Mes réservations</a></li>
<<<<<<< HEAD
    <li><a href="MesTournois.php">
=======
    <li class="<?php echo $currentPage === 'tournois' ? 'is-active' : ''; ?>"><a href="index.php?page=tournois">
>>>>>>> origin/main
      <svg class="ft-ic" viewBox="0 0 24 24" aria-hidden="true">
        <path d="M8 21h8"/>
        <path d="M12 17v4"/>
        <path d="M7 4h10v5a5 5 0 0 1-10 0V4z"/>
        <path d="M5 6H3a3 3 0 0 0 3 3"/>
        <path d="M19 6h2a3 3 0 0 1-3 3"/>
      </svg>
      Tournois</a></li>
<<<<<<< HEAD
    <li><a href="Newsletter.php">
=======
    <li class="<?php echo $currentPage === 'newsletter' ? 'is-active' : ''; ?>"><a href="index.php?page=newsletter">
>>>>>>> origin/main
      <svg class="ft-ic" viewBox="0 0 24 24" aria-hidden="true">
        <rect x="3" y="5" width="18" height="14" rx="2"/>
        <path d="M3 7l9 6 9-6"/>
      </svg>
      Newsletter</a></li>
  </ul>
</aside>
<<<<<<< HEAD
=======

>>>>>>> origin/main

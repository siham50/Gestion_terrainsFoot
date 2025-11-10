<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FootTime - Accueil</title>
    <link rel="stylesheet" href="../../assets/css/Style.css">
</head>
<body>
<?php require __DIR__ . '/../../includes/Navbar.php'; ?>

<main class="ft-shell">
  <div class="ft-content">
    <!-- Barre de recherche et filtres -->
    <div class="ft-search-section">
      <div class="ft-search-bar">
        <div class="ft-search-input">
          <svg class="ft-ic" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
          <input type="text" placeholder="Rechercher un terrain, une ville...">
        </div>
        <button class="ft-btn ft-filter-btn" id="filterToggle">
          <svg class="ft-ic" viewBox="0 0 24 24"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
          Filtres
        </button>
      </div>

      <!-- Filtres déroulants -->
      <div class="ft-filters-panel" id="filtersPanel">
        <h2>Filtres de recherche</h2>
        <p>Affinez votre recherche en sélectionnant les critères souhaités</p>
        
        <div class="ft-filter-group">
          <h3>Type de terrain</h3>
          <div class="ft-checkbox-list">
            <label><input type="checkbox"> Foot à 5 (5x5)</label>
            <label><input type="checkbox"> Foot à 7 (7x7)</label>
            <label><input type="checkbox"> Foot à 11 (11x11)</label>
          </div>
        </div>
        
        <div class="ft-filter-group">
          <h3>Type de gazon</h3>
          <div class="ft-checkbox-list">
            <label><input type="checkbox"> Gazon naturel</label>
            <label><input type="checkbox"> Gazon synthétique</label>
            <label><input type="checkbox"> Gazon hybride</label>
          </div>
        </div>
        
        <div class="ft-filter-group">
          <h3>Créneaux horaires disponibles</h3>
          <div class="ft-time-slots">
            <div class="ft-time-row">
              <label><input type="checkbox"> 08:00 - 09:00</label>
              <label><input type="checkbox"> 09:00 - 10:00</label>
              <label><input type="checkbox"> 10:00 - 11:00</label>
              <label><input type="checkbox"> 11:00 - 12:00</label>
              <label><input type="checkbox"> 12:00 - 13:00</label>
            </div>
            <div class="ft-time-row">
              <label><input type="checkbox"> 13:00 - 14:00</label>
              <label><input type="checkbox"> 14:00 - 15:00</label>
              <label><input type="checkbox"> 15:00 - 16:00</label>
              <label><input type="checkbox"> 16:00 - 17:00</label>
              <label><input type="checkbox"> 17:00 - 18:00</label>
            </div>
            <div class="ft-time-row">
              <label><input type="checkbox"> 18:00 - 19:00</label>
              <label><input type="checkbox"> 19:00 - 20:00</label>
              <label><input type="checkbox"> 20:00 - 21:00</label>
              <label><input type="checkbox"> 21:00 - 22:00</label>
              <label><input type="checkbox"> 22:00 - 23:00</label>
            </div>
          </div>
        </div>
        
        <div class="ft-filter-actions">
          <button class="ft-btn ft-btn-secondary">Réinitialiser</button>
          <button class="ft-btn ft-btn-primary">Appliquer les filtres</button>
        </div>
      </div>
    </div>

    <!-- Section Statistiques -->
    <section class="ft-section">
      <h2 class="ft-section-title">Statistiques</h2>
      <p class="ft-section-subtitle">Aperçu de votre activité</p>
      
      <div class="ft-stats">
        <div class="ft-card ft-stat">
          <div class="ft-stat-icon">
            <svg class="ft-ic" viewBox="0 0 24 24"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
          </div>
          <div class="ft-stat-body">
            <div class="ft-stat-value">24</div>
            <div class="ft-stat-label">Réservations totales</div>
            <div class="ft-stat-sub">+12% ce mois</div>
          </div>
        </div>
        
        <div class="ft-card ft-stat">
          <div class="ft-stat-icon">
            <svg class="ft-ic" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
          </div>
          <div class="ft-stat-body">
            <div class="ft-stat-value">48h</div>
            <div class="ft-stat-label">Heures jouées</div>
            <div class="ft-stat-sub">+5h cette semaine</div>
          </div>
        </div>
        
        <div class="ft-card ft-stat">
          <div class="ft-stat-icon">
            <svg class="ft-ic" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
          </div>
          <div class="ft-stat-body">
            <div class="ft-stat-value">8</div>
            <div class="ft-stat-label">Tournois participés</div>
            <div class="ft-stat-sub">2 victoires</div>
          </div>
        </div>
      </div>
    </section>

    <!-- Section Terrains disponibles -->
    <section class="ft-section">
      <div class="ft-section-head">
        <h2 class="ft-section-title">Terrains disponibles</h2>
        <p class="ft-section-subtitle">Réservez un créneau horaire ou plusieurs jours pour un tournoi</p>
      </div>
      
      <div class="ft-list">
        <!-- Terrain 1 -->
        <div class="ft-card ft-booking">
      <div class="ft-booking-content">
        <h3 class="ft-title">Complexe Sportif Mohammed V</h3>
        <div class="ft-badges">
          <span class="ft-badge">Casablanca</span>
          <span class="ft-badge">Foot à 11 • 22 joueurs</span>
          <span class="ft-badge">Gazon naturel</span>
        </div>
        <p class="ft-muted"><strong>15 créneaux disponibles</strong></p>
      </div>
      <div class="ft-booking-actions">
        <div class="ft-price">1000 MAD/heure</div>
        <div><button class="ft-btn ft-btn-primary ft-details-btn" data-terrain="1">Détails</button>
        <button class="ft-btn ft-btn-primary">Réserver</button></div>
      </div>
    </div>
    
        
        <!-- Terrain 2 -->
        <div class="ft-card ft-booking">
      <div class="ft-booking-content">
        <h3 class="ft-title">Stade Moulay Hassan</h3>
        <div class="ft-badges">
          <span class="ft-badge">Rabat</span>
          <span class="ft-badge">Foot à 7 • 14 joueurs</span>
          <span class="ft-badge">Gazon synthétique</span>
        </div>
        <p class="ft-muted"><strong>10 créneaux disponibles</strong></p>
      </div>
      <div class="ft-booking-actions">
        <div class="ft-price">800 MAD/heure</div>
        <div><button class="ft-btn ft-btn-primary ft-details-btn" data-terrain="2">Détails</button>
        <button class="ft-btn ft-btn-primary">Réserver</button></div>
      </div>
    </div>
        
        <!-- Terrain 3 -->
        <div class="ft-card ft-booking">
      <div class="ft-booking-content">
        <h3 class="ft-title">Complexe Ibn Batouta</h3>
        <div class="ft-badges">
          <span class="ft-badge">Tanger</span>
          <span class="ft-badge">Foot à 11 • 22 joueurs</span>
          <span class="ft-badge">Gazon hybride</span>
        </div>
        <p class="ft-muted"><strong>12 créneaux disponibles</strong></p>
      </div>
      <div class="ft-booking-actions">
        <div class="ft-price">900 MAD/heure</div>
        <div><button class="ft-btn ft-btn-primary ft-details-btn" data-terrain="3">Détails</button>
        <button class="ft-btn ft-btn-primary">Réserver</button></div>
      </div>
    </div>
    </section>

    <!-- Section Tournois à venir -->
    <section class="ft-section">
      <div class="ft-section-head">
        <h2 class="ft-section-title">Tournois à venir</h2>
        <p class="ft-section-subtitle">Participez aux prochains tournois et compétitions</p>
      </div>
      
      <div class="ft-list">
        <!-- Tournoi 1 -->
        <div class="ft-card ft-booking">
      <div class="ft-booking-content">
        <h3 class="ft-title">Coupe d'été 2025</h3>
        <ul class="ft-meta">
          <li>
            <svg class="ft-ic" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            Foot à 11
          </li>
          <li>
            <svg class="ft-ic" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            15-18 Août 2025
          </li>
          <li>
            <svg class="ft-ic" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
            Complexe Mohammed V, Casablanca
          </li>
          <li>
            <svg class="ft-ic" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            24/32 équipes inscrites
          </li>
        </ul>
      </div>
      <div class="ft-booking-actions">
        <button class="ft-btn ft-btn-primary">Voir les détails</button>
      </div>
    </div>
        
        <!-- Tournoi 2 -->
         <div class="ft-card ft-booking">
      <div class="ft-booking-content">
        <h3 class="ft-title">Championnat Ramadan</h3>
        <ul class="ft-meta">
          <li>
            <svg class="ft-ic" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            Foot à 5
          </li>
          <li>
            <svg class="ft-ic" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            20-22 Juin 2025
          </li>
          <li>
            <svg class="ft-ic" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
            Moulay Hassan Sport Hall, Rabat
          </li>
          <li>
            <svg class="ft-ic" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            32/32 équipes inscrites
          </li>
        </ul>
      </div>
      <div class="ft-booking-actions">
        <button class="ft-btn ft-btn-primary">Voir les détails</button>
      </div>
    </div>

        <!-- Tournoi 3 -->
        <div class="ft-card ft-booking">
      <div class="ft-booking-content">
        <h3 class="ft-title">Tournoi du nord 2025</h3>
        <ul class="ft-meta">
          <li>
            <svg class="ft-ic" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            Foot à 11
          </li>
          <li>
            <svg class="ft-ic" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            8-10 Août 2025
          </li>
          <li>
            <svg class="ft-ic" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
            Complexe Ibn Batouta, Tanger
          </li>
          <li>
            <svg class="ft-ic" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            12/16 équipes inscrites
          </li>
        </ul>
      </div>
      <div class="ft-booking-actions">
        <button class="ft-btn ft-btn-primary">Voir les détails</button>
      </div>
    </div>
           
      </div>
    </section>
  </div>
</main>

<!-- Modal pour les détails des terrains -->
<div class="ft-modal" id="terrainModal">
  <div class="ft-modal-content">
    <div class="ft-modal-header">
      <h2 id="modalTitle">Détails du terrain</h2>
      <button class="ft-modal-close" id="modalClose">&times;</button>
    </div>
    <div class="ft-modal-body" id="modalBody">
      <!-- Contenu chargé dynamiquement -->
    </div>
  </div>
</div>

<!-- Modal de réservation -->
<div class="ft-modal" id="reservationModal">
  <div class="ft-modal-content">
    <div class="ft-modal-header">
      <h2 id="reservationModalTitle">Réserver un terrain</h2>
      <button class="ft-modal-close" id="reservationModalClose">&times;</button>
    </div>
    <div class="ft-modal-body">
      <form id="reservationForm" action="../../controllers/ReservationController.php" method="POST">
        <input type="hidden" id="resTerrainId" name="idTerrain">

        <div class="ft-form-grid">
          <!-- Informations terrain sélectionné -->
          <div class="ft-form-group">
            <label>Terrain sélectionné</label>
            <input type="text" id="resTerrainName" class="ft-input" disabled>
          </div>

          <!-- Date de réservation -->
          <div class="ft-form-group">
            <label for="dateReservation">Date de réservation</label>
            <input type="date" id="dateReservation" name="dateReservation" class="ft-input" required>
          </div>

          <!-- Créneau horaire -->
          <div class="ft-form-group">
            <label for="idCreneau">Créneau horaire</label>
            <select id="idCreneau" name="idCreneau" class="ft-input" required>
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

          <!-- Taille de terrain (préremplie et non modifiable) -->
          <div class="ft-form-group">
            <label>Taille du terrain</label>
            <input type="hidden" id="resTaille" name="taille">
            <input type="text" id="resTailleDisplay" class="ft-input" disabled>
          </div>

          <!-- Type de terrain (prérempli et non modifiable) -->
          <div class="ft-form-group">
            <label>Type de terrain</label>
            <input type="hidden" id="resType" name="type">
            <input type="text" id="resTypeDisplay" class="ft-input" disabled>
          </div>

          <!-- Options supplémentaires -->
          <div class="ft-form-group">
            <label>Options supplémentaires</label>
            <div class="ft-checkbox-list">
              <label><input type="checkbox" id="ballon" name="ballon" value="1"> Ballon</label>
              <label><input type="checkbox" id="arbitre" name="arbitre" value="1"> Arbitre</label>
              <label><input type="checkbox" id="maillot" name="maillot" value="1"> Maillots</label>
              <label><input type="checkbox" id="douche" name="douche" value="1"> Douche</label>
            </div>
          </div>

          <!-- Informations du client -->
          <!-- (Supprimé: les informations utilisateur sont déjà connues via la session) -->

          <!-- Demande spécifique -->
          <div class="ft-form-group" style="grid-column: 1 / -1;">
            <label for="demande">Demande spécifique</label>
            <textarea id="demande" name="demande" class="ft-input" rows="4" placeholder="Commentaires ou requêtes spécifiques..."></textarea>
          </div>
        </div>

        <div class="ft-modal-actions">
          <button type="button" class="ft-btn ft-btn-secondary" id="reservationCancelBtn">Annuler</button>
          <button type="submit" class="ft-btn ft-btn-primary">Confirmer la réservation</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php require __DIR__ . '/../../includes/Footer.php'; ?>

<script>
// Gestion des filtres
document.getElementById('filterToggle').addEventListener('click', function() {
  const panel = document.getElementById('filtersPanel');
  panel.classList.toggle('ft-filters-open');
});

// Gestion des détails des terrains
document.querySelectorAll('.ft-details-btn').forEach(button => {
  button.addEventListener('click', function() {
    const terrainId = this.getAttribute('data-terrain');
    openTerrainModal(terrainId);
  });
});

// Fermeture de la modal
document.getElementById('modalClose').addEventListener('click', function() {
  document.getElementById('terrainModal').classList.remove('ft-modal-open');
});

// Fonction pour ouvrir la modal avec les détails
function openTerrainModal(terrainId) {
  const modal = document.getElementById('terrainModal');
  const modalTitle = document.getElementById('modalTitle');
  const modalBody = document.getElementById('modalBody');
  
  // Simulation de données - en production, vous récupéreriez ces données depuis une API
  const terrains = {
    1: {
      title: "Complexe Sportif Mohammed V",
      description: "Stade mythique de Casablanca, équipement de classe internationale avec des installations modernes. Idéal pour les compétitions et matchs officiels.",
      features: ["Tribunes de 45,000 places", "Éclairage haute performance", "Vestiaires professionnels", "Parking sécurisé de 2,000 places", "Services médicaux"],
      price: "1000 MAD/heure",
      availability: "15 créneaux disponibles cette semaine",
      location: "Casablanca"
    },
    2: {
      title: "Stade Moulay Hassan",
      description: "Terrain de football à 7 situé au cœur de Rabat. Installation moderne avec gazon synthétique de dernière génération.",
      features: ["Gazon synthétique professionnel", "Vestiaires avec douches", "Éclairage LED", "Snack-bar", "Parking gratuit"],
      price: "800 MAD/heure",
      availability: "10 créneaux disponibles cette semaine",
      location: "Rabat"
    },
    3: {
      title: "Complexe Ibn Batouta",
      description: "Grand complexe sportif à Tanger avec terrain hybride de haute qualité. Vue imprenable sur le détroit de Gibraltar.",
      features: ["Gazon hybride professionnel", "Vestiaires premium", "Salle de fitness", "Cafétéria", "Parking surveillé"],
      price: "900 MAD/heure",
      availability: "12 créneaux disponibles cette semaine",
      location: "Tanger"
    }
  };
  
  const terrain = terrains[terrainId];
  
  modalTitle.textContent = terrain.title;
  modalBody.innerHTML = `
    <p>${terrain.description}</p>
    <h3>Équipements</h3>
    <ul>
      ${terrain.features.map(feature => `<li>${feature}</li>`).join('')}
    </ul>
    <div class="ft-modal-info">
      <p><strong>Tarif:</strong> ${terrain.price}</p>
      <p><strong>Disponibilité:</strong> ${terrain.availability}</p>
    </div>
    <div class="ft-modal-actions">
      <button class="ft-btn ft-btn-primary">Réserver maintenant</button>
    </div>
  `;
  
  modal.classList.add('ft-modal-open');
}

// Fermer la modal en cliquant en dehors
window.addEventListener('click', function(event) {
  const modal = document.getElementById('terrainModal');
  if (event.target === modal) {
    modal.classList.remove('ft-modal-open');
  }
});
</script>
</body>
</html>
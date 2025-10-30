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
            <h3 class="ft-title">Stade Municipal Centre-Ville</h3>
            <div class="ft-badges">
              <span class="ft-badge">Paris 15ème</span>
              <span class="ft-badge">Foot à 5 • 10 joueurs</span>
              <span class="ft-badge">Gazon synthétique</span>
            </div>
            <p class="ft-muted"><strong>12 créneaux disponibles</strong></p>
          </div>
          <div class="ft-booking-actions">
            <div class="ft-price">45€/heure</div>
            <button class="ft-btn ft-btn-primary ft-details-btn" data-terrain="1">Détails</button>
          </div>
        </div>
        
        <!-- Terrain 2 -->
        <div class="ft-card ft-booking">
          <div class="ft-booking-content">
            <h3 class="ft-title">Arena Sport Plus</h3>
            <div class="ft-badges">
              <span class="ft-badge">Lyon 3ème</span>
              <span class="ft-badge">Foot à 7 • 14 joueurs</span>
              <span class="ft-badge">Gazon naturel</span>
            </div>
            <p class="ft-muted"><strong>8 créneaux disponibles</strong></p>
          </div>
          <div class="ft-booking-actions">
            <div class="ft-price">65€/heure</div>
            <button class="ft-btn ft-btn-primary ft-details-btn" data-terrain="2">Détails</button>
          </div>
        </div>
        
        <!-- Terrain 3 -->
        <div class="ft-card ft-booking">
          <div class="ft-booking-content">
            <h3 class="ft-title">Complex Sportif Bordeaux</h3>
            <div class="ft-badges">
              <span class="ft-badge">Bordeaux Centre</span>
              <span class="ft-badge">Foot à 11 • 22 joueurs</span>
              <span class="ft-badge">Gazon hybride</span>
            </div>
            <p class="ft-muted"><strong>5 créneaux disponibles</strong></p>
          </div>
          <div class="ft-booking-actions">
            <div class="ft-price">120€/heure</div>
            <button class="ft-btn ft-btn-primary ft-details-btn" data-terrain="3">Détails</button>
          </div>
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
            <div class="ft-badges">
              <span class="ft-badge ft-chip-ok">Inscriptions ouvertes</span>
            </div>
            <h3 class="ft-title">Coupe d'Été 2025</h3>
            <ul class="ft-meta">
              <li>
                <svg class="ft-ic" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                Foot à 7
              </li>
              <li>
                <svg class="ft-ic" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                15-17 Juillet 2025
              </li>
              <li>
                <svg class="ft-ic" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                Stade Jean Bouin, Paris
              </li>
              <li>
                <svg class="ft-ic" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                12/16 équipes inscrites
              </li>
            </ul>
          </div>
          <div class="ft-booking-actions">
            <div class="ft-price">1500€</div>
            <button class="ft-btn ft-btn-primary">Voir les détails</button>
          </div>
        </div>
        
        <!-- Tournoi 2 -->
        <div class="ft-card ft-booking">
          <div class="ft-booking-content">
            <div class="ft-badges">
              <span class="ft-badge">Complet</span>
            </div>
            <h3 class="ft-title">Ligue des Champions Amateur</h3>
            <ul class="ft-meta">
              <li>
                <svg class="ft-ic" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                Foot à 11
              </li>
              <li>
                <svg class="ft-ic" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                2-4 Août 2025
              </li>
              <li>
                <svg class="ft-ic" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                Parc OL, Lyon
              </li>
              <li>
                <svg class="ft-ic" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                8/8 équipes inscrites
              </li>
            </ul>
          </div>
          <div class="ft-booking-actions">
            <div class="ft-price">3000€</div>
            <button class="ft-btn ft-btn-primary">Voir les détails</button>
          </div>
        </div>
        
        <!-- Tournoi 3 -->
        <div class="ft-card ft-booking">
          <div class="ft-booking-content">
            <div class="ft-badges">
              <span class="ft-badge ft-chip-ok">En cours</span>
            </div>
            <h3 class="ft-title">Tournoi Express 5x5</h3>
            <ul class="ft-meta">
              <li>
                <svg class="ft-ic" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                Foot à 5
              </li>
              <li>
                <svg class="ft-ic" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                25-26 Octobre 2025
              </li>
              <li>
                <svg class="ft-ic" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                Arena Sport, Marseille
              </li>
              <li>
                <svg class="ft-ic" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                6/8 équipes inscrites
              </li>
            </ul>
          </div>
          <div class="ft-booking-actions">
            <div class="ft-price">800€</div>
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
      title: "Stade Municipal Centre-Ville",
      description: "Terrain de football à 5 situé au cœur de Paris. Équipement de qualité professionnelle avec vestiaires modernes et éclairage LED.",
      features: ["Vestiaires avec douches", "Éclairage LED", "Parking sécurisé", "Snack-bar"],
      price: "45€/heure",
      availability: "12 créneaux disponibles cette semaine"
    },
    2: {
      title: "Arena Sport Plus",
      description: "Terrain de football à 7 avec gazon naturel de haute qualité. Idéal pour les matchs amicaux et les entraînements.",
      features: ["Gazon naturel entretenu", "Tribunes de 200 places", "Vestiaires spacieux", "Bar"],
      price: "65€/heure",
      availability: "8 créneaux disponibles cette semaine"
    },
    3: {
      title: "Complex Sportif Bordeaux",
      description: "Grand terrain de football à 11 avec gazon hybride. Parfait pour les compétitions et tournois.",
      features: ["Gazon hybride professionnel", "Éclairage haute performance", "Vestiaires équipés", "Parking de 200 places"],
      price: "120€/heure",
      availability: "5 créneaux disponibles cette semaine"
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
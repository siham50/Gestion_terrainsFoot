<?php
// views/public/Home.php
$GLOBALS['contentDisplayed'] = true;
?>
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
            <label><input type="checkbox" value="Foot à 5"> Foot à 5 (5x5)</label>
            <label><input type="checkbox" value="Foot à 7"> Foot à 7 (7x7)</label>
            <label><input type="checkbox" value="Foot à 11"> Foot à 11 (11x11)</label>
          </div>
        </div>
        
        <div class="ft-filter-group">
          <h3>Type de gazon</h3>
          <div class="ft-checkbox-list">
            <label><input type="checkbox" value="Naturel"> Gazon naturel</label>
            <label><input type="checkbox" value="Synthétique"> Gazon synthétique</label>
            <label><input type="checkbox" value="Hybride"> Gazon hybride</label>
          </div>
        </div>
        
        <div class="ft-filter-actions">
          <button class="ft-btn ft-btn-secondary" id="resetFilters">Réinitialiser</button>
          <button class="ft-btn ft-btn-primary" id="applyFilters">Appliquer les filtres</button>
        </div>
      </div>
    </div>

    <!-- Section Statistiques -->
    <section class="ft-section">
      <h2 class="ft-section-title">Statistiques en temps réel</h2>
      <p class="ft-section-subtitle">Aperçu de l'activité des terrains</p>
      
      <div class="ft-stats">
        <div class="ft-card ft-stat">
          <div class="ft-stat-icon">
            <svg class="ft-ic" viewBox="0 0 24 24"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
          </div>
          <div class="ft-stat-body">
            <div class="ft-stat-value" id="totalTerrains">0</div>
            <div class="ft-stat-label">Terrains total</div>
            <div class="ft-stat-sub" id="terrainsUpdate">Mise à jour...</div>
          </div>
        </div>
        
        <div class="ft-card ft-stat">
          <div class="ft-stat-icon">
            <svg class="ft-ic" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
          </div>
          <div class="ft-stat-body">
            <div class="ft-stat-value" id="terrainsDisponibles">0</div>
            <div class="ft-stat-label">Terrains disponibles</div>
            <div class="ft-stat-sub" id="disponiblesUpdate">Mise à jour...</div>
          </div>
        </div>
        
        <div class="ft-card ft-stat">
          <div class="ft-stat-icon">
            <svg class="ft-ic" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
          </div>
          <div class="ft-stat-body">
            <div class="ft-stat-value" id="creneauxDisponibles">0</div>
            <div class="ft-stat-label">Créneaux disponibles</div>
            <div class="ft-stat-sub" id="creneauxUpdate">Mise à jour...</div>
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
      
      <div class="ft-list" id="terrains-disponibles-list">
        <!-- Les terrains disponibles seront chargés dynamiquement par AJAX -->
        <div class="ft-loading">Chargement des terrains disponibles...</div>
      </div>
    </section>

    <!-- Section Terrains indisponibles -->
    <section class="ft-section">
      <div class="ft-section-head">
        <h2 class="ft-section-title">Terrains indisponibles</h2>
        <p class="ft-section-subtitle">Terrains complets ou temporairement fermés</p>
      </div>
      
      <div class="ft-list" id="terrains-indisponibles-list">
        <!-- Les terrains indisponibles seront chargés dynamiquement par AJAX -->
        <div class="ft-loading">Chargement des terrains indisponibles...</div>
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
// Configuration AJAX - CHEMIN CORRECT
const AJAX_CONFIG = {
    updateInterval: 1000,
    controllerUrl: '/Gestion_terrainsFoot/index.php?action=get_terrains_data'  // CHEMIN COMPLET
};

// Données globales
let terrainsData = {
    disponibles: [],
    indisponibles: []
};

// Initialisation
document.addEventListener('DOMContentLoaded', function() {
    initEventListeners();
    console.log('Page chargée - Démarrage AJAX...');
    
    // Premier chargement immédiat
    loadTerrainsData();
});

// Écouteurs d'événements
function initEventListeners() {
    // Gestion des filtres
    const filterToggle = document.getElementById('filterToggle');
    if (filterToggle) {
        filterToggle.addEventListener('click', function() {
            const panel = document.getElementById('filtersPanel');
            panel.classList.toggle('ft-filters-open');
        });
    }

    // Application des filtres
    const applyFilters = document.getElementById('applyFilters');
    const resetFilters = document.getElementById('resetFilters');
    if (applyFilters) applyFilters.addEventListener('click', applyFiltersFunction);
    if (resetFilters) resetFilters.addEventListener('click', resetFiltersFunction);

    // Fermeture de la modal
    const modalClose = document.getElementById('modalClose');
    if (modalClose) {
        modalClose.addEventListener('click', function() {
            document.getElementById('terrainModal').classList.remove('ft-modal-open');
        });
    }

    // Fermer la modal en cliquant en dehors
    window.addEventListener('click', function(event) {
        const modal = document.getElementById('terrainModal');
        if (event.target === modal) {
            modal.classList.remove('ft-modal-open');
        }
    });
}

// Charger les données des terrains via AJAX - VERSION SIMPLIFIÉE
function loadTerrainsData() {
    console.log('Chargement des données...');
    
    const xhr = new XMLHttpRequest();
    xhr.open('GET', '/Gestion_terrainsFoot/index.php?action=get_terrains_data', true); // CHEMIN DIRECT
    
    xhr.onreadystatechange = function() {
        console.log('État XHR:', xhr.readyState, 'Statut:', xhr.status);
        
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                try {
                    console.log('Réponse reçue:', xhr.responseText.substring(0, 200));
                    const response = JSON.parse(xhr.responseText);
                    
                    if (response.success) {
                        console.log('Données chargées avec succès');
                        updateTerrainsUI(response.data);
                    } else {
                        console.error('Erreur serveur:', response.message);
                        showError('Erreur: ' + response.message);
                    }
                } catch (e) {
                    console.error('Erreur parsing JSON:', e, 'Réponse:', xhr.responseText);
                    showError('Erreur de format des données');
                }
            } else {
                console.error('Erreur HTTP:', xhr.status);
                showError('Erreur de connexion: ' + xhr.status);
            }
        }
    };
    
    xhr.onerror = function() {
        console.error('Erreur réseau');
        showError('Erreur réseau');
    };
    
    xhr.send();
}

// Afficher une erreur
function showError(message) {
    const containers = [
        'terrains-disponibles-list', 
        'terrains-indisponibles-list'
    ];
    
    containers.forEach(containerId => {
        const container = document.getElementById(containerId);
        if (container) {
            container.innerHTML = `<div class="ft-error" style="color: red; padding: 20px; text-align: center;">${message}</div>`;
        }
    });
}

// Mettre à jour l'interface utilisateur avec les données AJAX
function updateTerrainsUI(data) {
    console.log('Mise à jour UI - Disponibles:', data.disponibles?.length, 'Indisponibles:', data.indisponibles?.length);
    
    terrainsData = data;
    updateTerrainsList('disponibles', data.disponibles || []);
    updateTerrainsList('indisponibles', data.indisponibles || []);
    updateStats();
}

// Mettre à jour la liste des terrains
function updateTerrainsList(type, terrains) {
    const container = document.getElementById(`terrains-${type}-list`);
    
    if (!container) {
        console.error('Container non trouvé:', `terrains-${type}-list`);
        return;
    }
    
    console.log(`Mise à jour ${type}:`, terrains.length, 'terrains');
    
    if (terrains.length === 0) {
        container.innerHTML = '<div class="ft-no-data">Aucun terrain ' + type + '</div>';
        return;
    }
    
    let html = '';
    terrains.forEach(terrain => {
        const badges = getTerrainBadges(terrain);
        const creneauxText = type === 'disponibles' 
            ? `<p class="ft-muted"><strong>${terrain.creneaux_disponibles || 0} créneaux disponibles</strong></p>`
            : '<p class="ft-muted"><strong>Complet ou indisponible</strong></p>';
        
        html += `
            <div class="ft-card ft-booking" data-terrain-id="${terrain.idTerrain}">
                <div class="ft-booking-content">
                    <h3 class="ft-title">${terrain.nom}</h3>
                    <div class="ft-badges">
                        ${badges}
                    </div>
                    ${creneauxText}
                </div>
                <div class="ft-booking-actions">
                    <div class="ft-price">${terrain.prix_heure ? terrain.prix_heure + ' MAD/heure' : 'Prix non disponible'}</div>
                    <div>
                        <button class="ft-btn ft-btn-primary ft-details-btn" 
                                onclick="openTerrainModal(${terrain.idTerrain})">
                            Détails
                        </button>
                        ${type === 'disponibles' ? 
                            `<button class="ft-btn ft-btn-primary" onclick="reserverTerrain(${terrain.idTerrain})">
                                Réserver
                            </button>` : 
                            `<button class="ft-btn ft-btn-secondary" disabled>
                                Indisponible
                            </button>`
                        }
                    </div>
                </div>
            </div>
        `;
    });
    
    container.innerHTML = html;
    console.log(`Liste ${type} mise à jour avec ${terrains.length} terrains`);
}

// Mettre à jour les statistiques
function updateStats() {
    const total = (terrainsData.disponibles?.length || 0) + (terrainsData.indisponibles?.length || 0);
    const creneauxTotal = (terrainsData.disponibles || []).reduce((sum, terrain) => 
        sum + (parseInt(terrain.creneaux_disponibles) || 0), 0);
    
    // Mettre à jour les éléments DOM
    const totalElem = document.getElementById('totalTerrains');
    const dispoElem = document.getElementById('terrainsDisponibles');
    const creneauxElem = document.getElementById('creneauxDisponibles');
    
    if (totalElem) totalElem.textContent = total;
    if (dispoElem) dispoElem.textContent = terrainsData.disponibles?.length || 0;
    if (creneauxElem) creneauxElem.textContent = creneauxTotal;
    
    const now = new Date();
    const updateElem = document.getElementById('terrainsUpdate');
    if (updateElem) {
        updateElem.textContent = `Mis à jour: ${now.getHours()}:${now.getMinutes().toString().padStart(2, '0')}:${now.getSeconds().toString().padStart(2, '0')}`;
    }
    
    console.log('Stats mises à jour - Total:', total, 'Disponibles:', terrainsData.disponibles?.length, 'Créneaux:', creneauxTotal);
}

// Générer les badges du terrain
function getTerrainBadges(terrain) {
    return `
        <span class="ft-badge">${terrain.taille || 'Taille non spécifiée'}</span>
        <span class="ft-badge">${terrain.type || 'Type non spécifié'}</span>
        <span class="ft-badge ${terrain.disponible ? 'ft-badge-success' : 'ft-badge-danger'}">
            ${terrain.disponible ? 'Disponible' : 'Indisponible'}
        </span>
    `;
}

// Ouvrir la modal des détails
function openTerrainModal(terrainId) {
    const terrain = [...(terrainsData.disponibles || []), ...(terrainsData.indisponibles || [])]
        .find(t => t.idTerrain == terrainId);
    
    if (!terrain) {
        console.error('Terrain non trouvé:', terrainId);
        return;
    }
    
    const modal = document.getElementById('terrainModal');
    const modalTitle = document.getElementById('modalTitle');
    const modalBody = document.getElementById('modalBody');
    
    if (!modal || !modalTitle || !modalBody) {
        console.error('Éléments modal non trouvés');
        return;
    }
    
    modalTitle.textContent = terrain.nom;
    modalBody.innerHTML = `
        <p><strong>Description:</strong> Terrain de football ${terrain.taille} avec gazon ${terrain.type}.</p>
        <h3>Informations</h3>
        <ul>
            <li><strong>Taille:</strong> ${terrain.taille}</li>
            <li><strong>Type de gazon:</strong> ${terrain.type}</li>
            <li><strong>Statut:</strong> ${terrain.disponible ? 'Disponible' : 'Indisponible'}</li>
            <li><strong>Créneaux disponibles:</strong> ${terrain.creneaux_disponibles || 0}</li>
            <li><strong>Prix:</strong> ${terrain.prix_heure ? terrain.prix_heure + ' MAD/heure' : 'Non spécifié'}</li>
        </ul>
        <div class="ft-modal-actions">
            ${terrain.disponible && (terrain.creneaux_disponibles > 0) ? 
                `<button class="ft-btn ft-btn-primary" onclick="reserverTerrain(${terrain.idTerrain})">
                    Réserver maintenant
                </button>` : 
                `<button class="ft-btn ft-btn-secondary" disabled>
                    Indisponible pour réservation
                </button>`
            }
        </div>
    `;
    
    modal.classList.add('ft-modal-open');
}

// Réserver un terrain
function reserverTerrain(terrainId) {
    window.location.href = 'index.php?page=reservation&terrain=' + terrainId;
}

// Appliquer les filtres
function applyFiltersFunction() {
    const selectedSizes = Array.from(document.querySelectorAll('input[value^="Foot à"]:checked'))
        .map(cb => cb.value);
    const selectedTypes = Array.from(document.querySelectorAll('input[value$="l"]:checked'))
        .map(cb => cb.value);
    
    console.log('Filtres appliqués:', { selectedSizes, selectedTypes });
    // Implémentation avancée des filtres
}

// Réinitialiser les filtres
function resetFiltersFunction() {
    document.querySelectorAll('#filtersPanel input[type="checkbox"]').forEach(checkbox => {
        checkbox.checked = false;
    });
}

// Démarrer les mises à jour automatiques
setInterval(loadTerrainsData, AJAX_CONFIG.updateInterval);
</script>
</body>
</html>
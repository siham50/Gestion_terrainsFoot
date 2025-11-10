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
          <input type="text" id="searchInput" placeholder="Rechercher par nom, taille (5x5, 7x7, 11x11) ou type (naturel, synthétique, hybride)...">
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
            <label><input type="checkbox" name="size" value="Foot à 5"> Foot à 5 (5x5)</label>
            <label><input type="checkbox" name="size" value="Foot à 7"> Foot à 7 (7x7)</label>
            <label><input type="checkbox" name="size" value="Foot à 11"> Foot à 11 (11x11)</label>
          </div>
        </div>
        
        <div class="ft-filter-group">
          <h3>Type de gazon</h3>
          <div class="ft-checkbox-list">
            <label><input type="checkbox" name="type" value="Naturel"> Gazon naturel</label>
            <label><input type="checkbox" name="type" value="Synthétique"> Gazon synthétique</label>
            <label><input type="checkbox" name="type" value="Hybride"> Gazon hybride</label>
          </div>
        </div>

        <!-- Nouvelle section Créneaux horaires -->
        <div class="ft-filter-group">
          <h3>Créneaux horaires disponibles</h3>
          <div class="ft-checkbox-list ft-time-slots">
            <label><input type="checkbox" name="time" value="08:00-09:00"> 08:00 - 09:00</label>
            <label><input type="checkbox" name="time" value="09:00-10:00"> 09:00 - 10:00</label>
            <label><input type="checkbox" name="time" value="10:00-11:00"> 10:00 - 11:00</label>
            <label><input type="checkbox" name="time" value="11:00-12:00"> 11:00 - 12:00</label>
            <label><input type="checkbox" name="time" value="12:00-13:00"> 12:00 - 13:00</label>
            <label><input type="checkbox" name="time" value="13:00-14:00"> 13:00 - 14:00</label>
            <label><input type="checkbox" name="time" value="14:00-15:00"> 14:00 - 15:00</label>
            <label><input type="checkbox" name="time" value="15:00-16:00"> 15:00 - 16:00</label>
            <label><input type="checkbox" name="time" value="16:00-17:00"> 16:00 - 17:00</label>
            <label><input type="checkbox" name="time" value="17:00-18:00"> 17:00 - 18:00</label>
            <label><input type="checkbox" name="time" value="18:00-19:00"> 18:00 - 19:00</label>
            <label><input type="checkbox" name="time" value="19:00-20:00"> 19:00 - 20:00</label>
            <label><input type="checkbox" name="time" value="20:00-21:00"> 20:00 - 21:00</label>
            <label><input type="checkbox" name="time" value="21:00-22:00"> 21:00 - 22:00</label>
            <label><input type="checkbox" name="time" value="22:00-23:00"> 22:00 - 23:00</label>
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
          </div>
        </div>
        
        <div class="ft-card ft-stat">
          <div class="ft-stat-icon">
            <svg class="ft-ic" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
          </div>
          <div class="ft-stat-body">
            <div class="ft-stat-value" id="terrainsDisponibles">0</div>
            <div class="ft-stat-label">Terrains disponibles</div>
          </div>
        </div>
        
        <div class="ft-card ft-stat">
          <div class="ft-stat-icon">
            <svg class="ft-ic" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
          </div>
          <div class="ft-stat-body">
            <div class="ft-stat-value" id="creneauxDisponibles">0</div>
            <div class="ft-stat-label">Créneaux disponibles</div>
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
    updateInterval: 2000,  // Mise à jour toutes les 2 secondes
    controllerUrl: '/Gestion_terrainsFoot/controllers/TerrainController.php?action=get_terrains_data'  // CHEMIN COMPLET
};

// Données globales
let terrainsData = {
    disponibles: [],
    indisponibles: []
};

// Filtres actuels
let currentFilters = {
    search: '',
    sizes: [],
    types: [],
    times: []
};

// Mappings pour la recherche
const searchMappings = {
    // Taille du terrain
    '5x5': 'Foot à 5',
    '5': 'Foot à 5',
    'foot à 5': 'Foot à 5',
    'foot a 5': 'Foot à 5',
    
    '7x7': 'Foot à 7',
    '7': 'Foot à 7',
    'foot à 7': 'Foot à 7',
    'foot a 7': 'Foot à 7',
    
    '11x11': 'Foot à 11',
    '11': 'Foot à 11',
    'foot à 11': 'Foot à 11',
    'foot a 11': 'Foot à 11',
    
    // Type de gazon
    'naturel': 'Naturel',
    'gazon naturel': 'Naturel',
    'natural': 'Naturel',
    
    'synthétique': 'Synthétique',
    'synthétique': 'Synthétique',
    'synthétique': 'Synthétique',
    'gazon synthétique': 'Synthétique',
    'synthetic': 'Synthétique',
    
    'hybride': 'Hybride',
    'gazon hybride': 'Hybride',
    'hybrid': 'Hybride'
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

    // Recherche en temps réel
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                currentFilters.search = this.value.trim();
                applyFilters();
            }, 300); // Délai de 300ms après la fin de la frappe
        });

        // Recherche par Entrée
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                currentFilters.search = this.value.trim();
                applyFilters();
            }
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

// Charger les données des terrains via AJAX
function loadTerrainsData() {
    console.log('Chargement des données...');
    
    const xhr = new XMLHttpRequest();
    xhr.open('GET', '/Gestion_terrainsFoot/controllers/TerrainController.php?action=get_terrains_data', true);
    
    xhr.onreadystatechange = function() {
        console.log('État XHR:', xhr.readyState, 'Statut:', xhr.status);
        
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                try {
                    console.log('Réponse reçue:', xhr.responseText.substring(0, 200));
                    const response = JSON.parse(xhr.responseText);
                    
                    if (response.success) {
                        console.log('Données chargées avec succès');
                        terrainsData = response.data;
                        applyFilters(); // Appliquer les filtres après chargement
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

// Appliquer les filtres
function applyFilters() {
    console.log('Application des filtres:', currentFilters);
    
    const filteredDisponibles = filterTerrains(terrainsData.disponibles || []);
    const filteredIndisponibles = filterTerrains(terrainsData.indisponibles || []);
    
    updateTerrainsList('disponibles', filteredDisponibles);
    updateTerrainsList('indisponibles', filteredIndisponibles);
    updateStats(filteredDisponibles, filteredIndisponibles);
}

// Filtrer les terrains selon les critères
function filterTerrains(terrains) {
    return terrains.filter(terrain => {
        // Filtre de recherche texte
        if (currentFilters.search) {
            const searchTerm = currentFilters.search.toLowerCase();
            const normalizedSearch = searchTerm.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
            
            // Recherche dans le nom du terrain
            const nomMatch = terrain.nom && 
                terrain.nom.toLowerCase().includes(searchTerm);
            
            // Recherche dans la taille du terrain
            const tailleMatch = terrain.taille && 
                terrain.taille.toLowerCase().includes(searchTerm);
            
            // Recherche dans le type de gazon
            const typeMatch = terrain.type && 
                terrain.type.toLowerCase().includes(searchTerm);
            
            // Recherche intelligente avec mappings
            const mappedSearch = searchMappings[searchTerm.toLowerCase()];
            const mappedMatch = mappedSearch && (
                (terrain.taille && terrain.taille.toLowerCase().includes(mappedSearch.toLowerCase())) ||
                (terrain.type && terrain.type.toLowerCase().includes(mappedSearch.toLowerCase()))
            );

            // Recherche par formats courants
            const formatMatch = 
                (searchTerm === '5x5' && terrain.taille === 'Foot à 5') ||
                (searchTerm === '7x7' && terrain.taille === 'Foot à 7') ||
                (searchTerm === '11x11' && terrain.taille === 'Foot à 11') ||
                (searchTerm === '5' && terrain.taille === 'Foot à 5') ||
                (searchTerm === '7' && terrain.taille === 'Foot à 7') ||
                (searchTerm === '11' && terrain.taille === 'Foot à 11');

            if (!nomMatch && !tailleMatch && !typeMatch && !mappedMatch && !formatMatch) {
                return false;
            }
        }
        
        // Filtre par taille (cases à cocher)
        if (currentFilters.sizes.length > 0) {
            if (!currentFilters.sizes.includes(terrain.taille)) return false;
        }
        
        // Filtre par type de gazon (cases à cocher)
        if (currentFilters.types.length > 0) {
            if (!currentFilters.types.includes(terrain.type)) return false;
        }
        
        // Filtre par créneaux horaires
        if (currentFilters.times.length > 0) {
            if (!terrain.disponible || (terrain.creneaux_disponibles || 0) === 0) return false;
        }
        
        return true;
    });
}

// Mettre à jour l'interface utilisateur avec les données filtrées
function updateTerrainsList(type, terrains) {
    const container = document.getElementById(`terrains-${type}-list`);
    
    if (!container) {
        console.error('Container non trouvé:', `terrains-${type}-list`);
        return;
    }
    
    console.log(`Mise à jour ${type}:`, terrains.length, 'terrains');
    
    if (terrains.length === 0) {
        const searchMessage = currentFilters.search ? 
            `Aucun terrain ${type} correspondant à "${currentFilters.search}"` :
            `Aucun terrain ${type}`;
        container.innerHTML = `<div class="ft-no-data">${searchMessage}</div>`;
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
function updateStats(disponibles, indisponibles) {
    const total = (disponibles?.length || 0) + (indisponibles?.length || 0);
    const creneauxTotal = (disponibles || []).reduce((sum, terrain) => 
        sum + (parseInt(terrain.creneaux_disponibles) || 0), 0);
    
    // Mettre à jour les éléments DOM
    const totalElem = document.getElementById('totalTerrains');
    const dispoElem = document.getElementById('terrainsDisponibles');
    const creneauxElem = document.getElementById('creneauxDisponibles');
    
    if (totalElem) totalElem.textContent = total;
    if (dispoElem) dispoElem.textContent = disponibles?.length || 0;
    if (creneauxElem) creneauxElem.textContent = creneauxTotal;
    
    const now = new Date();
    const updateElem = document.getElementById('terrainsUpdate');
    if (updateElem) {
        updateElem.textContent = `Mis à jour: ${now.getHours()}:${now.getMinutes().toString().padStart(2, '0')}:${now.getSeconds().toString().padStart(2, '0')}`;
    }
    
    console.log('Stats mises à jour - Total:', total, 'Disponibles:', disponibles?.length, 'Créneaux:', creneauxTotal);
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
    window.location.href = 'ReservationForm.php?terrain=' + terrainId;
}

// Appliquer les filtres depuis le bouton
function applyFiltersFunction() {
    // Récupérer les valeurs des cases à cocher
    currentFilters.sizes = Array.from(document.querySelectorAll('input[name="size"]:checked'))
        .map(cb => cb.value);
    currentFilters.types = Array.from(document.querySelectorAll('input[name="type"]:checked'))
        .map(cb => cb.value);
    currentFilters.times = Array.from(document.querySelectorAll('input[name="time"]:checked'))
        .map(cb => cb.value);
    
    console.log('Filtres appliqués:', currentFilters);
    
    // Fermer le panneau des filtres
    document.getElementById('filtersPanel').classList.remove('ft-filters-open');
    
    // Appliquer les filtres
    applyFilters();
}

// Réinitialiser les filtres
function resetFiltersFunction() {
    // Réinitialiser les cases à cocher
    document.querySelectorAll('#filtersPanel input[type="checkbox"]').forEach(checkbox => {
        checkbox.checked = false;
    });
    
    // Réinitialiser la recherche
    document.getElementById('searchInput').value = '';
    
    // Réinitialiser les filtres en mémoire
    currentFilters = {
        search: '',
        sizes: [],
        types: [],
        times: []
    };
    
    // Fermer le panneau des filtres
    document.getElementById('filtersPanel').classList.remove('ft-filters-open');
    
    // Appliquer les filtres (qui seront vides)
    applyFilters();
    
    console.log('Filtres réinitialisés');
}

// Démarrer les mises à jour automatiques
setInterval(loadTerrainsData, AJAX_CONFIG.updateInterval);
</script>
</body>
</html>
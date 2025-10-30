<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FootTime - Réservation & Tournois</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="app-container">
        <!-- En-tête -->
        <header class="app-header">
            <div class="header-content">
                <div class="logo-section">
                    <h1 class="app-logo">FootTime</h1>
                    <p class="app-tagline">Réservation & Tournois</p>
                </div>
                <nav class="main-nav">
                    <ul>
                        <li><a href="#" class="nav-link">Mes réservations</a></li>
                        <li><a href="#" class="nav-link active">Tournois</a></li>
                        <li><a href="#" class="nav-link">Newsletter</a></li>
                    </ul>
                </nav>
            </div>
        </header>

        <!-- Contenu principal -->
        <main class="main-content">
            <!-- Section de bienvenue -->
            <section class="welcome-section">
                <h2 class="welcome-title">Bienvenue sur FootTime</h2>
                <p class="welcome-subtitle">Réservez vos terrains et participez aux tournois</p>
                
                <!-- Barre de recherche -->
                <div class="search-container">
                    <input type="text" class="search-input" placeholder="Rechercher un terrain, un tournoi...">
                    <button class="search-btn">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M21 21L16.514 16.506L21 21ZM19 10.5C19 15.194 15.194 19 10.5 19C5.806 19 2 15.194 2 10.5C2 5.806 5.806 2 10.5 2C15.194 2 19 5.806 19 10.5Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                </div>
            </section>

            <!-- Section des filtres -->
            <section class="filters-section">
                <h3 class="section-title">Filtres</h3>
                <div class="filters-container">
                    <div class="filter-item">
                        <div class="filter-value">24</div>
                        <div class="filter-label">Terrains disponibles</div>
                        <div class="filter-trend positive">+3 cette semaine</div>
                    </div>
                    <div class="filter-item">
                        <div class="filter-value">+12%</div>
                        <div class="filter-label">Réservations actives</div>
                        <div class="filter-trend">ce mois</div>
                    </div>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
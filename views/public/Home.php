<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FootTime - Réservation de terrains de foot</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
        }

        /* Background */
        .app-container {
            display: flex;
            min-height: 100vh;
            background: linear-gradient(135deg, #064e3b 0%, #06382a 50%, #024330 100%);
        }

        /* Glassmorphism utilities */
        .glass {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        /* Sidebar */
        .sidebar {
            width: 280px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(16px);
            border-right: 1px solid rgba(255, 255, 255, 0.2);
            display: flex;
            flex-direction: column;
        }

        .sidebar-header {
            padding: 24px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        .logo-container {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo-icon {
            background: rgba(9, 255, 173, 0.8);
            padding: 8px;
            border-radius: 8px;
            backdrop-filter: blur(4px);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }

        .logo-text {
            font-size: 20px;
            font-weight: 700;
            color: white;
        }

        .sidebar-content {
            padding: 16px;
        }

        .nav-label {
            font-size: 12px;
            font-weight: 600;
            color: rgb(167, 243, 208);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 12px;
            padding-left: 12px;
        }

        .nav-menu {
            list-style: none;
        }

        .nav-item {
            margin-bottom: 4px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
            color: rgb(236, 253, 245);
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .nav-link:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .nav-item.active .nav-link {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        /* Header */
        .header {
            position: sticky;
            top: 0;
            z-index: 40;
            background: rgba(6, 78, 59, 0.85);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(16, 185, 129, 0.2);
            padding: 16px 24px;
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .search-container {
            flex: 1;
            position: relative;
        }

        .search-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: rgb(167, 243, 208);
            pointer-events: none;
        }

        .search-input {
            width: 100%;
            padding: 10px 12px 10px 40px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            color: white;
            font-size: 14px;
            outline: none;
            transition: all 0.2s ease;
        }

        .search-input::placeholder {
            color: rgb(167, 243, 208);
        }

        .search-input:focus {
            border-color: rgb(52, 211, 153);
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2);
        }

        .filter-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 16px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            color: white;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .filter-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: rgb(52, 211, 153);
        }

        /* Content Wrapper */
        .content-wrapper {
            padding: 32px 24px;
            overflow-y: auto;
        }

        /* Section */
        .section {
            margin-bottom: 48px;
        }

        .section-header {
            margin-bottom: 24px;
        }

        .section-title {
            font-size: 28px;
            font-weight: 700;
            color: white;
            margin-bottom: 8px;
        }

        .section-description {
            font-size: 14px;
            color: rgb(167, 243, 208);
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            padding: 24px;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .stat-content {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
        }

        .stat-info {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .stat-label {
            font-size: 14px;
            color: rgb(167, 243, 208);
        }

        .stat-value {
            font-size: 32px;
            font-weight: 700;
            color: white;
        }

        .stat-trend {
            font-size: 13px;
        }

        .trend-up {
            color: rgb(110, 231, 183);
        }

        .trend-down {
            color: rgb(252, 165, 165);
        }

        .stat-icon {
            background: rgba(16, 185, 129, 0.2);
            padding: 12px;
            border-radius: 8px;
            color: rgb(167, 243, 208);
        }

        /* Cards Grid */
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 24px;
        }

        .field-card,
        .tournament-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .field-card:hover,
        .tournament-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
        }

        .card-image {
            position: relative;
            height: 192px;
            overflow: hidden;
        }

        .card-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .field-card:hover .card-image img,
        .tournament-card:hover .card-image img {
            transform: scale(1.1);
        }

        .card-badge {
            position: absolute;
            top: 12px;
            right: 12px;
            padding: 6px 12px;
            background: rgb(5, 150, 105);
            color: white;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
        }

        .card-prize {
            position: absolute;
            bottom: 12px;
            left: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(4px);
            border-radius: 20px;
            color: white;
            font-size: 13px;
            font-weight: 600;
        }

        .prize-icon {
            color: rgb(251, 191, 36);
        }

        .card-body {
            padding: 20px;
            background: rgba(6, 78, 59, 0.4);
            backdrop-filter: blur(4px);
        }

        .card-title {
            font-size: 18px;
            font-weight: 700;
            color: white;
            margin-bottom: 12px;
        }

        .card-info {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-top: 16px;
        }

        .card-info-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: rgb(167, 243, 208);
        }

        .info-icon {
            color: rgb(110, 231, 183);
        }

        .card-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 16px;
            margin-top: 16px;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
        }

        .card-price {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 16px;
            font-weight: 600;
            color: rgb(167, 243, 208);
        }

        .badge {
            display: inline-block;
            padding: 4px 12px;
            border: 1px solid rgb(52, 211, 153);
            color: rgb(110, 231, 183);
            border-radius: 6px;
            font-size: 12px;
            font-weight: 500;
            margin-bottom: 12px;
        }

        /* Badge status */
        .badge-open {
            background: rgb(5, 150, 105);
            border-color: rgb(5, 150, 105);
            color: white;
        }

        .badge-full {
            background: rgb(239, 68, 68);
            border-color: rgb(239, 68, 68);
            color: white;
        }

        .badge-ongoing {
            background: rgb(245, 158, 11);
            border-color: rgb(245, 158, 11);
            color: white;
        }

        /* Buttons */
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-primary {
            background: rgb(5, 150, 105);
            color: white;
        }

        .btn-primary:hover {
            background: rgb(4, 120, 87);
        }

        .btn-primary:disabled {
            background: rgb(107, 114, 128);
            cursor: not-allowed;
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        /* Dialog */
        .dialog-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(4px);
            z-index: 50;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .dialog-overlay.active {
            display: flex;
        }

        .dialog-content {
            width: 100%;
            max-width: 650px;
            max-height: 85vh;
            background: rgba(6, 78, 59, 0.95);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 16px;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .dialog-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            padding: 24px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        .dialog-title {
            font-size: 20px;
            font-weight: 700;
            color: white;
            margin-bottom: 4px;
        }

        .dialog-description {
            font-size: 14px;
            color: rgb(167, 243, 208);
        }

        .dialog-close {
            background: none;
            border: none;
            color: white;
            cursor: pointer;
            padding: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 4px;
            transition: background 0.2s ease;
        }

        .dialog-close:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .dialog-body {
            padding: 24px;
            overflow-y: auto;
            flex: 1;
        }

        .dialog-footer {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 12px;
            padding: 20px 24px;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
        }

        /* Filter Groups */
        .filter-group {
            margin-bottom: 32px;
        }

        .filter-label {
            font-size: 16px;
            font-weight: 600;
            color: white;
            margin-bottom: 16px;
        }

        .checkbox-group {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .checkbox-label {
            display: flex;
            align-items: center;
            gap: 12px;
            color: rgb(167, 243, 208);
            font-size: 14px;
            cursor: pointer;
            padding: 8px;
            border-radius: 6px;
            transition: background 0.2s ease;
        }

        .checkbox-label:hover {
            background: rgba(255, 255, 255, 0.05);
        }

        .checkbox-label input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: rgb(16, 185, 129);
        }

        /* Time Slots Grid */
        .time-slots-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
            gap: 12px;
        }

        .time-slot {
            padding: 12px 8px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            color: rgb(167, 243, 208);
            font-size: 13px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .time-slot:hover {
            background: rgba(255, 255, 255, 0.15);
            border-color: rgb(52, 211, 153);
        }

        .time-slot.selected {
            background: rgb(5, 150, 105);
            border-color: rgb(5, 150, 105);
            color: white;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 240px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .cards-grid {
                grid-template-columns: 1fr;
            }

            .time-slots-grid {
                grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
            }
        }

        @media (max-width: 640px) {
            .sidebar {
                display: none;
            }

            .content-wrapper {
                padding: 20px 16px;
            }

            .section-title {
                font-size: 22px;
            }
        }
    </style>
</head>
<body>
    <div class="app-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="logo-container">
                    <div class="logo-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"></path>
                            <path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"></path>
                            <path d="M4 22h16"></path>
                            <path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"></path>
                            <path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"></path>
                            <path d="M18 2H6v7a6 6 0 0 0 12 0V2Z"></path>
                        </svg>
                    </div>
                    <span class="logo-text">FootTime</span>
                </div>
            </div>
            <nav class="sidebar-content">
                <div class="nav-label">Menu Principal</div>
                <ul class="nav-menu">
                    <li class="nav-item active">
                        <a href="#" class="nav-link">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                <polyline points="9 22 9 12 15 12 15 22"></polyline>
                            </svg>
                            <span>Accueil</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect width="18" height="18" x="3" y="4" rx="2" ry="2"></rect>
                                <line x1="16" x2="16" y1="2" y2="6"></line>
                                <line x1="8" x2="8" y1="2" y2="6"></line>
                                <line x1="3" x2="21" y1="10" y2="10"></line>
                            </svg>
                            <span>Mes réservations</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"></path>
                                <path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"></path>
                                <path d="M4 22h16"></path>
                                <path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"></path>
                                <path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"></path>
                                <path d="M18 2H6v7a6 6 0 0 0 12 0V2Z"></path>
                            </svg>
                            <span>Tournois</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect width="20" height="16" x="2" y="4" rx="2"></rect>
                                <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path>
                            </svg>
                            <span>Newsletter</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            <header class="header">
                <div class="search-container">
                    <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <path d="m21 21-4.3-4.3"></path>
                    </svg>
                    <input type="text" id="searchInput" class="search-input" placeholder="Rechercher un terrain, une ville...">
                </div>
                <button class="filter-btn" onclick="openFilterDialog()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="4" x2="20" y1="21" y2="21"></line>
                        <line x1="4" x2="20" y1="14" y2="14"></line>
                        <line x1="4" x2="20" y1="7" y2="7"></line>
                        <line x1="14" x2="14" y1="3" y2="9"></line>
                        <line x1="8" x2="8" y1="12" y2="18"></line>
                        <line x1="16" x2="16" y1="16" y2="22"></line>
                    </svg>
                    <span>Filtres</span>
                </button>
            </header>

            <!-- Content -->
            <div class="content-wrapper">
                <!-- Statistics Section -->
                <section class="section">
                    <div class="section-header">
                        <h2 class="section-title">Statistiques</h2>
                        <p class="section-description">Aperçu de votre activité</p>
                    </div>
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-content">
                                <div class="stat-info">
                                    <p class="stat-label">Réservations totales</p>
                                    <p class="stat-value">24</p>
                                    <p class="stat-trend trend-up">+12% ce mois</p>
                                </div>
                                <div class="stat-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect width="18" height="18" x="3" y="4" rx="2" ry="2"></rect>
                                        <line x1="16" x2="16" y1="2" y2="6"></line>
                                        <line x1="8" x2="8" y1="2" y2="6"></line>
                                        <line x1="3" x2="21" y1="10" y2="10"></line>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-content">
                                <div class="stat-info">
                                    <p class="stat-label">Heures jouées</p>
                                    <p class="stat-value">48h</p>
                                    <p class="stat-trend trend-up">+5h cette semaine</p>
                                </div>
                                <div class="stat-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <polyline points="12 6 12 12 16 14"></polyline>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-content">
                                <div class="stat-info">
                                    <p class="stat-label">Tournois participés</p>
                                    <p class="stat-value">8</p>
                                    <p class="stat-trend trend-up">2 victoires</p>
                                </div>
                                <div class="stat-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"></path>
                                        <path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"></path>
                                        <path d="M4 22h16"></path>
                                        <path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"></path>
                                        <path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"></path>
                                        <path d="M18 2H6v7a6 6 0 0 0 12 0V2Z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Available Fields Section -->
                <section class="section">
                    <div class="section-header">
                        <h2 class="section-title">Terrains disponibles</h2>
                        <p class="section-description">Réservez un créneau horaire ou plusieurs jours pour un tournoi</p>
                    </div>
                    <div class="cards-grid" id="fieldsGrid">
                        <!-- Field cards will be dynamically generated -->
                    </div>
                </section>

                <!-- Tournaments Section -->
                <section class="section">
                    <div class="section-header">
                        <h2 class="section-title">Tournois à venir</h2>
                        <p class="section-description">Participez aux prochains tournois et compétitions</p>
                    </div>
                    <div class="cards-grid" id="tournamentsGrid">
                        <!-- Tournament cards will be dynamically generated -->
                    </div>
                </section>
            </div>
        </main>
    </div>

    <!-- Filter Dialog -->
    <div id="filterDialog" class="dialog-overlay" onclick="closeFilterDialog(event)">
        <div class="dialog-content" onclick="event.stopPropagation()">
            <div class="dialog-header">
                <div>
                    <h3 class="dialog-title">Filtres de recherche</h3>
                    <p class="dialog-description">Affinez votre recherche en sélectionnant les critères souhaités</p>
                </div>
                <button class="dialog-close" onclick="closeFilterDialog()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" x2="6" y1="6" y2="18"></line>
                        <line x1="6" x2="18" y1="6" y2="18"></line>
                    </svg>
                </button>
            </div>
            <div class="dialog-body">
                <!-- Field Type -->
                <div class="filter-group">
                    <h4 class="filter-label">Type de terrain</h4>
                    <div class="checkbox-group">
                        <label class="checkbox-label">
                            <input type="checkbox" value="5x5">
                            <span>Foot à 5 (5x5)</span>
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" value="7x7">
                            <span>Foot à 7 (7x7)</span>
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" value="11x11">
                            <span>Foot à 11 (11x11)</span>
                        </label>
                    </div>
                </div>

                <!-- Grass Type -->
                <div class="filter-group">
                    <h4 class="filter-label">Type de gazon</h4>
                    <div class="checkbox-group">
                        <label class="checkbox-label">
                            <input type="checkbox" value="naturel">
                            <span>Gazon naturel</span>
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" value="synthetique">
                            <span>Gazon synthétique</span>
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" value="hybride">
                            <span>Gazon hybride</span>
                        </label>
                    </div>
                </div>

                <!-- Time Slots -->
                <div class="filter-group">
                    <h4 class="filter-label">Créneaux horaires disponibles</h4>
                    <div class="time-slots-grid" id="timeSlotsGrid">
                        <!-- Time slots will be dynamically generated -->
                    </div>
                </div>
            </div>
            <div class="dialog-footer">
                <button class="btn btn-secondary" onclick="resetFilters()">Réinitialiser</button>
                <button class="btn btn-primary" onclick="applyFilters()">Appliquer les filtres</button>
            </div>
        </div>
    </div>

    <script>
        // Sample data
        const fieldsData = [
            {
                id: 1,
                name: "Stade Municipal Centre-Ville",
                location: "Paris 15ème",
                type: "Foot à 5",
                grassType: "Gazon synthétique",
                image: "https://images.unsplash.com/photo-1529900748604-07564a03e7a6?w=800&q=80",
                availableSlots: 12,
                pricePerHour: 45,
                capacity: 10
            },
            {
                id: 2,
                name: "Arena Sport Plus",
                location: "Lyon 3ème",
                type: "Foot à 7",
                grassType: "Gazon naturel",
                image: "https://images.unsplash.com/photo-1579952363873-27f3bade9f55?w=800&q=80",
                availableSlots: 8,
                pricePerHour: 65,
                capacity: 14
            },
            {
                id: 3,
                name: "Complex Sportif Bordeaux",
                location: "Bordeaux Centre",
                type: "Foot à 11",
                grassType: "Gazon hybride",
                image: "https://images.unsplash.com/photo-1551958219-acbc608c6377?w=800&q=80",
                availableSlots: 5,
                pricePerHour: 120,
                capacity: 22
            },
            {
                id: 4,
                name: "Terrain Express Marseille",
                location: "Marseille 8ème",
                type: "Foot à 5",
                grassType: "Gazon synthétique",
                image: "https://images.unsplash.com/photo-1560272564-c83b66b1ad12?w=800&q=80",
                availableSlots: 15,
                pricePerHour: 40,
                capacity: 10
            }
        ];

        const tournamentsData = [
            {
                id: 1,
                name: "Coupe d'Été 2025",
                type: "Foot à 7",
                date: "15-17 Juillet 2025",
                location: "Stade Jean Bouin, Paris",
                teams: 12,
                maxTeams: 16,
                prize: "1,500€",
                status: "open",
                image: "https://images.unsplash.com/photo-1489944440615-453fc2b6a9a9?w=800&q=80"
            },
            {
                id: 2,
                name: "Ligue des Champions Amateur",
                type: "Foot à 11",
                date: "2-4 Août 2025",
                location: "Parc OL, Lyon",
                teams: 8,
                maxTeams: 8,
                prize: "3,000€",
                status: "full",
                image: "https://images.unsplash.com/photo-1574629810360-7efbbe195018?w=800&q=80"
            },
            {
                id: 3,
                name: "Tournoi Express 5x5",
                type: "Foot à 5",
                date: "En cours",
                location: "Arena Sport, Marseille",
                teams: 6,
                maxTeams: 8,
                prize: "800€",
                status: "ongoing",
                image: "https://images.unsplash.com/photo-1522778119026-d647f0596c20?w=800&q=80"
            }
        ];

        // Generate time slots from 8:00 to 23:00
        function generateTimeSlots() {
            const slots = [];
            for (let hour = 8; hour < 23; hour++) {
                slots.push(`${hour.toString().padStart(2, '0')}:00 - ${(hour + 1).toString().padStart(2, '0')}:00`);
            }
            return slots;
        }

        // Render fields
        function renderFields(fields = fieldsData) {
            const grid = document.getElementById('fieldsGrid');
            grid.innerHTML = '';
            
            fields.forEach(field => {
                const card = document.createElement('div');
                card.className = 'field-card';
                card.innerHTML = `
                    <div class="card-image">
                        <img src="${field.image}" alt="${field.name}">
                        <div class="card-badge">${field.availableSlots} créneaux disponibles</div>
                    </div>
                    <div class="card-body">
                        <h3 class="card-title">${field.name}</h3>
                        <div class="card-info">
                            <div class="card-info-item">
                                <svg class="info-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"></path>
                                    <circle cx="12" cy="10" r="3"></circle>
                                </svg>
                                <span>${field.location}</span>
                            </div>
                            <div class="card-info-item">
                                <svg class="info-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                    <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                </svg>
                                <span>${field.type} • ${field.capacity} joueurs</span>
                            </div>
                            <div class="card-info-item">
                                <svg class="info-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect width="18" height="18" x="3" y="4" rx="2" ry="2"></rect>
                                    <line x1="16" x2="16" y1="2" y2="6"></line>
                                    <line x1="8" x2="8" y1="2" y2="6"></line>
                                    <line x1="3" x2="21" y1="10" y2="10"></line>
                                </svg>
                                <span>${field.grassType}</span>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="card-price">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <polyline points="12 6 12 12 16 14"></polyline>
                                </svg>
                                <span>${field.pricePerHour}€/heure</span>
                            </div>
                            <button class="btn btn-primary">Détails</button>
                        </div>
                    </div>
                `;
                grid.appendChild(card);
            });
        }

        // Render tournaments
        function renderTournaments(tournaments = tournamentsData) {
            const grid = document.getElementById('tournamentsGrid');
            grid.innerHTML = '';
            
            tournaments.forEach(tournament => {
                const card = document.createElement('div');
                card.className = 'tournament-card';
                
                let statusBadge = '';
                let statusClass = '';
                let statusText = '';
                
                if (tournament.status === 'open') {
                    statusClass = 'badge-open';
                    statusText = 'Inscriptions ouvertes';
                } else if (tournament.status === 'full') {
                    statusClass = 'badge-full';
                    statusText = 'Complet';
                } else if (tournament.status === 'ongoing') {
                    statusClass = 'badge-ongoing';
                    statusText = 'En cours';
                }
                
                card.innerHTML = `
                    <div class="card-image">
                        <img src="${tournament.image}" alt="${tournament.name}">
                        <div class="card-badge ${statusClass}">${statusText}</div>
                        <div class="card-prize">
                            <svg class="prize-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"></path>
                                <path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"></path>
                                <path d="M4 22h16"></path>
                                <path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"></path>
                                <path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"></path>
                                <path d="M18 2H6v7a6 6 0 0 0 12 0V2Z"></path>
                            </svg>
                            <span>${tournament.prize}</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <h3 class="card-title">${tournament.name}</h3>
                        <span class="badge">${tournament.type}</span>
                        <div class="card-info">
                            <div class="card-info-item">
                                <svg class="info-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect width="18" height="18" x="3" y="4" rx="2" ry="2"></rect>
                                    <line x1="16" x2="16" y1="2" y2="6"></line>
                                    <line x1="8" x2="8" y1="2" y2="6"></line>
                                    <line x1="3" x2="21" y1="10" y2="10"></line>
                                </svg>
                                <span>${tournament.date}</span>
                            </div>
                            <div class="card-info-item">
                                <svg class="info-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"></path>
                                    <circle cx="12" cy="10" r="3"></circle>
                                </svg>
                                <span>${tournament.location}</span>
                            </div>
                            <div class="card-info-item">
                                <svg class="info-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                    <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                </svg>
                                <span>${tournament.teams}/${tournament.maxTeams} équipes inscrites</span>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button class="btn btn-primary" ${tournament.status !== 'open' ? 'disabled' : ''}>
                                ${tournament.status === 'open' ? "S'inscrire" : "Voir les détails"}
                            </button>
                        </div>
                    </div>
                `;
                grid.appendChild(card);
            });
        }

        // Render time slots in filter dialog
        function renderTimeSlots() {
            const grid = document.getElementById('timeSlotsGrid');
            const slots = generateTimeSlots();
            
            slots.forEach(slot => {
                const button = document.createElement('button');
                button.className = 'time-slot';
                button.textContent = slot;
                button.onclick = () => toggleTimeSlot(button);
                grid.appendChild(button);
            });
        }

        // Toggle time slot selection
        function toggleTimeSlot(button) {
            button.classList.toggle('selected');
        }

        // Open filter dialog
        function openFilterDialog() {
            const dialog = document.getElementById('filterDialog');
            dialog.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        // Close filter dialog
        function closeFilterDialog(event) {
            if (event && event.target !== event.currentTarget) {
                return;
            }
            const dialog = document.getElementById('filterDialog');
            dialog.classList.remove('active');
            document.body.style.overflow = '';
        }

        // Reset filters
        function resetFilters() {
            // Reset checkboxes
            document.querySelectorAll('.dialog-body input[type="checkbox"]').forEach(checkbox => {
                checkbox.checked = false;
            });
            
            // Reset time slots
            document.querySelectorAll('.time-slot').forEach(slot => {
                slot.classList.remove('selected');
            });
        }

        // Apply filters
        function applyFilters() {
            // Get selected field types
            const selectedFieldTypes = [];
            document.querySelectorAll('.filter-group:nth-child(1) input[type="checkbox"]:checked').forEach(checkbox => {
                selectedFieldTypes.push(checkbox.value);
            });
            
            // Get selected grass types
            const selectedGrassTypes = [];
            document.querySelectorAll('.filter-group:nth-child(2) input[type="checkbox"]:checked').forEach(checkbox => {
                selectedGrassTypes.push(checkbox.value);
            });
            
            // Get selected time slots
            const selectedTimeSlots = [];
            document.querySelectorAll('.time-slot.selected').forEach(slot => {
                selectedTimeSlots.push(slot.textContent);
            });
            
            // Filter fields based on selections
            let filteredFields = fieldsData;
            
            if (selectedFieldTypes.length > 0) {
                filteredFields = filteredFields.filter(field => {
                    const fieldType = field.type.includes('5') ? '5x5' : 
                                    field.type.includes('7') ? '7x7' : '11x11';
                    return selectedFieldTypes.includes(fieldType);
                });
            }
            
            if (selectedGrassTypes.length > 0) {
                filteredFields = filteredFields.filter(field => {
                    const grassType = field.grassType.toLowerCase().includes('naturel') ? 'naturel' :
                                    field.grassType.toLowerCase().includes('synthétique') ? 'synthetique' : 'hybride';
                    return selectedGrassTypes.includes(grassType);
                });
            }
            
            // Render filtered fields
            renderFields(filteredFields);
            
            // Close dialog
            closeFilterDialog();
            
            // Show notification (optional)
            console.log('Filters applied:', {
                fieldTypes: selectedFieldTypes,
                grassTypes: selectedGrassTypes,
                timeSlots: selectedTimeSlots
            });
        }

        // Search functionality
        document.getElementById('searchInput').addEventListener('input', (e) => {
            const searchTerm = e.target.value.toLowerCase();
            
            const filteredFields = fieldsData.filter(field => 
                field.name.toLowerCase().includes(searchTerm) ||
                field.location.toLowerCase().includes(searchTerm) ||
                field.type.toLowerCase().includes(searchTerm)
            );
            
            const filteredTournaments = tournamentsData.filter(tournament =>
                tournament.name.toLowerCase().includes(searchTerm) ||
                tournament.location.toLowerCase().includes(searchTerm) ||
                tournament.type.toLowerCase().includes(searchTerm)
            );
            
            renderFields(filteredFields);
            renderTournaments(filteredTournaments);
        });

        // Close dialog on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeFilterDialog();
            }
        });

        // Initialize the app
        document.addEventListener('DOMContentLoaded', () => {
            renderFields();
            renderTournaments();
            renderTimeSlots();
        });
    </script>
</body>
</html>
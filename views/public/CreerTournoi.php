<?php
// views/public/CreerTournoi.php
$GLOBALS['contentDisplayed'] = true;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Afficher les messages de feedback
$tournoiFeedback = $_SESSION['tournoi_feedback'] ?? null;
if ($tournoiFeedback !== null) {
    unset($_SESSION['tournoi_feedback']);
}
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FootTime - Créer un Tournoi</title>
    <link rel="stylesheet" href="../../assets/css/Style.css">
    <style>
        .form-section {
            margin-bottom: 32px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--ft-text);
        }
        .form-input, .form-select {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--ft-border);
            border-radius: 8px;
            background: var(--ft-panel);
            color: var(--ft-text);
            font-size: 14px;
        }
        .form-input:focus, .form-select:focus {
            outline: none;
            border-color: var(--ft-accent);
            box-shadow: 0 0 0 3px rgba(43, 217, 151, 0.1);
        }
        .equipe-input-group {
            display: flex;
            gap: 8px;
            margin-bottom: 8px;
            align-items: center;
        }
        .equipe-input-group input {
            flex: 1;
        }
        .btn-remove-equipe {
            padding: 8px 12px;
            background: rgba(255, 0, 0, 0.1);
            color: #ff4444;
            border: 1px solid rgba(255, 0, 0, 0.3);
            border-radius: 6px;
            cursor: pointer;
        }
        .btn-remove-equipe:hover {
            background: rgba(255, 0, 0, 0.2);
        }
        .btn-add-equipe {
            padding: 8px 16px;
            background: rgba(43, 217, 151, 0.1);
            color: var(--ft-accent);
            border: 1px solid rgba(43, 217, 151, 0.3);
            border-radius: 6px;
            cursor: pointer;
            margin-top: 8px;
        }
        .btn-add-equipe:hover {
            background: rgba(43, 217, 151, 0.2);
        }
        .equipes-container {
            border: 1px solid var(--ft-border);
            border-radius: 8px;
            padding: 16px;
            background: var(--ft-panel);
        }
        .format-info {
            padding: 12px;
            background: rgba(43, 217, 151, 0.1);
            border: 1px solid rgba(43, 217, 151, 0.3);
            border-radius: 8px;
            margin-bottom: 16px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <?php require '../../includes/Navbar.php'; ?>
    <div class="ft-shell">
        <main class="ft-content" aria-label="Contenu principal">
            <section class="ft-page">
                <header class="ft-page-header">
                    <h1 class="ft-h1">Créer un Tournoi</h1>
                    <p class="ft-sub">Organisez votre tournoi de football</p>
                </header>

                <?php if ($tournoiFeedback): ?>
                    <div class="ft-alert <?php echo $tournoiFeedback['success'] ? 'ft-alert-success' : 'ft-alert-error'; ?>" style="padding: 16px; margin-bottom: 20px; border-radius: 12px; border: 1px solid <?php echo $tournoiFeedback['success'] ? '#1a6a58' : '#623b3b'; ?>; background: <?php echo $tournoiFeedback['success'] ? 'rgba(43,217,151,.12)' : 'rgba(255,0,0,.1)'; ?>;">
                        <div style="font-weight: 600; margin-bottom: 8px;"><?php echo htmlspecialchars($tournoiFeedback['message'], ENT_QUOTES, 'UTF-8'); ?></div>
                        <?php if (!$tournoiFeedback['success'] && !empty($tournoiFeedback['errors']) && is_array($tournoiFeedback['errors'])): ?>
                            <ul style="margin: 8px 0 0 20px; padding: 0;">
                                <?php foreach ($tournoiFeedback['errors'] as $error): ?>
                                    <li><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <form id="createTournoiForm" method="POST" action="../../controllers/TournoiController.php?action=create" class="ft-card" style="max-width: 800px; margin: 0 auto;">
                    <div class="form-section">
                        <h2 class="ft-h2" style="margin-bottom: 20px;">Informations du Tournoi</h2>
                        
                        <div class="form-group">
                            <label class="form-label" for="format">Format du Tournoi *</label>
                            <select class="form-select" id="format" name="format" required>
                                <option value="">Sélectionnez un format</option>
                                <option value="8 équipes">8 équipes</option>
                                <option value="16 équipes">16 équipes</option>
                            </select>
                            <div class="format-info" id="formatInfo" style="display: none;">
                                <strong>Format sélectionné:</strong> <span id="formatText"></span><br>
                                <span id="formatDescription"></span>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h2 class="ft-h2" style="margin-bottom: 20px;">Équipes</h2>
                        
                        <div class="equipes-container" id="equipesContainer">
                            <div id="equipesList">
                                <!-- Les équipes seront ajoutées dynamiquement -->
                            </div>
                            <button type="button" class="btn-add-equipe" id="addEquipeBtn">
                                + Ajouter une équipe
                            </button>
                        </div>
                    </div>

                    <div style="display: flex; gap: 12px; margin-top: 32px;">
                        <button type="submit" class="ft-btn ft-btn-primary" style="flex: 1;">
                            Créer le Tournoi
                        </button>
                        <a href="MesTournois.php" class="ft-btn ft-btn-secondary">
                            Annuler
                        </a>
                    </div>
                </form>
            </section>
        </main>
    </div>
    <?php require '../../includes/Footer.php'; ?>

    <script>
        (function() {
            const formatSelect = document.getElementById('format');
            const formatInfo = document.getElementById('formatInfo');
            const formatText = document.getElementById('formatText');
            const formatDescription = document.getElementById('formatDescription');
            const equipesList = document.getElementById('equipesList');
            const addEquipeBtn = document.getElementById('addEquipeBtn');
            const form = document.getElementById('createTournoiForm');
            
            let equipeCount = 0;
            let requiredEquipes = 0;

            // Gérer le changement de format
            formatSelect.addEventListener('change', function() {
                const format = this.value;
                if (format) {
                    const match = format.match(/(\d+)/);
                    requiredEquipes = match ? parseInt(match[1]) : 0;
                    
                    formatText.textContent = format;
                    formatDescription.textContent = `Vous devez ajouter exactement ${requiredEquipes} équipes.`;
                    formatInfo.style.display = 'block';
                    
                    // Réinitialiser la liste des équipes
                    equipesList.innerHTML = '';
                    equipeCount = 0;
                    
                    // Ajouter les équipes nécessaires
                    for (let i = 0; i < requiredEquipes; i++) {
                        addEquipeInput();
                    }
                } else {
                    formatInfo.style.display = 'none';
                    equipesList.innerHTML = '';
                    equipeCount = 0;
                }
            });

            // Ajouter une équipe
            function addEquipeInput() {
                if (equipeCount >= requiredEquipes && requiredEquipes > 0) {
                    return;
                }
                
                const div = document.createElement('div');
                div.className = 'equipe-input-group';
                div.innerHTML = `
                    <input type="text" 
                           name="equipes[]" 
                           class="form-input" 
                           placeholder="Nom de l'équipe" 
                           required
                           maxlength="100">
                    ${equipeCount >= requiredEquipes ? '<button type="button" class="btn-remove-equipe" onclick="this.parentElement.remove(); updateEquipeCount();">×</button>' : ''}
                `;
                
                equipesList.appendChild(div);
                equipeCount++;
            }

            // Mettre à jour le compteur
            window.updateEquipeCount = function() {
                equipeCount = equipesList.children.length;
            };

            // Bouton ajouter équipe
            addEquipeBtn.addEventListener('click', function() {
                if (equipeCount < requiredEquipes || requiredEquipes === 0) {
                    addEquipeInput();
                }
            });

            // Soumission du formulaire
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Vérifier le nombre d'équipes
                const equipes = Array.from(document.querySelectorAll('input[name="equipes[]"]'))
                    .map(input => input.value.trim())
                    .filter(val => val.length > 0);
                
                if (equipes.length !== requiredEquipes) {
                    alert(`Vous devez ajouter exactement ${requiredEquipes} équipes. Actuellement: ${equipes.length}`);
                    return;
                }
                
                // Vérifier les doublons
                const uniqueEquipes = new Set(equipes);
                if (uniqueEquipes.size !== equipes.length) {
                    alert('Les noms d\'équipes doivent être uniques.');
                    return;
                }
                
                // Soumettre via AJAX
                const formData = new FormData(form);
                
                fetch(form.action, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = 'MesTournois.php';
                    } else {
                        alert('Erreur: ' + (data.message || 'Erreur inconnue'));
                        if (data.errors) {
                            console.error('Erreurs:', data.errors);
                        }
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('Erreur lors de la création du tournoi');
                });
            });
        })();
    </script>
</body>
</html>







<?php
// views/errors/404.php
http_response_code(404);
$GLOBALS['contentDisplayed'] = true;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FootTime - Page non trouvée</title>
    <link rel="stylesheet" href="../../assets/css/Style.css">
    <style>
        .ft-error-page {
            max-width: 600px;
            margin: 0 auto;
            padding: 40px 20px;
            text-align: center;
        }
        
        .ft-error-page h1 {
            color: #dc3545;
            font-size: 4rem;
            margin-bottom: 20px;
            font-weight: bold;
        }
        
        .ft-error-page h2 {
            color: #333;
            font-size: 2rem;
            margin-bottom: 20px;
        }
        
        .ft-error-page p {
            color: #666;
            font-size: 1.1rem;
            margin-bottom: 30px;
            line-height: 1.6;
        }
        
        .ft-error-actions {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .ft-btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }
        
        .ft-btn-primary {
            background-color: #007bff;
            color: white;
        }
        
        .ft-btn-primary:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
        }
        
        .ft-btn-secondary {
            background-color: #6c757d;
            color: white;
        }
        
        .ft-btn-secondary:hover {
            background-color: #545b62;
            transform: translateY(-2px);
        }
        
        .ft-error-icon {
            font-size: 6rem;
            color: #dc3545;
            margin-bottom: 30px;
        }
        
        @media (max-width: 768px) {
            .ft-error-page h1 {
                font-size: 3rem;
            }
            
            .ft-error-page h2 {
                font-size: 1.5rem;
            }
            
            .ft-error-actions {
                flex-direction: column;
                align-items: center;
            }
            
            .ft-btn {
                width: 200px;
            }
        }
    </style>
</head>
<body>
    <?php 
    // Inclure la navbar sauf si on est déjà dans un contexte qui l'inclut
    if (!defined('NO_NAVBAR')): 
        require __DIR__ . '/../../includes/Navbar.php'; 
    endif; 
    ?>

    <main class="ft-shell">
        <div class="ft-content">
            <div class="ft-container">
                <div class="ft-error-page">
                    <div class="ft-error-icon">⚠️</div>
                    <h1>404</h1>
                    <p>Désolé, la page que vous recherchez n'existe pas ou a été déplacée.</p>
                    
                    <div class="ft-error-actions">
                        <a href="index.php?page=home" class="ft-btn ft-btn-primary">
                            Retour à l'accueil
                        </a>
                        <a href="javascript:history.back()" class="ft-btn ft-btn-secondary">
                            Retour en arrière
                        </a>
                    </div>
                    
                    <div style="margin-top: 40px; padding: 20px; background: #f8f9fa; border-radius: 8px;">
                        <p style="color: #666; font-size: 0.9rem; margin: 0;">
                            <strong>URL demandée :</strong><br>
                            <code style="background: #e9ecef; padding: 5px 10px; border-radius: 4px; word-break: break-all;">
                                <?php 
                                $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
                                $full_url = $protocol . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
                                echo htmlspecialchars($full_url);
                                ?>
                            </code>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php 
    // Inclure le footer sauf si on est déjà dans un contexte qui l'inclut
    if (!defined('NO_FOOTER')): 
        require __DIR__ . '/../../includes/Footer.php'; 
    endif; 
    ?>

    <script>
    // Script pour améliorer l'expérience utilisateur
    document.addEventListener('DOMContentLoaded', function() {
        // Ajouter un effet de fade-in
        const errorPage = document.querySelector('.ft-error-page');
        if (errorPage) {
            errorPage.style.opacity = '0';
            errorPage.style.transition = 'opacity 0.5s ease';
            
            setTimeout(() => {
                errorPage.style.opacity = '1';
            }, 100);
        }
        
        // Logger l'erreur 404 pour le débogage
        console.warn('Erreur 404 - Page non trouvée:', window.location.href);
    });
    </script>
</body>
</html>
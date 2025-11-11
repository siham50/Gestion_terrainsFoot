<?php
require_once '../../config/conn.php';
require_once '../../controllers/UtilisateurController.php';
require_once '../../config/recaptcha_config.php';

session_start();

$controller = new UtilisateurController($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CORRECTION : Pour reCAPTCHA v3, le token vient d'un champ caché, pas de 'g-recaptcha-response'
    $recaptchaToken = $_POST['recaptcha_token'] ?? ''; // Changé ici
    
    if (!$recaptchaToken) {
        $error = "Validation reCAPTCHA manquante.";
    } else {
        // CORRECTION : reCAPTCHA v3 nécessite une vérification différente
        $verifyResponse = file_get_contents(
            'https://www.google.com/recaptcha/api/siteverify?secret=' . 
            urlencode($recaptcha_secret_key) . 
            '&response=' . urlencode($recaptchaToken)
        );
        
        $captchaData = json_decode($verifyResponse, true);
        
        // CORRECTION : Vérifier le score pour reCAPTCHA v3 (0.5 est un bon seuil)
        if (!$captchaData || !$captchaData['success'] || $captchaData['score'] < 0.5) {
            $error = "Échec de la vérification de sécurité. Score: " . ($captchaData['score'] ?? 'N/A');
        } else {
            $email = mysqli_real_escape_string($conn, $_POST['username']);
            $password = $_POST['password'];
            
            $result = $controller->login($email, $password);
            
            if ($result['success']) {
                $user = $result['user'];
                $_SESSION["user_id"] = $user['idUtilisateur'];
                $_SESSION["user_role"] = $user['role'];
                $_SESSION["user_name"] = $user['prenom'] . " " . $user['nom'];
                
                if ($user['role'] === 'admin') {
                    header("Location: ../admin/index.php");
                } else {
                    header("Location: Home.php");
                }
                exit();
            } else {
                $error = $result['message'];
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- CORRECTION : Script reCAPTCHA v3 -->
    <script src="https://www.google.com/recaptcha/api.js?render=<?php echo $recaptcha_site_key; ?>"></script>
    <title>Foot Fields - Connexion</title>
    <style>
        /* Même style que register.php */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #032d2b;
            color: #ffffff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .container {
            width: 100%;
            max-width: 28rem;
        }

        .header {
            margin-bottom: 2rem;
            text-align: center;
        }

        h1 {
            font-size: 2.25rem;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 0.5rem;
        }

        .subtitle {
            font-size: 1rem;
            color: #9ca3af;
        }

        .card {
            background: #002b27;
            border-radius: 1rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 2rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        .card-header {
            margin-bottom: 1.5rem;
        }

        h2 {
            font-size: 1.5rem;
            font-weight: 600;
            color: #ffffff;
            margin-bottom: 0.5rem;
        }

        .card-description {
            font-size: 0.875rem;
            color: rgba(209, 213, 219, 0.8);
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        label {
            font-size: 0.875rem;
            font-weight: 500;
            color: #e5e7eb;
        }

        input {
            width: 100%;
            padding: 0.75rem;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 0.5rem;
            color: #ffffff;
            font-size: 0.875rem;
            transition: all 0.2s;
            outline: none;
        }

        input:focus {
            background: rgba(255, 255, 255, 0.15);
            border-color: #00c26e;
            box-shadow: 0 0 0 3px rgba(0, 194, 110, 0.3);
        }

        button[type="submit"] {
            width: 100%;
            padding: 0.875rem;
            background: #00c26e;
            border: none;
            border-radius: 0.5rem;
            color: white;
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            margin-top: 0.5rem;
        }

        button[type="submit"]:hover {
            background: #00a35c;
        }

        .login-link {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.875rem;
            color: rgba(209, 213, 219, 0.8);
        }

        .login-link a {
            color: #00c26e;
            text-decoration: none;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        .error {
            color: #ff6b6b;
            background: rgba(255, 107, 107, 0.1);
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
            text-align: center;
        }

        /* SUPPRIMER : les styles pour .g-recaptcha car reCAPTCHA v3 est invisible */
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Foot Fields</h1>
            <p class="subtitle">Connexion</p>
        </div>

        <div class="card">
            <div class="card-header">
                <h2>Connectez-vous</h2>
                <p class="card-description">Entrez vos identifiants pour accéder à votre compte.</p>
            </div>

            <?php if (isset($error)): ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endif; ?>

            <!-- CORRECTION : Ajout de l'ID pour le formulaire et du champ caché -->
            <form method="POST" action="" id="loginForm">
                <div class="form-group">
                    <label for="username">Email</label>
                    <input type="email" id="username" name="username" placeholder="votre@email.com" required>
                </div>

                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" placeholder="••••••••" required>
                </div>

                <!-- SUPPRIMER : le div g-recaptcha car reCAPTCHA v3 est invisible -->
                <!-- AJOUTER : champ caché pour le token reCAPTCHA -->
                <input type="hidden" name="recaptcha_token" id="recaptchaToken">

                <button type="submit" name="login">Se connecter</button>
            </form>

            <div class="login-link">
                <p><a href="forgot_password.php">Mot de passe oublié ?</a></p>
                <p>Vous n'avez pas de compte ? <a href="register.php">S'inscrire</a></p>
            </div>
        </div>
    </div>

    <!-- CORRECTION : Script JavaScript pour reCAPTCHA v3 -->
    <script>
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            grecaptcha.ready(function() {
                grecaptcha.execute('<?php echo $recaptcha_site_key; ?>', {action: 'login'}).then(function(token) {
                    // Mettre le token dans le champ caché
                    document.getElementById('recaptchaToken').value = token;
                    // Soumettre le formulaire
                    document.getElementById('loginForm').submit();
                });
            });
        });
    </script>
</body>
</html>
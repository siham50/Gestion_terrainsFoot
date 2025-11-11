<?php
require_once '../config/conn.php';
require_once '../controllers/UtilisateurController.php';

session_start();

$controller = new UtilisateurController($conn);
$message = "";
$valid_token = false;
$token = $_GET['token'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $controller->resetPassword($token, $_POST['password'], $_POST['confirm_password']);
    $message = $result['message'];
    $valid_token = !$result['success']; // Si réussi, token n'est plus valide
} else {
    // Vérifier si le token est valide pour l'affichage
    if (!empty($token) && isset($_SESSION['reset_tokens'][$token])) {
        $token_data = $_SESSION['reset_tokens'][$token];
        $valid_token = (time() <= $token_data['expires']);
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveau mot de passe - Foot Fields</title>
    <style>
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
            margin-bottom: 1.5rem;
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

        input::placeholder {
            color: #9ca3af;
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

        button[type="submit"]:disabled {
            background: #9ca3af;
            cursor: not-allowed;
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
            transition: color 0.2s;
        }

        .login-link a:hover {
            color: #00e37f;
            text-decoration: underline;
        }

        .success {
            color: #00c26e;
            background: rgba(0, 194, 110, 0.1);
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
            text-align: center;
        }

        .error {
            color: #ff6b6b;
            background: rgba(255, 107, 107, 0.1);
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
            text-align: center;
        }

        @media (max-width: 640px) {
            h1 {
                font-size: 1.875rem;
            }

            h2 {
                font-size: 1.25rem;
            }

            .card {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Foot Fields</h1>
            <p class="subtitle">Nouveau mot de passe</p>
        </div>

        <div class="card">
            <div class="card-header">
                <h2><?php echo $valid_token ? 'Créer un nouveau mot de passe' : 'Lien invalide'; ?></h2>
                <p class="card-description">
                    <?php echo $valid_token ? 'Entrez votre nouveau mot de passe ci-dessous.' : 'Le lien de réinitialisation est invalide ou a expiré.'; ?>
                </p>
            </div>

            <?php if (!empty($message)): ?>
                <div class="<?php echo strpos($message, '❌') !== false ? 'error' : 'success'; ?>"><?php echo $message; ?></div>
            <?php endif; ?>

            <?php if ($valid_token): ?>
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="password">Nouveau mot de passe</label>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            placeholder="••••••••"
                            required
                            minlength="8"
                        >
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">Confirmer le mot de passe</label>
                        <input 
                            type="password" 
                            id="confirm_password" 
                            name="confirm_password" 
                            placeholder="••••••••"
                            required
                            minlength="8"
                        >
                    </div>

                    <button type="submit">Réinitialiser le mot de passe</button>
                </form>
            <?php else: ?>
                <div class="login-link">
                    <p>
                        <a href="forgot_password.php">Demander un nouveau lien de réinitialisation</a>
                    </p>
                </div>
            <?php endif; ?>

            <div class="login-link">
                <p>
                    <a href="login.php">← Retour à la connexion</a>
                </p>
            </div>
        </div>
    </div>

    <script>
        // Validation côté client pour les mots de passe
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                const password = document.getElementById('password').value;
                const confirmPassword = document.getElementById('confirm_password').value;
                
                if (password !== confirmPassword) {
                    e.preventDefault();
                    alert('Les mots de passe ne correspondent pas.');
                    return false;
                }
                
                if (password.length < 8) {
                    e.preventDefault();
                    alert('Le mot de passe doit contenir au moins 8 caractères.');
                    return false;
                }
                
                return true;
            });
        }
    </script>
</body>
</html>
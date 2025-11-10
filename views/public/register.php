<?php
require_once '../../config/conn.php';
require_once '../../controllers/UtilisateurController.php';

session_start();

$controller = new UtilisateurController($conn);
$message = "";
$verification_sent = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['verification_code'])) {
        $result = $controller->verifyCode($_POST['verification_code']);
        $message = $result['message'];
        
        if ($result['success']) {
            $_SESSION["user_id"] = $result['user_id'];
            $_SESSION["user_role"] = 'client';
            $_SESSION["user_name"] = $result['user_name'];
            
            header("Location: login.php");
            exit();
        }
    } else {
        $result = $controller->register($_POST);
        $message = $result['message'];
        $verification_sent = $result['verification_sent'];
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Foot Fields - Inscription</title>
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
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        label {
            font-size: 0.875rem;
            font-weight: 500;
            color: #e5e7eb;
        }

        input, select {
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

        input:focus, select:focus {
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
            transition: color 0.2s;
        }

        .login-link a:hover {
            color: #00e37f;
            text-decoration: underline;
        }

        .resend-section {
            text-align: center;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .resend-btn {
            background: rgba(0, 194, 110, 0.2);
            border: 1px solid rgba(0, 194, 110, 0.3);
            color: #00c26e;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            cursor: pointer;
            transition: all 0.2s;
        }

        .resend-btn:hover {
            background: rgba(0, 194, 110, 0.3);
        }

        #verification_code {
            text-align: center;
            font-size: 1.5rem;
            letter-spacing: 0.5rem;
            font-weight: 600;
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
            .form-row {
                grid-template-columns: 1fr;
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
            <p class="subtitle">Inscription</p>
        </div>

        <div class="card">
            <div class="card-header">
                <h2><?php echo $verification_sent ? 'Vérifiez votre email' : 'Créez votre compte'; ?></h2>
                <p class="card-description">
                    <?php echo $verification_sent ? 'Un code de vérification a été envoyé à votre adresse email.' : 'Rejoignez notre communauté'; ?>
                </p>
            </div>

            <?php if (!empty($message)): ?>
                <div class="<?php echo strpos($message, '❌') !== false ? 'error' : 'success'; ?>"><?php echo $message; ?></div>
            <?php endif; ?>

            <?php if ($verification_sent): ?>
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="verification_code">Code de vérification</label>
                        <input 
                            type="text" 
                            id="verification_code" 
                            name="verification_code" 
                            placeholder="123456"
                            maxlength="6"
                            required
                            oninput="this.value = this.value.replace(/[^\d]/g, '')"
                        >
                    </div>
                    <button type="submit">Vérifier le code</button>
                </form>

                <div class="resend-section">
                    <p>Vous n'avez pas reçu le code ?</p>
                    <button type="button" id="resendCode" class="resend-btn">Renvoyer le code</button>
                    <div id="countdown" style="color: #ffa500; font-size: 0.875rem; margin-top: 0.5rem;">
                        Renvoyer disponible dans <span id="timer">60</span> seconde(s)
                    </div>
                </div>

                <script>
                    let resendCooldown = 60;
                    const resendBtn = document.getElementById('resendCode');
                    const timerElement = document.getElementById('timer');
                    
                    function updateResendButton() {
                        if (resendCooldown > 0) {
                            timerElement.textContent = resendCooldown;
                            resendBtn.disabled = true;
                            resendCooldown--;
                            setTimeout(updateResendButton, 1000);
                        } else {
                            resendBtn.disabled = false;
                            document.getElementById('countdown').style.display = 'none';
                        }
                    }
                    updateResendButton();
                    
                    resendBtn.addEventListener('click', function() {
                        if (resendCooldown <= 0) {
                            window.location.reload();
                        }
                    });
                </script>
            <?php else: ?>
                <form method="POST" action="">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="first_name">Prénom</label>
                            <input 
                                type="text" 
                                id="first_name" 
                                name="first_name" 
                                placeholder="Jean"
                                required
                                value="<?php echo isset($_POST['first_name']) ? htmlspecialchars($_POST['first_name']) : ''; ?>"
                            >
                        </div>
                        <div class="form-group">
                            <label for="last_name">Nom</label>
                            <input 
                                type="text" 
                                id="last_name" 
                                name="last_name" 
                                placeholder="Dupont"
                                required
                                value="<?php echo isset($_POST['last_name']) ? htmlspecialchars($_POST['last_name']) : ''; ?>"
                            >
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            placeholder="jean.dupont@example.com"
                            required
                            value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                        >
                    </div>

                    <div class="form-group">
                        <label for="phone">Téléphone (optionnel)</label>
                        <input 
                            type="tel" 
                            id="phone" 
                            name="phone" 
                            placeholder="+33 1 23 45 67 89"
                            value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>"
                        >
                    </div>

                    <div class="form-group">
                        <label for="adresse">Adresse (optionnel)</label>
                        <input 
                            type="text" 
                            id="adresse" 
                            name="adresse" 
                            placeholder="123 Rue Example, Ville"
                            value="<?php echo isset($_POST['adresse']) ? htmlspecialchars($_POST['adresse']) : ''; ?>"
                        >
                    </div>

                    <div class="form-group">
                        <label for="password">Mot de passe</label>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            placeholder="••••••••"
                            required
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
                        >
                    </div>

                    <button type="submit">Créer un compte</button>
                </form>

                <div class="login-link">
                    <p>Vous avez déjà un compte ? <a href="login.php">Se connecter</a></p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        const signupForm = document.querySelector('form');
        if (signupForm && !<?php echo $verification_sent ? 'true' : 'false'; ?>) {
            signupForm.addEventListener('submit', function(e) {
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

        const verificationForm = document.querySelector('form');
        if (verificationForm && <?php echo $verification_sent ? 'true' : 'false'; ?>) {
            const codeInput = document.getElementById('verification_code');
            codeInput.addEventListener('input', function() {
                this.value = this.value.replace(/[^\d]/g, '');
                if (this.value.length > 6) {
                    this.value = this.value.slice(0, 6);
                }
            });
        }
    </script>
</body>
</html>
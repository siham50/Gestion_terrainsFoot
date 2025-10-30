<?php
require __DIR__ . '/../../utils/Mailer.php';

include __DIR__ . '/../../config/conn.php';
include __DIR__ . '/../../config/mailer_config.php';
session_start();

$email = $_SESSION['email'] ?? '';

if (empty($email)) {
    header("Location: register.php");
    exit();
}

$message = '';

// Si un problème d'envoi a été stocké en session, l'afficher ici (non bloquant)
if (isset($_SESSION['mail_error'])) {
    $err = htmlspecialchars($_SESSION['mail_error']);
    $message .= "<div class='error'>❌ $err</div>";
    unset($_SESSION['mail_error']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérifier si c'est une demande de renvoi de code
    if (isset($_POST['resend_code'])) {
        // Vérifier si 30 secondes se sont écoulées
        $last_sent = $_SESSION['code_sent_time'] ?? 0;
        $current_time = time();
        
        if (($current_time - $last_sent) < 30) {
            $remaining = 30 - ($current_time - $last_sent);
            $message = "<div class='error'>⏰ Veuillez attendre $remaining seconde(s) avant de renvoyer le code.</div>";
        } else {
            // Générer un nouveau code
            $new_code = rand(100000, 999999);
            $expiration_time = date('Y-m-d H:i:s', strtotime('+5 minutes'));
            
            // Mettre à jour le code dans la base
            $update_code = "UPDATE utilisateur 
                           SET phone_verification_code = '$new_code', code_expires_at = '$expiration_time' 
                           WHERE email = '$email'";
            
            if (mysqli_query($conn, $update_code)) {
                // Envoyer le nouvel email via utilitaire Mailer
                $subject = 'Nouveau code de vérification - Nod Tasskhoun';
                $htmlBody = "<p>Votre nouveau code de vérification est :</p>
                             <p style='font-size:32px;font-weight:bold;color:#00c26e;letter-spacing:5px;'>$new_code</p>
                             <p style='color:#888;font-size:12px;'>Ce code expire dans 5 minutes.</p>";
                $altBody = "Votre nouveau code de vérification est : $new_code\n\nCe code expire dans 5 minutes.";

                $sendResult = Mailer::send($email, '', $subject, $htmlBody, $altBody);
                if ($sendResult['success']) {
                    $_SESSION['code_sent_time'] = time();
                    $message = "<div class='success'>✅ Nouveau code envoyé ! Vérifiez votre email.</div>";
                } else {
                    $message = "<div class='error'>❌ Erreur lors de l'envoi de l'email : " . htmlspecialchars($sendResult['error']) . "</div>";
                }
            } else {
                $message = "<div class='error'>❌ Erreur lors de la mise à jour du code.</div>";
            }
        }
    } else {
        // Vérification du code normal
        $code = isset($_POST['code']) ? mysqli_real_escape_string($conn, $_POST['code']) : '';

        if (empty($code)) {
            $message = "<div class='error'>❌ Veuillez entrer le code de vérification.</div>";
        } else {
            $sql = "SELECT * FROM utilisateur WHERE email='$email' AND phone_verification_code='$code' LIMIT 1";
            $result = mysqli_query($conn, $sql);

            if ($result && mysqli_num_rows($result) === 1) {
                $user = mysqli_fetch_assoc($result);
                
                // ✅ Vérifier si le code n'a pas expiré
                $current_time = date('Y-m-d H:i:s');
                $expiration_time = $user['code_expires_at'] ?? null;
                
                if ($expiration_time && $current_time > $expiration_time) {
                    $message = "<div class='error'>❌ Code expiré ! Veuillez demander un nouveau code.</div>";
                } else {
                    // ✅ Code correct et non expiré → mise à jour du statut
                    $update = "UPDATE utilisateur 
                               SET phone_verified = 1, phone_verification_code = NULL, code_expires_at = NULL 
                               WHERE email = '$email'";
                    if (mysqli_query($conn, $update)) {
                        unset($_SESSION['email']);
                        unset($_SESSION['code_sent_time']);
                        $message = "<div class='success'>✅ Email vérifié avec succès !<br>
                                    <a href='login.html'>Se connecter maintenant</a></div>";
                    } else {
                        $message = "<div class='error'>❌ Erreur lors de l'activation du compte.</div>";
                    }
                }
            } else {
                $message = "<div class='error'>❌ Code invalide. Essayez encore.</div>";
            }
        }
    }
}

// Récupérer l'email pour l'afficher
$user_email = $email;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Vérification de l'email</title>
    <style>
        body {
            background-color: #032d2b;
            font-family: 'Poppins', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: white;
        }
        .container {
            background: #002b27;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 0 15px rgba(0,0,0,0.3);
            width: 400px;
            text-align: center;
        }
        h2 {
            margin-bottom: 20px;
        }
        input {
            width: 90%;
            padding: 10px;
            border-radius: 8px;
            border: none;
            margin: 10px 0;
            font-size: 16px;
            text-align: center;
        }
        button {
            width: 100%;
            padding: 10px;
            background: #00c26e;
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 16px;
            cursor: pointer;
            margin-top: 10px;
        }
        button:hover {
            background: #00a35c;
        }
        .success {
            color: #00ff8f;
            background: rgba(0,255,143,0.1);
            padding: 10px;
            border-radius: 10px;
            margin-bottom: 15px;
        }
        .error {
            color: #ff6868;
            background: rgba(255,104,104,0.1);
            padding: 10px;
            border-radius: 10px;
            margin-bottom: 15px;
        }
        a {
            color: #00ff8f;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        .resend-btn {
            background: #ff6b35;
            font-size: 14px;
            padding: 8px 16px;
            width: auto;
            margin: 10px auto;
        }
        .resend-btn:hover {
            background: #e55a2b;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>📧 Vérifiez votre email</h2>
        <p>Un code a été envoyé à votre adresse<br><b><?= htmlspecialchars($user_email) ?></b></p>
        <p style="color: #ffa500; font-size: 14px;">⏰ Le code expire dans 5 minutes</p>
        
        <?php if (!empty($message)) echo $message; ?>

        <form method="POST" action="">
            <input type="text" name="code" placeholder="Entrez le code reçu (6 chiffres)" maxlength="6" required autofocus>
            <button type="submit">Vérifier</button>
        </form>
        
        <div id="resend-section" style="display: none; margin-top: 20px;">
            <p style="color: #aaa; font-size: 14px;">Vous n'avez pas reçu le code ?</p>
            <form method="POST" action="">
                <input type="hidden" name="resend_code" value="1">
                <button type="submit" class="resend-btn">
                    📧 Renvoyer le code
                </button>
            </form>
        </div>
        
        <div id="countdown" style="color: #ffa500; font-size: 14px; margin-top: 10px;">
            ⏰ Renvoyer disponible dans <span id="timer">30</span> seconde(s)
        </div>
    </div>

    <script>
        // Compte à rebours de 30 secondes
        let timeLeft = <?= isset($_SESSION['code_sent_time']) ? max(0, 30 - (time() - $_SESSION['code_sent_time'])) : 30; ?>;
        const timerElement = document.getElementById('timer');
        const resendSection = document.getElementById('resend-section');
        const countdownElement = document.getElementById('countdown');
        
        if (timeLeft <= 0) {
            resendSection.style.display = 'block';
            countdownElement.style.display = 'none';
        } else {
            const countdown = setInterval(() => {
                timeLeft--;
                timerElement.textContent = timeLeft;
                
                if (timeLeft <= 0) {
                    clearInterval(countdown);
                    resendSection.style.display = 'block';
                    countdownElement.style.display = 'none';
                }
            }, 1000);
        }
    </script>
</body>
</html>


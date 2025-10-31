<?php
include __DIR__ . '/../../config/conn.php';

$message = '';

$email = isset($_GET['email']) ? mysqli_real_escape_string($conn, $_GET['email']) : '';
$token = isset($_GET['token']) ? mysqli_real_escape_string($conn, $_GET['token']) : '';

if ($email && $token) {
    $sql = "SELECT * FROM utilisateur WHERE email='$email' AND phone_verification_code='$token' LIMIT 1";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);
        $current_time = date('Y-m-d H:i:s');
        $expiration_time = $user['code_expires_at'] ?? null;

        if ($expiration_time && $current_time > $expiration_time) {
            $message = "<div class='error'>❌ Lien expiré. Veuillez vous réinscrire.</div>";
        } else {
            $update = "UPDATE utilisateur 
                       SET phone_verified = 1, phone_verification_code = NULL, code_expires_at = NULL 
                       WHERE email = '$email'";
            if (mysqli_query($conn, $update)) {
                $message = "<div class='success'>✅ Email vérifié avec succès !<br><a href='login.html'>Se connecter</a></div>";
            } else {
                $message = "<div class='error'>❌ Erreur lors de l'activation du compte.</div>";
            }
        }
    } else {
        $message = "<div class='error'>❌ Lien invalide.</div>";
    }
} else {
    $message = "<div class='error'>❌ Paramètres manquants.</div>";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Vérification email</title>
    <style>
        body { background:#032d2b; color:#fff; font-family: 'Poppins', sans-serif; display:flex; justify-content:center; align-items:center; height:100vh; }
        .container { background:#002b27; padding:40px; border-radius:20px; width:420px; text-align:center; box-shadow:0 0 15px rgba(0,0,0,0.3); }
        .success { color:#00ff8f; background:rgba(0,255,143,0.1); padding:12px; border-radius:10px; margin-bottom:15px; }
        .error { color:#ff6868; background:rgba(255,104,104,0.1); padding:12px; border-radius:10px; margin-bottom:15px; }
        a { color:#00ff8f; text-decoration:none; }
        a:hover { text-decoration:underline; }
    </style>
    </head>
<body>
    <div class="container">
        <h2>✉️ Vérification de l'email</h2>
        <?php if (!empty($message)) echo $message; ?>
    </div>
</body>
</html>


<?php 
include 'conn.php';
include 'recaptcha_config.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['username']) || empty($_POST['password'])) {
        header("Location: login.html?error=" . urlencode("Veuillez remplir l'email et le mot de passe."));
        exit();
    }
    // Verify reCAPTCHA token
    $recaptchaToken = $_POST['g-recaptcha-response'] ?? '';
    if (!$recaptchaToken) {
        header("Location: login.html?error=" . urlencode("Validation reCAPTCHA manquante."));
        exit();
    }
    $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . urlencode($recaptcha_secret_key) . '&response=' . urlencode($recaptchaToken));
    $captchaData = json_decode($verifyResponse, true);
    if (!$captchaData || empty($captchaData['success'])) {
        header("Location: login.html?error=" . urlencode("reCAPTCHA invalide. Réessayez."));
        exit();
    }
   $email = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];
    try {
        $sql = "SELECT * FROM utilisateur WHERE email='$email' LIMIT 1";
        $result = mysqli_query($conn, $sql);
    } catch (Exception $e) {
        header("Location: login.html?error=" . urlencode("Erreur serveur."));
        exit();
    }
    if ($result && mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);
        if (!password_verify($password, $user['password_hash'])) {
            header("Location: login.html?error=" . urlencode("Identifiants incorrects."));
            exit();
        }
        if (isset($user['phone_verified']) && (int)$user['phone_verified'] !== 1) {
            header("Location: login.html?error=" . urlencode("Veuillez vérifier votre email avant de vous connecter."));
            exit();
        }
        $role = $user['role'] ?? 'client';
        if ($role === 'admin') {
            header("Location: admin_dashboard.php");
        } else {
            header("Location: user_dashboard.php");
        }
        exit();
    } else {
        header("Location: login.html?error=" . urlencode("Identifiants incorrects."));
        exit();
    }
}
// GET request -> afficher le formulaire
include 'login.html';
?>
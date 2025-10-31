<?php
require __DIR__ . '/../../utils/Mailer.php';

include __DIR__ . '/../../config/conn.php';
include __DIR__ . '/../../config/mailer_config.php';

$message = ""; // Variable pour stocker les messages

// Utiliser une détection POST plus robuste (évite dépendre du champ 'submit')
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ✅ 1. Vérification des champs
    if (empty($_POST['first_name']) || empty($_POST['last_name']) || empty($_POST['email']) || empty($_POST['password']) || empty($_POST['confirm_password'])) {
        $message = "<div style='color:red;text-align:center;'>⚠️ Tous les champs requis (téléphone optionnel).</div>";
    } else {
        $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';

        $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
        $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $phone = mysqli_real_escape_string($conn, $phone);
        $password = mysqli_real_escape_string($conn, $_POST['password']);
        $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);
        $role = 'client';

        // ✅ 2. Vérifie si les mots de passe correspondent
        if ($password !== $confirm_password) {
            $message = "<div style='color:red;text-align:center;'>❌ Les mots de passe ne correspondent pas.</div>";
        } else {
            // ✅ 3. Vérifie si l'email existe déjà
            $check = mysqli_query($conn, "SELECT * FROM utilisateur WHERE email='$email'");
            if (mysqli_num_rows($check) > 0) {
                $message = "<div style='color:red;text-align:center;'>⚠️ Cet email existe déjà.</div>";
            } else {
                // (Optionnel) Vérifier si le téléphone existe déjà
                if (!empty($phone)) {
                    $check_phone = mysqli_query($conn, "SELECT * FROM utilisateur WHERE telephone='$phone'");
                    if (mysqli_num_rows($check_phone) > 0) {
                        $message = "<div style='color:red;text-align:center;'>⚠️ Ce numéro de téléphone est déjà utilisé.</div>";
                    }
                }

                if (empty($message)) {
                    // ✅ 4. Générer un code de vérification à 6 chiffres avec expiration (5 minutes)
                    $code = rand(100000, 999999);
                    $expiration_time = date('Y-m-d H:i:s', strtotime('+5 minutes'));

                    // ✅ 5. Enregistrer l'utilisateur avec le code (réutilise les colonnes existantes)
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $sql = "INSERT INTO utilisateur (prenom, nom, email, telephone, password_hash, role, phone_verification_code, phone_verified, code_expires_at)
                            VALUES ('$first_name', '$last_name', '$email', '$phone', '$hashed_password', '$role', '$code', 0, '$expiration_time')";

                    if (mysqli_query($conn, $sql)) {
                        // L'utilisateur est créé en base — préparer la session et rediriger vers la page de vérification.
                        session_start();
                        $_SESSION['email'] = $email;

                        // ✅ 6. Envoyer l'email de vérification via utilitaire Mailer
                        $subject = 'Code de vérification - Nod Tasskhoun';
                        $htmlBody = "<p>Bonjour $first_name,</p>
                                     <p>Merci pour votre inscription sur Nod Tasskhoun.</p>
                                     <p>Votre code de vérification est :</p>
                                     <p style='font-size:32px;font-weight:bold;color:#00c26e;letter-spacing:5px;'>$code</p>
                                     <p style='color:#888;font-size:12px;'>Ce code expire dans 5 minutes.</p>
                                     <p style='color:#888;font-size:12px;'>Si vous n'avez pas demandé ce code, ignorez cet email.</p>";
                        $altBody = "Bonjour $first_name,\n\nVotre code de vérification est : $code\n\nCe code expire dans 5 minutes.";

                        $sendResult = Mailer::send($email, $first_name . ' ' . $last_name, $subject, $htmlBody, $altBody);
                        if (!$sendResult['success']) {
                            $_SESSION['mail_error'] = 'Erreur d\'envoi email: ' . $sendResult['error'];
                        }
                        // Rediriger vers la page de vérification du code (même en cas d'erreur d'envoi)
                        header("Location: verify_email_code.php");
                        exit();
                    } else {
                        $message = "<div style='color:red;text-align:center;'>Erreur SQL : " . mysqli_error($conn) . "</div>";
                    }
                }
            }
        }
    }
}

// Afficher le formulaire si la requête n'est pas un POST (GET)
// ou si un message d'erreur est présent (afin d'afficher l'erreur à l'utilisateur).
// NOTE: si la redirection vers verify_email_code.php a eu lieu, ce code ne sera pas exécuté.
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !empty($message)) {
    include __DIR__ . '/register.html';
}
?>


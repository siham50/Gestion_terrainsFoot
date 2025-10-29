<?php
require __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include 'conn.php';
include 'mailer_config.php';
include 'recaptcha_config.php';

$message = ""; // Variable pour stocker les messages

if (isset($_POST['submit'])) {

    // ✅ 1. Vérification des champs
    if (empty($_POST['first_name']) || empty($_POST['last_name']) || empty($_POST['email']) || empty($_POST['password']) || empty($_POST['confirm_password'])) {
        $message = "<div style='color:red;text-align:center;'>⚠️ Tous les champs requis (téléphone optionnel).</div>";
    } else {
        // reCAPTCHA server-side verification
        $recaptchaToken = $_POST['g-recaptcha-response'] ?? '';
        if (!$recaptchaToken) {
            $message = "<div style='color:red;text-align:center;'>❌ reCAPTCHA manquant.</div>";
        } else {
            $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . urlencode($recaptcha_secret_key) . '&response=' . urlencode($recaptchaToken));
            $captchaData = json_decode($verifyResponse, true);
            if (!$captchaData || empty($captchaData['success'])) {
                $message = "<div style='color:red;text-align:center;'>❌ reCAPTCHA invalide.</div>";
            }
        }
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
                        // ✅ 6. Envoyer l'email de vérification via PHPMailer
                        $mailer = new PHPMailer(true);
                        try {
                            $mailer->isSMTP();
                            $mailer->Host = $smtp_host;
                            $mailer->SMTPAuth = true;
                            $mailer->Username = $smtp_username;
                            $mailer->Password = $smtp_password;
                            $mailer->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                            $mailer->Port = $smtp_port;
                            $mailer->CharSet = 'UTF-8';

                            $mailer->setFrom($smtp_from_email, $smtp_from_name);
                            $mailer->addAddress($email, $first_name . ' ' . $last_name);

                            $mailer->isHTML(true);
                            $mailer->Subject = 'Code de vérification - Nod Tasskhoun';
                            $mailer->Body = "<p>Bonjour $first_name,</p>
                                             <p>Merci pour votre inscription sur Nod Tasskhoun.</p>
                                             <p>Votre code de vérification est :</p>
                                             <p style='font-size:32px;font-weight:bold;color:#00c26e;letter-spacing:5px;'>$code</p>
                                             <p style='color:#888;font-size:12px;'>Ce code expire dans 5 minutes.</p>
                                             <p style='color:#888;font-size:12px;'>Si vous n'avez pas demandé ce code, ignorez cet email.</p>";

                            $mailer->AltBody = "Bonjour $first_name,\n\nVotre code de vérification est : $code\n\nCe code expire dans 5 minutes.";

                            $mailer->send();

                            // Rediriger vers la page de vérification du code
                            session_start();
                            $_SESSION['email'] = $email;
                            header("Location: verify_email_code.php");
                            exit();
                        } catch (Exception $e) {
                            // En cas d'erreur, supprimer l'utilisateur créé et afficher erreur
                            mysqli_query($conn, "DELETE FROM utilisateur WHERE email='$email'");
                            $message = "<div style='background-color:#f8d7da;border:1px solid #f5c6cb;color:#721c24;padding:15px;border-radius:8px;margin:20px 0;text-align:center;'>
                                        <strong>❌ Erreur d'envoi email</strong><br>
                                        <small>" . htmlspecialchars($e->getMessage()) . "</small><br><br>
                                        <small>Vérifiez votre configuration SMTP dans mailer_config.php</small>
                                       </div>";
                        }
                    } else {
                        $message = "<div style='color:red;text-align:center;'>Erreur SQL : " . mysqli_error($conn) . "</div>";
                    }
                }
            }
        }
    }
}

// Afficher le formulaire si pas de soumission ou s'il y a un message d'erreur
if (!isset($_POST['submit']) || !empty($message)) {
    include 'register.html';
}
?>

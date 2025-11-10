<?php
require_once __DIR__ . '/../classes/Utilisateur.php';
require_once __DIR__ . '/../utils/Mailer.php';
require_once __DIR__ . '/../config/mailer_config.php';

class UtilisateurController {
    private $utilisateur;
    private $conn;
    
    public function __construct($db) {
        $this->conn = $db;
        $this->utilisateur = new Utilisateur($db);
    }
    
    public function register($data) {
        $response = ['success' => false, 'message' => '', 'verification_sent' => false];
        
        if (empty($data['first_name']) || empty($data['last_name']) || 
            empty($data['email']) || empty($data['password']) || empty($data['confirm_password'])) {
            $response['message'] = "⚠️ Tous les champs requis doivent être remplis.";
            return $response;
        }
        
        $prenom = trim($data['first_name']);
        $nom = trim($data['last_name']);
        $email = trim($data['email']);
        $telephone = isset($data['phone']) ? trim($data['phone']) : null;
        $adresse = isset($data['adresse']) ? trim($data['adresse']) : null;
        $password = $data['password'];
        $confirm_password = $data['confirm_password'];
        
        if ($password !== $confirm_password) {
            $response['message'] = "❌ Les mots de passe ne correspondent pas.";
            return $response;
        }
        
        if ($this->utilisateur->emailExists($email)) {
            $response['message'] = "⚠️ Cet email existe déjà.";
            return $response;
        }
        
        if (!empty($telephone) && $this->utilisateur->phoneExists($telephone)) {
            $response['message'] = "⚠️ Ce numéro de téléphone est déjà utilisé.";
            return $response;
        }
        
        $verificationCode = $this->generateVerificationCode();
        $codeExpiry = time() + (10 * 60);
        
        $_SESSION['verification_code'] = $verificationCode;
        $_SESSION['code_expiry'] = $codeExpiry;
        $_SESSION['temp_prenom'] = $prenom;
        $_SESSION['temp_nom'] = $nom;
        $_SESSION['temp_email'] = $email;
        $_SESSION['temp_telephone'] = $telephone;
        $_SESSION['temp_adresse'] = $adresse;
        $_SESSION['temp_mdp'] = password_hash($password, PASSWORD_DEFAULT);
        
        $sendResult = $this->sendVerificationEmail($email, $verificationCode, $prenom . ' ' . $nom);
        
        if ($sendResult['success']) {
            $response['success'] = true;
            $response['verification_sent'] = true;
            $response['message'] = "✅ Code de vérification envoyé avec succès.";
        } else {
            $response['message'] = "❌ Erreur lors de l'envoi de l'email de vérification.";
        }
        
        return $response;
    }
    
    public function verifyCode($code) {
        $response = ['success' => false, 'message' => ''];
        
        $storedCode = $_SESSION['verification_code'] ?? '';
        $codeExpiry = $_SESSION['code_expiry'] ?? 0;
        
        if ($code === $storedCode && time() < $codeExpiry) {
            $userData = [
                'prenom' => $_SESSION['temp_prenom'],
                'nom' => $_SESSION['temp_nom'],
                'email' => $_SESSION['temp_email'],
                'telephone' => $_SESSION['temp_telephone'] ?? null,
                'adresse' => $_SESSION['temp_adresse'] ?? null,
                'password' => $_SESSION['temp_mdp'],
                'role' => 'client',
                'etat' => 'actif'
            ];
            
            $user_id = $this->utilisateur->create($userData);
            
            if ($user_id) {
                unset($_SESSION['verification_code'], $_SESSION['code_expiry'], $_SESSION['temp_prenom'], 
                      $_SESSION['temp_nom'], $_SESSION['temp_email'], $_SESSION['temp_telephone'], 
                      $_SESSION['temp_adresse'], $_SESSION['temp_mdp']);
                
                $response['success'] = true;
                $response['message'] = "✅ Inscription réussie !";
                $response['user_id'] = $user_id;
                $response['user_name'] = $userData['prenom'] . " " . $userData['nom'];
            } else {
                $response['message'] = "❌ Erreur lors de l'inscription.";
            }
        } else {
            $response['message'] = "❌ Code de vérification invalide ou expiré.";
        }
        
        return $response;
    }
    
    public function login($email, $password) {
        $response = ['success' => false, 'message' => '', 'user' => null];
        
        $user = $this->utilisateur->validateLogin($email, $password);
        
        if ($user) {
            if (isset($user['etat']) && $user['etat'] !== 'actif') {
                $response['message'] = "❌ Votre compte est désactivé.";
                return $response;
            }
            
            $response['success'] = true;
            $response['user'] = $user;
            $response['message'] = "✅ Connexion réussie !";
        } else {
            $response['message'] = "❌ Identifiants incorrects.";
        }
        
        return $response;
    }
    
    public function forgotPassword($email) {
        $response = ['success' => false, 'message' => ''];
        
        $user = $this->utilisateur->getByEmail($email);
        
        if ($user) {
            $token = $this->generateResetToken();
            $expires = time() + (60 * 60);
            
            $_SESSION['reset_tokens'] = $_SESSION['reset_tokens'] ?? [];
            $_SESSION['reset_tokens'][$token] = [
                'user_id' => $user['idUtilisateur'],
                'email' => $email,
                'expires' => $expires
            ];
            
            $sendResult = $this->sendPasswordResetEmail($email, $token, $user['prenom'] . ' ' . $user['nom']);
            
            if ($sendResult['success']) {
                $response['success'] = true;
                $response['message'] = "✅ Un lien de réinitialisation a été envoyé à votre adresse email.";
            } else {
                $response['message'] = "❌ Erreur lors de l'envoi de l'email. Veuillez réessayer.";
            }
        } else {
            $response['message'] = "✅ Si votre email existe dans notre système, vous recevrez un lien de réinitialisation.";
        }
        
        return $response;
    }
    
    public function resetPassword($token, $password, $confirm_password) {
        $response = ['success' => false, 'message' => ''];
        
        if (isset($_SESSION['reset_tokens'])) {
            foreach ($_SESSION['reset_tokens'] as $t => $data) {
                if (time() > $data['expires']) {
                    unset($_SESSION['reset_tokens'][$t]);
                }
            }
        }
        
        if (!empty($token) && isset($_SESSION['reset_tokens'][$token])) {
            $token_data = $_SESSION['reset_tokens'][$token];
            
            if (time() <= $token_data['expires']) {
                if (empty($password) || empty($confirm_password)) {
                    $response['message'] = "⚠️ Veuillez remplir tous les champs.";
                    return $response;
                }
                
                if ($password !== $confirm_password) {
                    $response['message'] = "❌ Les mots de passe ne correspondent pas.";
                    return $response;
                }
                
                if (strlen($password) < 8) {
                    $response['message'] = "❌ Le mot de passe doit contenir au moins 8 caractères.";
                    return $response;
                }
                
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                if ($this->utilisateur->updatePassword($token_data['user_id'], $hashed_password)) {
                    unset($_SESSION['reset_tokens'][$token]);
                    $response['success'] = true;
                    $response['message'] = "✅ Votre mot de passe a été réinitialisé avec succès.";
                } else {
                    $response['message'] = "❌ Erreur lors de la réinitialisation. Veuillez réessayer.";
                }
            } else {
                unset($_SESSION['reset_tokens'][$token]);
                $response['message'] = "❌ Lien invalide ou expiré. Veuillez demander un nouveau lien.";
            }
        } else {
            $response['message'] = "❌ Lien de réinitialisation invalide ou expiré.";
        }
        
        return $response;
    }
    
    private function generateVerificationCode() {
        return str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
    }
    
    private function generateResetToken() {
        return bin2hex(random_bytes(32));
    }
    
    private function sendVerificationEmail($email, $code, $name) {
        $subject = 'Code de vérification - Foot Fields';
        $htmlBody = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <h2 style='color: #00c26e; text-align: center;'>Vérification de votre compte Foot Fields</h2>
            <p>Bonjour $name,</p>
            <p>Merci de vous être inscrit sur Foot Fields. Pour finaliser votre inscription, veuillez utiliser le code de vérification suivant :</p>
            <div style='background-color: #f3f4f6; padding: 20px; text-align: center; margin: 20px 0; border-radius: 8px;'>
                <h1 style='color: #1f2937; font-size: 32px; letter-spacing: 5px; margin: 0;'>$code</h1>
            </div>
            <p>Ce code est valide pendant 10 minutes. Si vous n'avez pas demandé cette inscription, vous pouvez ignorer cet email.</p>
            <p style='color: #6b7280; font-size: 14px; margin-top: 30px;'>Cordialement,<br>L'équipe Foot Fields</p>
        </div>";
        
        $altBody = "Bonjour $name,\n\nVotre code de vérification Foot Fields est : $code\n\nCe code est valide pendant 10 minutes.";
        
        return Mailer::send($email, $name, $subject, $htmlBody, $altBody);
    }
    
    private function sendPasswordResetEmail($email, $token, $name) {
        $resetLink = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/reset_password.php?token=" . $token;
        
        $subject = 'Réinitialisation de votre mot de passe - Foot Fields';
        $htmlBody = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <h2 style='color: #00c26e; text-align: center;'>Réinitialisation de mot de passe</h2>
            <p>Bonjour $name,</p>
            <p>Vous avez demandé la réinitialisation de votre mot de passe Foot Fields. Cliquez sur le bouton ci-dessous pour créer un nouveau mot de passe :</p>
            <div style='text-align: center; margin: 30px 0;'>
                <a href='$resetLink' style='background-color: #00c26e; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;'>
                    Réinitialiser mon mot de passe
                </a>
            </div>
            <p>Si le bouton ne fonctionne pas, vous pouvez copier-coller ce lien dans votre navigateur :</p>
            <p style='background-color: #f3f4f6; padding: 10px; border-radius: 5px; word-break: break-all;'>$resetLink</p>
            <p>Ce lien expirera dans 1 heure.</p>
            <p style='color: #888; font-size: 14px;'>Si vous n'avez pas demandé cette réinitialisation, veuillez ignorer cet email.</p>
            <p style='color: #6b7280; font-size: 14px; margin-top: 30px;'>Cordialement,<br>L'équipe Foot Fields</p>
        </div>";
        
        $altBody = "Bonjour $name,\n\nPour réinitialiser votre mot de passe Foot Fields, cliquez sur ce lien : $resetLink\n\nCe lien expirera dans 1 heure.\n\nSi vous n'avez pas demandé cette réinitialisation, ignorez cet email.";
        
        return Mailer::send($email, $name, $subject, $htmlBody, $altBody);
    }
}
?>
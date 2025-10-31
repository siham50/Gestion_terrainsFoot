<?php
// Lightweight mailer utility centralising PHPMailer setup for the project.

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Ensure Composer autoload is available when this helper is used from any script
require_once __DIR__ . '/../vendor/autoload.php';

// Load SMTP configuration variables ($smtp_host, $smtp_port, $smtp_username, $smtp_password, $smtp_from_email, $smtp_from_name)
require_once __DIR__ . '/../config/mailer_config.php';

class Mailer
{
    /**
     * Send an email using the project SMTP settings.
     *
     * @param string $toEmail Recipient email address
     * @param string $toName  Recipient name (optional)
     * @param string $subject Email subject
     * @param string $htmlBody HTML body
     * @param string $altBody  Plain-text alternative body (optional)
     * @return array { success: bool, error?: string }
     */
    public static function send(string $toEmail, string $toName, string $subject, string $htmlBody, string $altBody = ''): array
    {
        // Import SMTP globals from config
        global $smtp_host, $smtp_port, $smtp_username, $smtp_password, $smtp_from_email, $smtp_from_name;

        $mailer = new PHPMailer(true);
        try {
            $mailer->isSMTP();
            $mailer->Host       = $smtp_host;
            $mailer->SMTPAuth   = true;
            $mailer->Username   = $smtp_username;
            $mailer->Password   = $smtp_password;
            $mailer->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // 465
            $mailer->Port       = $smtp_port;
            $mailer->CharSet    = 'UTF-8';

            $mailer->setFrom($smtp_from_email ?: $smtp_username, $smtp_from_name ?: 'Mailer');
            $mailer->addAddress($toEmail, $toName);

            $mailer->isHTML(true);
            $mailer->Subject = $subject;
            $mailer->Body    = $htmlBody;
            $mailer->AltBody = $altBody ?: strip_tags($htmlBody);

            $mailer->send();
            return ['success' => true];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}

?>



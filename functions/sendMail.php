<?php
require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendMail($to, $subject, $message, $AltMssg)
{
    $host = $_ENV['MAIL_HOST'] ?? getenv('MAIL_HOST');
    $username = $_ENV['MAIL_USERNAME'] ?? getenv('MAIL_USERNAME');
    $password = $_ENV['MAIL_PASSWORD'] ?? getenv('MAIL_PASSWORD');
    $auth = $_ENV['SMTP_AUTH'] ?? getenv('SMTP_AUTH') ?? true;
    $secure = $_ENV['SMTP_SECURE'] ?? getenv('SMTP_SECURE') ?? 'tls';
    $port = $_ENV['MAIL_PORT'] ?? getenv('MAIL_PORT') ?? 587; // Default port for TLS is 587
    $app_name = $_ENV['APP_NAME'] ?? getenv('APP_NAME');

    if (!$host || !$username || !$password || !$port) {
        throw new Exception('Mail configuration is incomplete.');
    }

    if (empty($message) && empty($AltMssg)) {
        throw new Exception('Both email message and alt body are empty.');
    }


    $mail = new PHPMailer(true);

    // Server settings
    $mail->isSMTP();
    $mail->Host       = $host;
    $mail->SMTPAuth   = $auth;
    $mail->Username   = $username; // Your Gmail
    $mail->Password   = $password; // Your Gmail App Password
    $mail->SMTPSecure = $secure;
    $mail->Port       = $port;

    // Sender and recipient
    $mail->setFrom($username, $app_name);
    $mail->addAddress($to['email'], $to['name']); // Add a recipient

    // Content
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body    = $message ?: $AltMssg;
    $mail->AltBody = $AltMssg ?: strip_tags($message);

    $mail->send();
    error_log("Email sent successfully to " . $to['email']);
}

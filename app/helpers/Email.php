<?php
namespace App\Helpers;

use PHPMailer\PHPMailer\PHPMailer;
use App\Config\Env;

require_once __DIR__ . '/../../vendor/autoload.php';

class Email {

    public static function send($to, $subject, $body) {
        Env::load();

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = $_ENV['SMTP_HOST'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $_ENV['SMTP_USER'];
            $mail->Password   = $_ENV['SMTP_PASS'];
            $mail->SMTPSecure = 'tls';
            $mail->Port       = $_ENV['SMTP_PORT'];

            $mail->setFrom($_ENV['EMAIL_FROM'], 'ThreeKey');
            $mail->addAddress($to);

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $body;

            return $mail->send();

        } catch (\Exception $e) {
            return false;
        }
    }
}

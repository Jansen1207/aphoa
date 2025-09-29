<?php
require 'vendor/autoload.php'; 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $duesId = $_POST['duesId'] ?? '';
    function sendEmail($name, $email, $duesId) {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp-relay.brevo.com'; 
            $mail->SMTPAuth = true;
            $mail->Username = '6cac04001@smtp-brevo.com'; 
            $mail->Password = 'hnAf1mdrjBgz7UOK'; 
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            $mail->setFrom('6cac04001@smtp-brevo.com', 'APHOA'); 
            $mail->addAddress($email, $name); 
            $mail->isHTML(false);
            $mail->Subject = 'Gentle Reminder!';
            $mail->Body    = "Dear $name,\n\nThis is a reminder that you have unpaid dues on ANAK-PAWIS HOMEOWNERS' ASSOCIATION (APHOA), INC. Please send in your payment immediately to avoid any deactivation of the account.\n\nThank you.";
            $mail->send();
            return "Reminder sent to $name at $email.";
        } catch (Exception $e) {
            return "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }
    echo sendEmail($name, $email, $duesId);
    exit; 
}
?>
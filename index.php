<?php
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->SMTPDebug = 1; // Enable verbose debug output - set to 0 for no debug output
    $mail->isSMTP(); // Set mailer to use SMTP
    $mail->Host = 'smtp.gmail.com'; // Specify main and backup SMTP servers
    $mail->SMTPAuth = true; // Enable SMTP authentication
    $mail->Username = 'amalkorbi96@gmail.com'; // SMTP username
    $mail->Password = ''; // SMTP password
    $mail->SMTPSecure = 'ssl'; // Enable SSL encryption
    $mail->Port = 465; // TCP port to connect to

    // Recipients
    $mail->setFrom('amalkorbi96@gmail.com', 'Mailer');
    $mail->addAddress('amalkorbi96@gmail.com'); // Add a recipient

    // Content
    $mail->isHTML(true); // Set email format to HTML
    $mail->Subject = 'Subject';
    $mail->Body    = 'This is the HTML message body <b>in bold!</b>';
    $mail->AltBody = 'This is the plain text message body for non-HTML mail clients';

    $mail->send();
    echo 'Message has been sent';
} catch (Exception $e) {
    echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
}
?>

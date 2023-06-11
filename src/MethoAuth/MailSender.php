<?php

namespace Smartedutech\Authsecure\MethoAuth;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailSender {
    public function sendEmail($email, $subject, $body, $fromAddress, $fromName) {
        try {
            // Configuration de l'envoi de courriel avec PHPMailer
            $mail = new PHPMailer(true);
            $mail->SMTPDebug = 0;
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'amal.korbi@etudiant-isi.utm.tn';
            $mail->Password = 'Azerty*1234';
            $mail->SMTPSecure = 'ssl';
            $mail->Port = 465;
            $mail->setFrom($fromAddress, $fromName);
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;
            $mail->AltBody = 'Contenu du courriel en texte brut pour les clients de messagerie sans HTML';
    
            // Envoyer le courriel
            $mail->send();
            echo "Le courriel a été envoyé avec succès.";
        } catch (Exception $e) {
            echo 'Une erreur s\'est produite lors de l\'envoi du courriel. Erreur Mailer : ', $mail->ErrorInfo;
        }
    }
    
}

?>
<?php 

require_once 'vendor/autoload.php'; 

 $email = $_POST['email'];
function Envoyer_code_secret($email) {

    // Génère un code aléatoire à cinq chiffres
    $code = rand(10000, 99999);
    
    // Configure les paramètres du serveur SMTP pour l'envoi d'e-mails
    $transport = (new Swift_SmtpTransport('smtp.example.com', 587, 'tls'))
        ->setUsername('your-email@example.com')
        ->setPassword('your-email-password');

    // Crée l'instance du mailer SwiftMailer en utilisant le transport SMTP configuré
    $mailer = new Swift_Mailer($transport);

    // Crée le message e-mail
    $message = (new Swift_Message('Code de vérification à deux facteurs'))
        ->setFrom(['your-email@example.com' => 'Your Name'])
        ->setTo([$email])
        ->setBody("Votre code de vérification à deux facteurs est : $code");

    // Envoie le message e-mail
    $result = $mailer->send($message);

    // Retourne le code de vérification généré
    return $code;
}
?>
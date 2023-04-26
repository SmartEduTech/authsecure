<?php 

require_once 'vendor/autoload.php'; 

 $email = $_POST['email'];
function Envoyer_code_secret($email) {

    // Génère un code aléatoire à cinq chiffres
    $code_secret = rand(10000, 99999);
    
    // Configure les paramètres du serveur SMTP pour l'envoi d'e-mails
    $transport = (new Swift_SmtpTransport('UVT.com', 587, 'tls'))
        ->setUsername('UVT.com')
        ->setPassword('UVT-password');

    // Crée l'instance du mailer SwiftMailer en utilisant le transport SMTP configuré
    $mailer = new Swift_Mailer($transport);

    // Crée le message e-mail
    $message = (new Swift_Message('Code de vérification à deux facteurs'))
        ->setFrom(['your-email@example.com' => 'UVT'])
        ->setTo([$email])
        ->setBody("Votre code de vérification à deux facteurs est : $code_secret");

    // Envoie le message e-mail
    $result = $mailer->send($message);

    // Retourne le code de vérification généré
    return $code_secret;
}
function verifier_code_secret($email,$code_utilisateur) {
// Envoyer un code de vérification à deux facteurs à l'utilisateur
$code_secret = Envoyer_code_secret($email);

// Demander à l'utilisateur de saisir le code de vérification
$code_utilisateur = $_POST['code'];

// Vérifier si le code de vérification correspond à celui envoyé par e-mail
if ($code_utilisateur == $code_secret) {
    echo 'Code de vérification valide.';
} else {
    echo 'Code de vérification invalide.';
}
}

?>
<?php
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Smartedutech\Authsecure\methodeAuth\Auth2FA;

$mail = new PHPMailer(true);

// Initialisez l'objet Auth2FA avec les valeurs appropriées
$id_cle = '5D89GL';
$utilisateur = 'amal';
$code_secret = '123456';
$verif = false; // Ajoutez la valeur appropriée pour $verif
$auth2FA = new Auth2FA($id_cle, $utilisateur, $code_secret, $verif);

// Récupérez l'adresse e-mail de l'utilisateur à partir d'une source appropriée
$email = 'amal.korbi@etudiant-isi.utm.tn';

// Générez un mot de passe à inclure dans le corps de l'e-mail
$mot_de_passe = '';

// Générez l'URL à inclure dans le corps de l'e-mail
$URL = 'https://exemple.com/verification';

// Appelez la méthode envoyer_code_secret pour envoyer le code secret par e-mail
$auth2FA->envoyer_code_secret($email, $mot_de_passe, $URL);

// Vous pouvez également inclure ici un message pour indiquer que l'e-mail a été envoyé avec succès
echo 'Le courriel a été envoyé avec succès.';
try {
    // Server settings
    $mail->SMTPDebug = 0; // Enable verbose debug output - set to 0 for no debug output
    $mail->isSMTP(); // Set mailer to use SMTP
    $mail->Host = 'smtp.gmail.com'; // Specify main and backup SMTP servers
    $mail->SMTPAuth = true; // Enable SMTP authentication
    $mail->Username = "amal.korbi@etudiant-isi.utm.tn"; // SMTP username
    $mail->Password = ""; // SMTP password
    $mail->SMTPSecure = 'ssl'; // Enable SSL encryption
    $mail->Port = 465; // TCP port to connect to

    // Recipients
    $mail->setFrom('amal.korbi@etudiant-isi.utm.tn', 'Mailer');
    $mail->addAddress('amal.korbi@etudiant-isi.utm.tn'); // Add a recipient

    // Content
    $mail->isHTML(true); // Set email format to HTML
    $mail->Subject = 'Subject';
    $mail->Body    = 'This is the HTML message body <b>in bold!</b>.';
    $mail->AltBody = 'This is the plain text message body for non-HTML mail clients';

    $mail->send();
    echo 'Message has been sent';
} catch (Exception $e) {
    echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
}


?>

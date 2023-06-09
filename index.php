<?php
include "vendor/autoload.php";
use Smartedutech\Authsecure\Authentification\Authentification;
use Smartedutech\Authsecure\methodeAuth\Auth2FA;

$auth2facteur = new Auth2FA("", "", "");
$auth = new Authentification();
$subject = "Sujet de l'e-mail";
$headers = "From: amal.korbi@etudiant-isi.utm.tn\r\n" .
    "Reply-To: amal.korbi@etudiant-isi.utm.tn\r\n" .
    "X-Mailer: PHP/";

mail('amalkorbi96@gmail.com', $subject, "Contenu de l'e-mail", $headers);
?>

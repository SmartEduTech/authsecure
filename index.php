<?php
require 'vendor/autoload.php';
use Smartedutech\Authsecure\MethoAuth\Auth2F;
use Smartedutech\Authsecure\MethoAuth\ConfigAuth2F;

// Initialisez l'objet Auth2F avec les valeurs appropriées
$id_cle = '5D89GL';
$utilisateur = 'amal';
$code_secret = '123456';
$verif = false; // Ajoutez la valeur appropriée pour $verif
$auth2F = new Auth2F($id_cle, $utilisateur, $code_secret, $verif);

// Récupérez l'adresse e-mail de l'utilisateur à partir d'une source appropriée
$email = 'amal.korbi@etudiant-isi.utm.tn';

// Générez l'URL avec le code secret
$url = 'https://test.uvt.tn/candidature/public/admin/gestauth/login/token/';

// Appelez la méthode envoyer_code_secret pour envoyer le code secret par e-mail
$auth2F->envoyer_code_secret($email, $url);
?>

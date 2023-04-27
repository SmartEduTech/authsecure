<?php 
// Inclure SwiftMailer
require_once 'vendor/autoload.php'; 

class auth2FA extends Authentification {
  
  // Constructeur
  public function __construct($id_authentification, Utilisateur $id_utilisateur, $date_connexion, $adresse_ip) {
    parent::__construct($id_authentification, $id_utilisateur, $date_connexion, $adresse_ip);
  }

  // Fonction pour envoyer le code secret par email
  public function envoyer_code_secret($email) {
    // Générer le code secret
    $code_secret = rand(100000, 999999);

    // Configure les paramètres du serveur SMTP pour l'envoi d'e-mails
    $transport = (new Swift_SmtpTransport('UVT.com', 123, 'tls'))
        ->setUsername('UVT@example.com')
        ->setPassword('UVT-password');

    // Crée l'objet SwiftMailer avec le transport SMTP configuré
    $mailer = new Swift_Mailer($transport);

    // Crée l'objet message
    $message = (new Swift_Message('Code secret pour la vérification à deux facteurs'))
        ->setFrom(['UVT@example.com' => 'UVT'])
        ->setTo([$email])
        ->setBody("Votre code secret pour la vérification à deux facteurs est : " . $code_secret);

    // Envoie le message
    $result = $mailer->send($message);

    // Retourne le code secret généré
    return $code_secret;
  }

  // Fonction pour vérifier le code secret
  public function verifier_code_secret($code_secret) {
    // Récupérer le code secret stocké dans la session
    $code_secret_stocke = $_SESSION['code_secret'];

    // Vérifier si les codes secrets correspondent
    if ($code_secret == $code_secret_stocke) {
      // Si les codes correspondent, supprimer le code secret stocké dans la session et retourner vrai
      unset($_SESSION['code_secret']);
      return true;
    } else {
      // Si les codes ne correspondent pas, retourner faux
      return false;
    }
  }
}


?>
<?php
namespace Smartedutech\Authsecure\methodeAuth;
use Smartedutech\Authsecure\Authentification\iAuthentification;
use Firebase\JWT\JWT;

class Auth2FA {
  private $utilisateur;
  private $code_secret;
  private $id_cle;

  public function __construct($id_cle, $utilisateur, $code_secret) {
    $this->utilisateur = $utilisateur;
    $this->code_secret = $code_secret;
    $this->id_cle = $id_cle;
  }

  // Fonction pour envoyer le code secret par email
  public function envoyer_code_secret($email, $mot_de_passe, $URL) {


    // Configurer les destinataires
    $to = $email;

    // Configurer l'en-tête et le corps du courriel
    $subject = 'Sujet du courriel';
    $message = 'Contenu du courriel : ' . $mot_de_passe;
    $headers = "From: amal.korbi@etudiant-isi.utm.tn\r\n" .
               "Reply-To: amal.korbi@etudiant-isi.utm.tn\r\n" .
               "X-Mailer: PHP/" . phpversion();

    // Envoyer le courriel
    if (mail($to, $subject, $message, $headers)) {
      echo "Le courriel a été envoyé avec succès.";
    } else {
      echo "Une erreur s'est produite lors de l'envoi du courriel.";
    }
  }

  public function Verify() {
    $nbr_tentatives_echouees = 0;
    // Récupère la valeur du champ 'code_secret' depuis la méthode POST, 
    // ou met la variable $code à null si cette valeur n'existe pas
    $code = $_POST['code_secret'] ?? null;
    if ($code == $this->code_secret) {
      $verif = true;
      $nbr_tentatives_echouees = 0;
      $date_expiration_code = null;
      return true;
    } else {
      $verif = false;
      $nbr_tentatives_echouees += 1;
      if ($nbr_tentatives_echouees >= 3) {
        $date_expiration_code = date('Y-m-d H:i:s', strtotime('+5 minutes'));
      }
      return false;
    }
  }

  public function IsConnect() {
    $est_authentifier = true;
    // Vérifie si l'utilisateur est connecté
    if ($est_authentifier == true) {
      return true;
    }
    return false;
  }

  public function filterDataUser() {
    $utilisateur = new utilisateur;
    $email = $utilisateur->getEmail();
    $id_utilisateur = $utilisateur->getIdUtilisateur();
    $mot_de_passe = $utilisateur->getMotDePasse();

    define('FILTER_SANITIZE_BOOLEAN', 520);
    define('FILTER_SANITIZE_STRING', 513);

    // Vérification des données de l'utilisateur
    $id_utilisateur = filter_var($id_utilisateur, FILTER_SANITIZE_NUMBER_INT);
    $nom = filter_var($utilisateur->getNom(), FILTER_SANITIZE_STRING);
    $prenom = filter_var($utilisateur->getPrenom(), FILTER_SANITIZE_STRING);
    $email = filter_var($email, FILTER_SANITIZE_STRING, FILTER_VALIDATE_EMAIL);
    $mot_de_passe = filter_var($mot_de_passe, FILTER_SANITIZE_STRING);
  }

  public function getUserSession() {
    $id_cle = $this->id_cle;
    $donneesUser = array(
      'id' => $id_cle,
    );
    $donneesEncodees = JWT::encode($donneesUser, $id_cle, "HS256");
    return $donneesEncodees;
  }

  public function cryptInfoUser() {
    $utilisateur = new utilisateur;
    $mot_de_passe = $utilisateur->getMotDePasse();
    $cle_secrete = "exemple_de_cle_secrete";

    // Chiffrement des informations utilisateur
    $info_cryptee = openssl_encrypt($mot_de_passe, 'AES-128-ECB', $cle_secrete);
    return $info_cryptee;
  }

  public function decryptInfoUser() {
    $utilisateur = new utilisateur;
    $cle_secrete = "exemple_de_cle_secrete";

    // Décryptage des informations utilisateur
    $info_decryptee = openssl_decrypt($this->verif, 'AES-128-ECB', $cle_secrete);
    return $info_decryptee;
  }

  public function cookiesUserInfo() {
    // Récupération des informations utilisateur à partir des cookies
    if (isset($_COOKIE['info_utilisateur'])) {
      $info_utilisateur = $_COOKIE['info_utilisateur'];
      return $info_utilisateur;
    }
    return null;
  }

  public function restrection() {
    // Vérifie si l'utilisateur a accès à la ressource restreinte
    if ($this->verif) {
      return true;
    }
    return false;
  }

  public function blackIP() {
    // Vérifie si l'adresse IP de l'utilisateur est bloquée
    $adresse_ip = $_SERVER['REMOTE_ADDR'];
    $adresses_ip_bloquees = array('192.168.0.1', '10.0.0.1');
    if (in_array($adresse_ip, $adresses_ip_bloquees)) {
      return true;
    }
    return false;
  }

  public function sendInvitToRecover() {
    // Envoie une invitation à l'utilisateur pour récupérer son compte
    $email = 'exemple@example.com';
    $sujet = 'Invitation à récupérer le compte';
    $message = 'Bonjour, veuillez suivre le lien suivant pour récupérer votre compte : <a href="https://example.com/recover">https://example.com/recover</a>';
    mail($email, $sujet, $message);
  }

  public function genererURLRecoverIdentite() {
    // Génère une URL pour récupérer l'identité de l'utilisateur
    $id_utilisateur = $this->utilisateur->getIdUtilisateur();
    $cle_recover = 'exemple_de_cle_recover';
    $url = "https://example.com/recover?user=$id_utilisateur&key=$cle_recover";
    return $url;
  }

  public function sendRecoverIdentite() {
    // Envoie un courriel pour récupérer l'identité de l'utilisateur
    $email = 'exemple@example.com';
    $sujet = 'Récupération identité';
    $message = 'Bonjour, veuillez suivre le lien suivant pour récupérer votre identité : ' . $this->genererURLRecoverIdentite();
    mail($email, $sujet, $message);
  }
}
?>
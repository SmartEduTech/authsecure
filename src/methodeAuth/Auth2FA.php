<?php 
/**
 * Summary of Auth2FA
 */
class Auth2FA implements iAuthentification {
  private $utilisateur;
  private $code_secret;

  public function __construct($utilisateur, $code_secret) {
      $this->utilisateur = $utilisateur;
      $this->code_secret = $code_secret;
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

  public function Verify($code ){
    if($code == $this->code_secret){
        $verif = true;
        $nbr_tentatives_echouees = 0;
        $date_expiration_code = null;
        return true;
    }
    else{
        $verif = false;
        $nbr_tentatives_echouees += 1;
        if($nbr_tentatives_echouees >= 3){
            $date_expiration_code = date('Y-m-d H:i:s', strtotime('+5 minutes'));
        }
        return false;
    }
}


public function IsConnect($verif){
  if($verif){
      return true;
  }
  else{
      return false;
  }
}
public function filterDatautilisateur($utilisateur){
  // Vérification des données de l'utilisateur
  $id_utilisateur = filter_var($utilisateur->id_utilisateur, FILTER_SANITIZE_NUMBER_INT);
  $nom = filter_var($utilisateur->nom, FILTER_SANITIZE_STRING);
  $prenom = filter_var($utilisateur->prenom, FILTER_SANITIZE_STRING);
  $email = filter_var($utilisateur->email, FILTER_SANITIZE_EMAIL);
  $adress = filter_var($utilisateur->adress, FILTER_SANITIZE_STRING);
  $mot_de_passe = filter_var($utilisateur->mot_de_passe, FILTER_SANITIZE_STRING);
  $role = filter_var($utilisateur->role, FILTER_SANITIZE_STRING);
  $est_authentifier = filter_var($utilisateur->est_authentifier, FILTER_SANITIZE_BOOLEAN);
}

public function getutilisateurSession(){
  // Vérification de la session de l'utilisateur
  if(isset($_SESSION['utilisateur'])){
      $utilisateur = $_SESSION['utilisateur'];
      $utilisateur = $this->filterDatautilisateur($utilisateur);
      return $utilisateur;
  }
  else{
      return null;
  }
}

 public function getutilisateurInfo(){
    // Récupération des informations de l'utilisateur
    $utilisateur = $this->getutilisateurSession();
    if($utilisateur){
        $info = array(
            'id_utilisateur' => $utilisateur->id_utilisateur,
            'nom' => $utilisateur->nom,
            'prenom' => $utilisateur->prenom,
            'email' => $utilisateur->email,
            'role' => $utilisateur->role,
            'est_authentifier' => $utilisateur->est_authentifier
        );
        return $info;
    }
    else{
        return null;
    }
}

  public function cryptInfoutilisateur() {
      // Crypter les informations confidentielles de l'utilisateur
      $this->utilisateur->email = md5($this->utilisateur->email);
      $this->utilisateur->mot_de_passe = md5($this->utilisateur->mot_de_passe);
  }

  public function decryptInfoutilisateur() {
      // Décrypter les informations confidentielles de l'utilisateur
      $this->utilisateur->email = decrypt($this->utilisateur->email);
      $this->utilisateur->mot_de_passe = decrypt($this->utilisateur->mot_de_passe);
  }

  public function cookiesutilisateurInfo() {
      // Stocker des informations d'utilisateur dans un cookie
      setcookie('utilisateur_id', $this->utilisateur->id_utilisateur);
  }

  public function restriction() {
      // Vérifier que l'utilisateur a les autorisations nécessaires
      if (!$this->utilisateur->role->permissions->contains('permission1')) {
          return false;
      }
      return true;
  }

  public function blackIP() {
      // Vérifier que l'adresse IP de l'utilisateur n'est pas sur une liste noire
      $blacklist = ['127.0.0.1', '10.0.0.1'];
      if (in_array($_SERVER['REMOTE_ADDR'], $blacklist)) {
          return true;
      }
      return false;
  }


}
?>
<?php 

class Auth2FA implements iAuthentification{
  private $utilisateur;
  private $code_secret;
  private $id_cle;
 
  public function __construct($id_cle, $utilisateur, $code_secret) {
      $this->utilisateur = $utilisateur;
      $this->code_secret = $code_secret;
      $this->id_cle = $id_cle ;
     
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

  public function Verify(){
    $nbr_tentatives_echouees = 0;
    // Récupère la valeur du champ 'code_secret' depuis la méthode POST, 
    // ou met la variable $code à null si cette valeur n'existe pas
    $code = $_POST['code_secret'] ?? null;
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



public function IsConnect() {
    $est_authentifier = true;
    // Vérifie si l'utilisateur est connecté
    if ($est_authentifier == true) {
        return true;
    }
    return false;
}

public function filterDataUser(){
    $utilisateur= new utilisateur ;
    $email = $utilisateur->getEmail();
    $id_utilisateur = $utilisateur->getIdUtilisateur();
    $mot_de_passe = $utilisateur->getMotDePasse();
    
    define('FILTER_SANITIZE_BOOLEAN', 520);
    define('FILTER_SANITIZE_STRING', 513);

      // Vérification des données de l'utilisateur
      $id_utilisateur = filter_var($id_utilisateur, FILTER_SANITIZE_NUMBER_INT);
    $nom = filter_var($utilisateur->nom,FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $prenom = filter_var($utilisateur->prenom, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    $adress = filter_var($utilisateur->adress, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $mot_de_passe = filter_var($mot_de_passe, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $role = filter_var($utilisateur->role, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $est_authentifier = filter_var($utilisateur->est_authentifier, FILTER_SANITIZE_BOOLEAN);

    return (object) [
        'id_utilisateur' => $id_utilisateur,
        'nom' => $nom,
        'prenom' => $prenom,
        'email' => $email,
        'adress' => $adress,
        'mot_de_passe' => $mot_de_passe,
        'role' => $role,
        'est_authentifier' => $est_authentifier
    ];
}


public function getUserSession(){
  // Vérification de la session de l'utilisateur
  if(isset($_SESSION['utilisateur'])){
      $utilisateur = $_SESSION['utilisateur'];
      $utilisateur = $this->filterDataUser($utilisateur);
      return $utilisateur;
  }
  else{
      return null;
  }
}

 public function getUserInfo(){
    // Récupération des informations de l'utilisateur
    $utilisateur = $this->getUserSession();
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



  public function cryptInfoUser() {
    $utilisateur = $this->getUserSession();
    // Crypte les informations de l'utilisateur
    $info_crypt = openssl_encrypt(serialize($this->filterDataUser($utilisateur)), 'AES-128-ECB', $this->id_cle);
    return $info_crypt;
}


public function decryptInfoUser() {
    // Décrypte les informations de l'utilisateur
    $info_decrypt = openssl_decrypt($this->verif, 'AES-128-ECB', $this->id_cle);
    return unserialize($info_decrypt);
}

  public function cookiesUserInfo() {
      // Stocker des informations d'utilisateur dans un cookie
      setcookie('utilisateur_id', $this->utilisateur->id_utilisateur);
  }

  public function restrection() {
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
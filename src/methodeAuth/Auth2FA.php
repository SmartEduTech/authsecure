<?php 
require_once '/path/to/swiftmailer/lib/autoload.php';

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
public function envoyer_code_secret($email, $mot_de_passe, $URL) {
    // Define and initialize the variable $URL
    $URL = isset($URL) ? $URL : 'example.com';

    // Générer le code secret
    $code_secret = rand(100000, 999999);

    // Configure les paramètres du serveur SMTP pour l'envoi d'e-mails
    $transport = (new Swift_SmtpTransport($URL, 123, 'tls'))
        ->setUsername($email)
        ->setPassword($mot_de_passe);

    // Crée l'objet SwiftMailer avec le transport SMTP configuré
    $mailer = new Swift_Mailer($transport);

    // Crée l'objet message
    $message = (new Swift_Message('Code secret pour la vérification à deux facteurs'))
        ->setFrom([$URL => $URL])
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
      if (!$this->utilisateur->role->permissions->contains('adminastrateur')) {
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
  public function sendInvitToRecover(){
    
    $utilisateur = new Utilisateur;
    $email = $utilisateur->getEmail();
    $nom = $utilisateur->nom;
    $lien_reinitialisation ='www.exemple.com';

    // Générer un code unique pour la réinitialisation de mot de passe
    $code_reinitialisation = bin2hex(random_bytes(16));
    // Générer un code de réinitialisation unique
    $resetCode = bin2hex(random_bytes(16));
    $expiryDate = date('Y-m-d H:i:s', strtotime('+1 hour'));

    // Enregistrer le code de réinitialisation dans un fichier
    $filename = "reset-codes.txt";
    $data = array(
        'user_id' => 123, // l'ID de l'utilisateur dont le mot de passe doit être réinitialisé
        'reset_code' => $resetCode,
        'reset_expiry' => $expiryDate
    );
    $file = fopen($filename, 'a');
    fwrite($file, json_encode($data) . "\n");
    fclose($file);

    // Construire le corps du message
    $message = "Bonjour $nom,\n\n
                Vous avez demandé la réinitialisation de votre mot de passe.\n\n 
                Veuillez cliquer sur le lien ci-dessous pour créer un nouveau mot de passe :\n\n
                $lien_reinitialisation?code=$code_reinitialisation\n\n
                Si vous n'avez pas demandé la réinitialisation de votre mot de passe, veuillez ignorer ce message.\n\n
                Cordialement,\n";

    // En-têtes du message
    $headers = "From: VotreNom ". "\r\n" .
               "Reply-To: VotreNom ". "\r\n" .
               "Content-type: text/plain; charset=UTF-8.";

    // Envoyer le message
    $sujet = "Réinitialisation de mot de passe pour votre compte";
    if (mail($email, $sujet, $message, $headers)) {
        // Le message a été envoyé avec succès
        return true;
    } else {
        // Une erreur s'est produite lors de l'envoi du message
        return false;
    }
}
  
public function genererURLRecoverIdentite($id_utilisateur) {
    $timestamp = time();
    $hash = sha1($id_utilisateur . $timestamp . 'secret'); // Changez "secret" par une clé secrète appropriée
    $url = "https://example.com/recover?user_id=$id_utilisateur&timestamp=$timestamp&hash=$hash";
    return $url;
}

public function sendRecoverIdentite() {
       // Vérifier si l'utilisateur est autorisé à récupérer son identité avec une clé USB
       $utilisateur = $this->getUserSession();
       if (!$utilisateur) {
           return false; // L'utilisateur n'est pas connecté
       }
       
       // Récupérer l'adresse email de l'utilisateur
       $email = $utilisateur->getEmail();
       $mot_de_passe = $utilisateur->getMotDePasse();
       // Define and initialize the variable $URL
       $URL = isset($URL) ? $URL : 'example.com';


       // Génère un token de récupération d'identité
       $code_secret = $this->envoyer_code_secret($email,$mot_de_passe,$URL);

       // Envoyer un e-mail à l'utilisateur avec le code de récupération
       $destinataire = $email ;
       $sujet = "Récupération d'identité";
       $message = "Bonjour,\n\nVous avez demandé à récupérer votre identité sur notre site.\n\n
                   Voici votre code de récupération : ".$code_secret."\n\n
                   Pour poursuivre la récupération de votre identité, \n\n
                   veuillez suivre les instructions ou cliquer sur le lien fourni dans l'e-mail.\n\n
                   Cordialement";
       $headers = "From: " . "\r\n" .
                  "Reply-To: " . "\r\n" .
                  "X-Mailer: PHP/" . phpversion();

       if (mail($destinataire, $sujet, $message, $headers)) {
           return true; // L'e-mail a été envoyé avec succès
       } else {
           return false; // Une erreur est survenue lors de l'envoi de l'e-mail
       }
 
}
  public function verifyIdentite(){
   
        // vérification du code secret
        if ($this->code_secret != $_POST['code_secret']) {
            // le code secret est invalide, on utilise les fonctions de l'interface pour récupérer l'identité
            $this->sendInvitToRecover();
            $this->sendRecoverIdentite();
            $this->secureRecoverIdentite();
            return false;
        } else {
            // le code secret est valide, on continue le processus d'authentification
            return true;
        }
     
  }
  public function secureRecoverIdentite(){

        // Vérifier que l'utilisateur est bien authentifié et qu'il a un compte actif
        $utilisateur = $this->getUserSession();
        if (!$utilisateur || !$utilisateur->IsConnect()) {
            return false;
        }
    
        // Vérifier que l'utilisateur a fourni une adresse e-mail valide
        $email = isset($_POST['email']) ? $_POST['email'] : '';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }
    
        // Générer un code de vérification aléatoire et l'envoyer à l'utilisateur par e-mail
        $code = rand(100000, 999999);
        $message = "Votre code de vérification est : " . $code;
        mail($email, "Code de vérification", $message);
    
        // Stocker le code de vérification dans une variable de session pendant 5 minutes
        $_SESSION['code_verif'] = array(
            'utilisateur_id' => $utilisateur->id_utilisateur,
            'adresse_email' => $email,
            'code' => $code,
            'expire' => time() + 300 // 5 minutes
        );
    
        // Retourner true pour indiquer que le processus de récupération d'identité est sécurisé
        return true;
  
  }
  
}
?>
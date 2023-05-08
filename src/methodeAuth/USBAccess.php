<?php
class USBAccess implements iAuthentification, iIdentiteRecover {
    private $id_cle;
   
    private $date_expiration_cle;
    private $token;
    private $role;
    
    public function setRole($role){
    $this->role = $role;
    }

    function detecterCleUSB() {
        // Obtenir la liste des lecteurs connectés à l'ordinateur en utilisant  la commande wmic
        $output = shell_exec('wmic logicaldisk where drivetype=2 get deviceid');
        // La sortie de wmic est une chaîne de caractères donc l'utilisation de la fonction explode sert à séparer la chaîne en un tableau de lignes.
        $disques = explode("\r\r\n", trim($output));
        // Crée un tableau vide qui contiendra les noms des clés USB détectées.
        $cles_usb = array();
    
        foreach ($disques as $disque) {
        // Vérifie si la ligne correspond à un nom de lecteur
            if (preg_match('/^([A-Z]:)$/', $disque, $matches)) {
                $cles_usb[] = $matches[1];
            }
        }
    
        return $cles_usb;
    }
    // Generate a new token for USB key authentication
    public function generateToken( $id_cle,$id_utilisateur) {
        // Generate a random string for the token
        $random_string = bin2hex(random_bytes(32));
        
        // Calculate the expiration time of the token
        $date_expiration_clestamp = time() + $this->date_expiration_cle;
        
        // Construct the token string in the format of "id_cle|id_utilisateur|date_expiration_clestamp|random_string"
        $token_string = $id_cle . '|' . $id_utilisateur . '|' . $date_expiration_clestamp . '|' . $random_string;
        
        // Encrypt the token string using a secret key and AES-256 encryption algorithm
        $secret_key = 'my_secret_key';
        $encrypted_token = openssl_encrypt($token_string, 'AES-256-CBC', $secret_key, 0, 'my_init_vector');
        
        // Set the token attribute to the encrypted token
        $this->token = $encrypted_token;
        
        // Return the encrypted token
        return $encrypted_token;
    }
  
    public function Verify() {
        $nbr_tentatives_echouees = 0;
       
        // Vérifier si une clé USB est détectée
        $cles_usb = $this->detecterCleUSB();
        if (empty($cles_usb)) {
            return false; // Aucune clé USB détectée
        }
    
        // Vérifier si l'utilisateur a fourni un jeton valide
        if (!isset($_POST['usb_token']) || empty($_POST['usb_token'])) {
            return false; // Le jeton n'a pas été fourni ou est vide
        }
    
        $token = $_POST['usb_token'];
        $utilisateur = $this->getUserSession();
        if (!$utilisateur) {
            return false; // L'utilisateur n'est pas connecté
        }
    
        // Vérifier si l'utilisateur est autorisé à utiliser un jeton USB
        if (!$this->restrection('USB',$utilisateur)) {
            return false;
        }
    
        // Vérifier si le jeton est expiré
        if (time() > $this->date_expiration_cle) {
            return false; // Le jeton a expiré
        }
    
        // Vérifier si le nombre de tentatives a été dépassé
        if ($nbr_tentatives_echouees >= 3) {
            return false; // Le nombre de tentatives a été dépassé
        }
    
        // Vérifier si le jeton est valide
        if ($this->id_cle !== $token) {
            // Le jeton est invalide, incrémenter le nombre de tentatives échouées
            $nbr_tentatives_echouees++;
            $verif = false;
            return false;
        }
    
        // Le jeton est valide, réinitialiser le nombre de tentatives échouées
        $nbr_tentatives_echouees = 0;
        $verif = true;
        return true;
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
    
/**
 * @var object|null $utilisateur
 */
    public function cryptInfoUser() {
        $utilisateur = $this->getUserSession();

        // Crypte les informations de l'utilisateur
        $info_crypt = openssl_encrypt(serialize($this->filterDataUser()), 'AES-128-ECB', $this->id_cle);
        return $info_crypt;
    }

    /**
     * Summary of decryptInfoUser
     * @param mixed $verif
     * @return mixed
     */
    public function decryptInfoUser() {
        
        // Décrypte les informations de l'utilisateur
        $info_decrypt = openssl_decrypt($this->verif, 'AES-128-ECB', $this->id_cle);
        return unserialize($info_decrypt);
    }

    public function cookiesUserInfo() {
        // Stocke les informations de l'utilisateur cryptées dans un cookie
        $cookie_name = "utilisateur_info";
        $cookie_value = $this->cryptInfoUser();
        setcookie($cookie_name, $cookie_value, time() + (86400 * 30), "/"); // 1 jour
    }

   
    public function restrection() {
        // Vérifie si l'utilisateur a les permissions nécessaires pour accéder à une page
        foreach ($this->role->permissions as $permission) {
            if ($permission->nom_permission == 'restrection') {
                return true;
            }
        }
        return false;
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

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Retrieve the email address from the form
            $email = $_POST['email'];
          
        // Vérifie si l'adresse email est valide
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        // Génère un token de récupération d'identité
        $token = $this->generateToken($this->id_cle, $email);

        // Envoie un email à l'utilisateur contenant le lien de récupération d'identité
        $to = $email;
        $subject = 'Récupération d\'identité - Cle USB';
        $message = 'Bonjour, \n\n
                    Vous avez demandé à récupérer votre identité via votre cle USB. \n\n
                    Voici votre token:'.$token.' \n\n
                    Cordialement, \n
                    L\'équipe de monsite.com';
        $headers = 'From: ' . "\r\n" .
                   'Reply-To: ' . "\r\n" .
                   'X-Mailer: PHP/' . phpversion();

        if (mail($to, $subject, $message, $headers)) {
            return true;
        } else {
            return false;
        }
    }
}

    public function sendRecoverIdentite(){

        // Vérifier si l'utilisateur est autorisé à récupérer son identité avec une clé USB
        $utilisateur = $this->getUserSession();
        if (!$utilisateur) {
            return false; // L'utilisateur n'est pas connecté
        }

        if (!$this->restrection('USB', $utilisateur)) {
            return false; // L'utilisateur n'est pas autorisé à utiliser une clé USB pour récupérer son identité
        }
        
        // Récupérer l'adresse email de l'utilisateur
        $email = $utilisateur->getEmail();

        // Génère un token de récupération d'identité
        $token = $this->generateToken($this->id_cle, $email);

        // Envoyer un e-mail à l'utilisateur avec le code de récupération
        $destinataire = $email ;
        $sujet = "Récupération d'identité";
        $message = "Bonjour,\n\nVous avez demandé à récupérer votre identité sur notre site.\n\n
                    Voici votre code de récupération : ".$token."\n\n
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
    // Vérifier si l'utilisateur est authentifié avec une clé USB
    $is_authenticated = $this->Verify();
    if (!$is_authenticated) {
        return false;
    } 
    // Récupérer les informations de l'utilisateur
    $utilisateur = $this->getUserSession();
    if (!$utilisateur) {
        return false;
    }
    // Filtrer les données de l'utilisateur
    $this->filterDataUser();
    // Récupérer l'identité de l'utilisateur
    $identite = $this->getUserInfo($utilisateur);
    return $identite;

    }
    public function secureRecoverIdentite() {
        // Vérifier si une clé USB est détectée
        $cles_usb = $this->detecterCleUSB();
        if (empty($cles_usb)) {
            return false; // Aucune clé USB détectée
        }
    
        // Vérifier si l'utilisateur est connecté
        if (!$this->IsConnect()) {
            return false; // L'utilisateur n'est pas connecté
        }
    
        // Vérifier si l'utilisateur est autorisé à récupérer son identité
        $utilisateur = $this->getUserSession();
        if (!$this->restrection('ID',$utilisateur)) {
            return false;
        }
    
        // Vérifier si l'utilisateur a fourni un jeton valide
        if (!isset($_POST['token']) || empty($_POST['token'])) {
            return false; // Le jeton n'a pas été fourni ou est vide
        }
    
        $token = $_POST['token'];
    
        // Decrypter le jeton
        $secret_key = 'my_secret_key';
        $token_string = openssl_decrypt($token, 'AES-256-CBC', $secret_key, 0, 'my_init_vector');
    
        // Extraire les informations du jeton
        list($id_cle, $id_utilisateur, $date_expiration_clestamp, $random_string) = explode('|', $token_string);
    
        // Vérifier si le jeton est expiré
        if (time() > $date_expiration_clestamp) {
            return false; // Le jeton a expiré
        }
    
        // Vérifier si le jeton est valide
        if ($this->id_cle !== $id_cle) {
            return false; // Le jeton est invalide
        }
    
        // Récupérer les informations de l'utilisateur
        $utilisateur = new utilisateur();
        $utilisateur->getIdUtilisateur($id_utilisateur);
      
        // Retourner les informations de l'utilisateur
        return array(
            'nom' => $utilisateur-> nom,
            'prenom' => $utilisateur->prenom,
            'email' => $utilisateur-> getEmail(),
            'mot_de_passe' => $utilisateur->getMotDePasse()
        );
    }
    

}
?>
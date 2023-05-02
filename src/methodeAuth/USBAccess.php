<?php
class USBAccess implements iAuthentification {
    private $id_cle;
   
    private $date_expiration_cle;
    private $token;
    private $role;
    
    public function setRole($role){
    $this->role = $role;
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
    

    public function cryptInfoUser() {
        $utilisateur = $this->getUserSession();
        // Crypte les informations de l'utilisateur
        $info_crypt = openssl_encrypt(serialize($this->filterDataUser($utilisateur)), 'AES-128-ECB', $this->id_cle);
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


}


?>



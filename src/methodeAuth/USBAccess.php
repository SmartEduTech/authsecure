<?php
class USBAccess implements iAuthentification {
    private $id_cle;
    private $id_utilisateur;
    private $date_expiration_cle;
    private $token;

    function __construct($id_cle, $id_utilisateur, $date_expiration_cle) {
        $this->id_cle = $id_cle;
        $this->id_utilisateur = $id_utilisateur;
        $this->date_expiration_cle = $date_expiration_cle;
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
    
    public function Verify($nbr_tentatives_echouees,$verif ) {
        // Vérifier si l'utilisateur a fourni un jeton valide
        if (!isset($_POST['usb_token']) || empty($_POST['usb_token'])) {
            return false; // Le jeton n'a pas été fourni ou est vide
        }
    
        $token = $_POST['usb_token'];
        $utilisateur = $this->getutilisateurSession();
        if (!$utilisateur) {
            return false; // L'utilisateur n'est pas connecté
        }
    
        // Vérifier si l'utilisateur est autorisé à utiliser un jeton USB
        if (!$this->restriction('utiliser_usb', $utilisateur)) {
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

    public function IsConnect($est_authentifier ) {
        // Vérifie si l'utilisateur est connecté
        if ($est_authentifier == true) {
            return true;
        }
        return false;
    }

    /**
     * Summary of filterDatautilisateur
     * @param mixed $nom
     * @param mixed $prenom
     * @param mixed $email
     * @param mixed $adresse
     * @param mixed $role
     * @param mixed $est_authentifier
     * @param mixed $date_connexion
     * @return array|bool|null
     */
    public function filterDatautilisateur($nom,$prenom,$email,$adresse,$role,$est_authentifier,$date_connexion) {
        // Retourne les informations de l'utilisateur filtrées
        return filter_var_array([
            'id_utilisateur' => $this->id_utilisateur,
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email,
            'adresse' => $adresse,
            'role' => $role,
            'est_authentifier' => $est_authentifier,
            'date_connexion' => $date_connexion
        ], FILTER_SANITIZE_STRING);
    }

    public function getutilisateurSession() {
        // Retourne les informations de l'utilisateur stockées dans la session
        return $_SESSION['utilisateur'];
    }

    public function getutilisateurInfo() {
        // Retourne les informations de l'utilisateur filtrées et stockées dans la session
        $_SESSION['utilisateur'] = filterDatautilisateur();
        return $_SESSION['utilisateur'];
    }

    public function cryptInfoutilisateur() {
        // Crypte les informations de l'utilisateur
        $info_crypt = openssl_encrypt(serialize($this->filterDatautilisateur()), 'AES-128-ECB', $this->id_cle);
        return $info_crypt;
    }

    /**
     * Summary of decryptInfoutilisateur
     * @param mixed $verif
     * @return mixed
     */
    public function decryptInfoutilisateur($verif) {
        // Décrypte les informations de l'utilisateur
        $info_decrypt = openssl_decrypt($verif, 'AES-128-ECB', $this->id_cle);
        return unserialize($info_decrypt);
    }

    public function cookiesutilisateurInfo() {
        // Stocke les informations de l'utilisateur cryptées dans un cookie
        $cookie_name = "utilisateur_info";
        $cookie_value = $this->cryptInfoutilisateur();
        setcookie($cookie_name, $cookie_value, time() + (86400 * 30), "/"); // 1 jour
    }

    /**
     * Summary of restrection
     * @param mixed $role
     * @return bool
     */
    public function restrection($role) {
        // Vérifie si l'utilisateur a les permissions nécessaires pour accéder à une page
        foreach ($role->permissions as $permission) {
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



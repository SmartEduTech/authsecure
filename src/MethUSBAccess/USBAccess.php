<?php
namespace Smartedutech\Authsecure\MethUSBAccess;
class USBAccess  {
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
    
    
    
        // Vérifier si le jeton est expiré
        if (time() > $this->date_expiration_cle) {
            return false; // Le jeton a expiré
        }
    
        // Vérifier si le nombre de tentatives a été dépassé
        if ($nbr_tentatives_echouees >= 3) {
            return false; // Le nombre de tentatives a été dépassé
        }
    
        // Le jeton est valide, réinitialiser le nombre de tentatives échouées
        $nbr_tentatives_echouees = 0;
        $verif = true;
        return true;
    }


}
?>
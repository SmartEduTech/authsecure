<<?php 
//crée class authentification 
class Authentification {
    protected $id_authentification;
    protected $id_utilisateur;
    protected $date_connexion;
    protected $adresse_ip;
  
    public function __construct($id_authentification, Utilisateur $utilisateur, $date_connexion, $adresse_ip) {
      $this->id_authentification = $id_authentification;
      $this->id_utilisateur = $utilisateur;
      $this->date_connexion = $date_connexion;
      $this->adresse_ip = $adresse_ip;
    }
  
 
  }
   // crée class authentification a deux facteurs que hérite la class authentification
  class auth2fa extends Authentification {
   
    public $id_utilisateur;
    private $mot_de_passe;
    private $code_secret;
    public $date_expiration_code;
    public $nbr_tentatives_echouees;
    public $verif;

    function __construct(Utilisateur $id_utilisateur,$mot_de_passe,$code_secret,$date_expiration_code,$nbr_tentatives_echouees,$verif) 
    {
        $this->id_utilisateur = $id_utilisateur;
        $this->mot_de_passe = $mot_de_passe ;
        $this->code_secret = $code_secret;
        $this->date_expiration_code = $date_expiration_code;
        $this->nbr_tentatives_echouees = $nbr_tentatives_echouees;
        $this->verif = $verif;
    }
    public function Envoyer_code_secret() {
        // Code de la méthode 
    }
    public function verifier_code_secret() {
        // Code de la méthode 
    }
    
}
  // crée class authentification avec clé usb que hérite la class authentification
  class usbaccess extends Authentification {

        public $id_utilisateur;
        private $id_cle;
        public $date_expiration_cle;
        public $nbr_tentatives_echouees;
        public $verif;
    
        function __construct(Utilisateur $id_utilisateur,$id_cle,$code_secret,$date_expiration_cle,$nbr_tentatives_echouees,$verif) 
        {
            $this->id_utilisateur = $id_utilisateur;
            $this->id_cle = $id_cle ;
            $this->date_expiration_cle = $date_expiration_cle;
            $this->nbr_tentatives_echouees = $nbr_tentatives_echouees;
            $this->verif = $verif;
        }
    
        public function connecter_USB() {
            // Code de la méthode 
        }
        public function deconnecter_USB() {
            // Code de la méthode 
        }
        public function verifier_USB() {
            // Code de la méthode 
        }

    }
    
  

?>
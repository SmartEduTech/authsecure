<?php

class utilisateur {

    public $id_utilisateur;
    public $nom;
    public $prenom;
    private $email;
    public $adress;
    private $mot_de_passe;
    public $role;
    public $est_authentifier;
   

    function __construct($id_utilisateur,$nom ,$prenom ,$email,$adress,$mot_de_passe,$role,$est_authentifier) 
    {
        $this->id_utilisateur = $id_utilisateur;
        $this->nom  = $nom ;
        $this->prenom = $prenom;
        $this->email = $email;
        $this->adress  = $adress ;
        $this->mot_de_passe= $mot_de_passe;
        $this->role = $role;
        $this->est_authentifier = $est_authentifier;
    }

}

class permission {

    public $id_permission;
    public $nom_permission;
    public $descrip_permission;
   


    function __construct($id_permission, $nom_permission,$descrip_permission) 
    {
        $this->id_permission = $id_permission;
        $this->nom_permission  = $nom_permission;
        $this->descrip_permission = $descrip_permission;
       
    }

}

class role {

    public $id_role;
    public $nom_role;
    public $permissions;
   


    function __construct($id_role, $nom_role,$permissions) 
    {
        $this->id_role = $id_role;
        $this->nom_role  = $nom_role;
        $this->permissions = $permissions;
       
    }

}

class authentifier {

    public $id_authentification;
    public $id_utilisateur;
    public $date_connexion;
    private $adresse_ip;


    function __construct($id_authentification, $id_utilisateur,$date_connexion,$adresse_ip) 
    {
        $this->id_utilisateur = $id_utilisateur;
        $this->id_authentification  = $id_authentification ;
        $this->date_connexion = $date_connexion;
        $this->adresse_ip = $adresse_ip;
       
    }

}

class auth2fa {

    public $id_utilisateur;
    private $mot_de_passe;
    private $code_secret;
    public $date_expiration_code;
    public $nbr_tentatives_echouees;
    public $verif;

    function __construct($id_utilisateur,$mot_de_passe,$code_secret,$date_expiration_code,$nbr_tentatives_echouees,$verif) 
    {
        $this->id_utilisateur = $id_utilisateur;
        $this->mot_de_passe  = $mot_de_passe ;
        $this->code_secret = $code_secret;
        $this->date_expiration_code = $date_expiration_code;
        $this->nbr_tentatives_echouees = $nbr_tentatives_echouees;
        $this->verif = $verif;
    }
    
    
}
class usbaccess {

    public $id_utilisateur;
    private $nom_cle;
    public $date_expiration_cle;
    public $nbr_tentatives_echouees;
    public $verif;

    function __construct($id_utilisateur,$nom_cle,$code_secret,$date_expiration_cle,$nbr_tentatives_echouees,$verif) 
    {
        $this->id_utilisateur = $id_utilisateur;
        $this->nom_cle  = $nom_cle ;
        $this->date_expiration_cle = $date_expiration_cle;
        $this->nbr_tentatives_echouees = $nbr_tentatives_echouees;
        $this->verif = $verif;
    }
    // Fonction de vérification d'authentification
    public function estAuthentifie() {
        // Récupérer l'identifiant unique de la clé USB
        $cle_usb_id = $this->get_cle_usb_id();
        
        // Vérifier si l'identifiant de la clé USB est autorisé
        if ($this->estCleUsbAutorisee($cle_usb_id)) {
            // Mettre à jour les attributs de la classe
            $this->date_expiration_cle = $this->getDateExpirationCle();
            $this->nbr_tentatives_echouees = 0;
            $this->verif = true;
            
           
            return true;
        } else {
          
            $this->nbr_tentatives_echouees++;
        
            $this->verif = false;
            
            return false;
        }
    }
    
    // Fonction pour récupérer l'identifiant unique de la clé USB
    private function get_cle_usb_id() {
        // Code pour récupérer l'identifiant unique de la clé USB
        // ...
        
        return $cle_usb_id;
    }
    
    // Fonction pour vérifier si l'identifiant de la clé USB est autorisé
    private function estCleUsbAutorisee($cle_usb_id) {
        // Code pour vérifier si l'identifiant de la clé USB est autorisé
        // ...
        
        return $est_autorisee;
    }
    
    // Fonction pour récupérer la date d'expiration de la clé USB
    private function getDateExpirationCle() {
        // Code pour récupérer la date d'expiration de la clé USB
        // ...
        
        return $date_expiration;
    }
}
    
    




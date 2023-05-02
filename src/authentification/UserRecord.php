<?php

class Utilisateur {
    private $id_utilisateur;
    public $nom;
    public $prenom;
    private $email;
    public $adress;
    private $mot_de_passe;
    public $role;
    public $est_authentifier;

    public function getEmail() {
        return $this->email;
    }

    public function getIdUtilisateur() {
        return $this->id_utilisateur;
    }

    public function getMotDePasse() {
        return $this->mot_de_passe;
    }
}


class permission {

    public $id_permission;
    public $nom_permission;
    public $descrip_permission;

}

class role {

    public $id_role;
    public $nom_role;
    public $permissions;
    
}

class authentifier {

    public $id_authentification;
    public $id_utilisateur;
    public $date_connexion;
    private $adresse_ip;

    public function getAdresse_ip() {
        return $this->adresse_ip;
    }


}

class auth2fa {

    public $id_utilisateur;
    private $mot_de_passe;
    private $code_secret;
    public $date_expiration_code;
    public $nbr_tentatives_echouees;
    public $verif;
    public function getMotDePasse() {
        return $this->mot_de_passe;
    }
    public function getCodeSecret() {
        return $this->code_secret;
    }

}
class usbaccess {

    public $id_utilisateur;
    private $id_cle;
    public $date_expiration_cle;
    public $nbr_tentatives_echouees;
    public $verif;
    public function getIdCle() {
        return $this->id_cle;
    }
}

   
    ?>

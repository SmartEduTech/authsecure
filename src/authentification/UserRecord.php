<?php

class utilisateur {

    public $id_utilisateur;
    public $nom;
    public $prenom;
    public $email;
    public $adress;
    public $mot_de_passe;
    public $role;
    public $est_authentifier;
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
}

class auth2fa {

    public $id_utilisateur;
    private $mot_de_passe;
    private $code_secret;
    public $date_expiration_code;
    public $nbr_tentatives_echouees;
    public $verif;
}
class usbaccess {

    public $id_utilisateur;
    private $id_cle;
    public $date_expiration_cle;
    public $nbr_tentatives_echouees;
    public $verif;
}

   
    ?>

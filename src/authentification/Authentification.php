<?php

class authentification {

    private $utilisateurs;
    private $connections;
    private $connection;
    private $id;

    function __construct($utilisateurs,$connections,$connection,$id) 
    {
        $this->utilisateurs = $utilisateurs;
        $this->connections = $connections;
        $this->connection = $connection;
        $this->id = $id;
    }
    

   
    public function verify($email, $mot_de_passe,$nbr_tentatives_echouees,$est_authentifier) 
    {
        foreach ($this->utilisateurs as $utilisateur) {
            if ($utilisateur->email == $email && $utilisateur->mot_de_passe == $mot_de_passe) 
            {
                $utilisateur->est_authentifier = true;
                $this->nbr_tentatives_echouees = 0;

                return true;
            }
    
            else {
                $this->nbr_tentatives_echouees += 1;
                if ($this->nbr_tentatives_echouees >= 3) {
                    // Bloquer le compte si le nombre de tentatives échouées est supérieur ou égal à 3
                    $this->est_authentifier = false;
                }
                return false;
            }
        }
    }

        public function isConnected()

        {
            return $this->est_authentifier;
        }

        public function getUserById($id) {


            foreach ($this->utilisateurs as $utilisateur) {
        if ($utilisateur->id_utilisateur == $id) {
            return $utilisateur;
        }
    }
    return null; // si l'utilisateur n'est pas trouvé, on retourne null
}
        public function InfoUserConnected() {
            if ($this->isConnected()) {
                // L'utilisateur est connecté, on renvoie ses informations
                // On suppose que la variable de session 'id_utilisateur' contient l'ID de l'utilisateur connecté
                $id_utilisateur = $_SESSION['id_utilisateur']; 
                $utilisateur = $this->getUserById($id_utilisateur); 
                return "Utilisateur connecté : ".$utilisateur->nom." ".$utilisateur->prenom." (".$utilisateur->email.")";
            } else {
                // Aucun utilisateur n'est connecté
                return "Aucun utilisateur n'est connecté.";
            }
        }
    

    public function getTimeConnect($id_utilisateur) {
        // Récupération de la dernière connexion de l'utilisateur
        $last_connection = null;
        foreach ($this->connections as $connection) {
            if ($connection->id_utilisateur == $id_utilisateur && (!$last_connection || $connection->date_connexion > $last_connection->date_connexion)) {
                $last_connection = $connection;
            }
        }

        // Retourne la date et l'heure de la dernière connexion
        if ($last_connection) {
            return $last_connection->date_connexion;
        } else {
            return null;
        }
    }

public function deconnecter() {

    if (!$this->isConnected()) {

        return "utilisateur non connecté";
    }
    else {

    // Détruire toutes les données de la session
    session_unset();
    
    // Détruire la session
    session_destroy();
    
    // Rediriger vers la page de login
    header('Location: login.php');
}
}
}
?>
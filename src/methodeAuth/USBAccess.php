<?php

// function est_authentifier($cle_usb) 
{
    if ($this->nom_cle === $cle_usb && $this->date_expiration_cle > time() && $this->nbr_tentatives_echouees < 3) {
        $this->verif = true;
        return true;
    } else {
        $this->nbr_tentatives_echouees++;
        $this->verif = false;
        return false;
    }
}
function authentification_par_cle_usb($id_utilisateur, $nom_cle, $date_expiration_cle, $nbr_tentatives_echouees, $verif) {
    // Vérifier si la clé USB est valide
    if ($date_expiration_cle < time()) {
        $verif = false;
        return $verif;
    }

    // Vérifier le nombre de tentatives d'authentification échouées
    if ($nbr_tentatives_echouees > 3) {
        $verif = false;
        return $verif;
    }

    // Vérifier si la clé USB correspond à l'utilisateur
    $id_utilisateur_from_usb = obtenir_id_utilisateur_par_nom_cle($nom_cle);
    if ($id_utilisateur_from_usb !== $id_utilisateur) {
        $verif = false;
        return $verif;
    }

    // Si toutes les vérifications sont passées avec succès, marquer l'authentification comme réussie
    $verif = true;
    return $verif;
}
?>
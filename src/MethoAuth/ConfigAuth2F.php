<?php

namespace Smartedutech\Authsecure\MethoAuth;

class ConfigAuth2F {
    private $id_cle;
    private $utilisateur;
    private $code_secret;
    private $verif;

    public function __construct($id_cle, $utilisateur, $code_secret, $verif) {
        $this->id_cle = $id_cle;
        $this->utilisateur = $utilisateur;
        $this->code_secret = $code_secret;
        $this->verif = $verif;
    }

    public function envoyer_email($email, $URL) {
        $auth2F = new Auth2F($this->id_cle, $this->utilisateur, $this->code_secret, $this->verif);
        $auth2F->envoyer_code_secret($email, $URL);
    }
}

?>

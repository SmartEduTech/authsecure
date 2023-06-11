<?php

namespace Smartedutech\Authsecure\MethoAuth;
use Smartedutech\Authsecure\Mailsender\MailTemplateGen;
use Smartedutech\Authsecure\Mailsender\MailConfig;
require 'src/MethoAuth/TokenGen.php';

class Auth2F {
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

    public function envoyer_code_secret($email, $URL) {
        // Code pour envoyer le code secret par e-mail
        $code_secret = $this->generer_code_2F();
        $contenu_email = "Code à deux facteurs : " . $code_secret . "\n";
        $contenu_email .= "Voici l'URL : " . $URL;

        // Envoyer l'e-mail à l'utilisateur avec le code secret
        $this->envoyer_email($email, "Code à deux facteurs", $contenu_email);
    }

    public function generer_code_2F() {
        $tokenGen = new TokenGen();
        return $tokenGen->generateToken(['user_id' => 123], $this->code_secret);
    }

    private function envoyer_email($destinataire, $sujet, $contenu) {
        $mailConfig = new MailConfig();
        $smtpConfig = $mailConfig->getConfig();
    
        $mailSender = new MailSender();
        
        $templatePath = __DIR__ . '/../Mailsender/template.html';
        $data = array(
            'code_secret' => $contenu,
            'url' => $sujet
        );
        
        $emailContent = MailTemplateGen::generateEmailContent($templatePath, $data);
        
        $mailSender->sendEmail($destinataire, $sujet, $emailContent, $smtpConfig['fromAddress'], $smtpConfig['fromName']);
    }
    
    
}

?>


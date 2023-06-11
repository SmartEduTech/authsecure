<?php

namespace Smartedutech\Authsecure\Mailsender;

class MailConfig {
    public static function getConfig() {
        return [
            'host' => 'smtp.gmail.com',
            'port' => 465,
            'username' => 'amal.korbi@etudiant-isi.utm.tn',
            'password' => 'Azerty*1234',
            'fromAddress' => 'amalkorbi96@gmail.com',
            'fromName' => 'Amal',
        ];
    }
}
?>
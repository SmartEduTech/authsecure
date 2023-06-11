<?php
namespace Smartedutech\Authsecure\Mailsender;

class MailTemplateGen {
    public static function generateEmailContent($templatePath, $data) {
        // Lecture du contenu du fichier template.html
        $template = file_get_contents($templatePath);
    
        // Remplacement des variables dans le template
        $template = str_replace('{{code_secret}}', $data['code_secret'], $template);
        $template = str_replace('{{url}}', $data['url'], $template);
    
        return $template;
    }
    
}
?>
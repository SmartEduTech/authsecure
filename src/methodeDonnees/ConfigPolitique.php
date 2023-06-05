<?php

class ConfigPolitique implements Data {
    private $parameters;
    private $userPassword;
    private $userActivity;

    /**
     * Constructeur de la classe ConfigPolitique.
     * @param array $parameters Les paramètres de configuration des politiques de sécurité.
     * @param string $userPassword Le mot de passe de l'utilisateur.
     * @param array $userActivity Les activités de l'utilisateur.
     */
    public function __construct($parameters, $userPassword, $userActivity) {
        $this->parameters = $parameters;
        $this->userPassword = $userPassword;
        $this->userActivity = $userActivity;
    }

    /**
     * Applique les politiques de sécurité à l'utilisateur.
     * @return bool Retourne true si toutes les politiques de sécurité sont satisfaites, sinon false.
     */
    public function applySecurityPolicies()
    {
        return $this->isPasswordComplex() &&
               !$this->isAccountLocked() &&
               !$this->isPasswordExpired() &&
               $this->hasPermission();
    }

    /**
     * Vérifie si le mot de passe de l'utilisateur satisfait les critères de complexité définis.
     * @return bool Retourne true si le mot de passe est complexe, sinon false.
     */
    public function isPasswordComplex()
    {
        $password = $this->userPassword;
        $minLength = $this->parameters['longueur_minimale'];
        $requiresSpecialChars = $this->parameters['complexite_caracteres_speciaux'];
        $requiresUpperCase = $this->parameters['complexite_lettres_majuscules'];
        $requiresNumbers = $this->parameters['complexite_chiffres'];

        return strlen($password) >= $minLength &&
               (!$requiresSpecialChars || preg_match('/[!@#$%^&*]/', $password)) &&
               (!$requiresUpperCase || preg_match('/[A-Z]/', $password)) &&
               (!$requiresNumbers || preg_match('/\d/', $password));
    }

    /**
     * Vérifie si le compte de l'utilisateur est verrouillé en fonction du nombre de tentatives de connexion infructueuses
     * et de la durée de verrouillage définie.
     * @return bool Retourne true si le compte est verrouillé, sinon false.
     */
    public function isAccountLocked()
    {
        $maxLoginAttempts = $this->parameters['seuil_tentatives_infructueuses'];
        $accountLockDuration = $this->parameters['duree_verrouillage'];

        return $this->userActivity['login_attempts'] >= $maxLoginAttempts &&
               (time() - $this->userActivity['last_login_attempt']) < $accountLockDuration;
    }

    /**
     * Vérifie si le mot de passe de l'utilisateur a expiré en fonction de la période de validité définie.
     * @return bool Retourne true si le mot de passe a expiré, sinon false.
     */
    public function isPasswordExpired()
    {
        $passwordExpirationPeriod = $this->parameters['duree_validite_mot_de_passe'];

        return (time() - $this->userActivity['last_password_change']) > $passwordExpirationPeriod;
    }

    /**
     * Vérifie si l'utilisateur a la permission requise en fonction de son rôle et des permissions autorisées.
     * @return bool Retourne true si l'utilisateur a la permission, sinon false.
     */
    public function hasPermission()
    {
        $userRole = $this->parameters['Administrateur'];
        $requiredPermission = $this->parameters['permissions_autorisees'];

        return $userRole === 'administrateur' || $userRole[$requiredPermission];
    }
}
?>
<?php 
class DefRegle {
    private $regle;
    private $reglePolicyAssociations;

    public function __construct() {
        $this->regle = [];
        $this->reglePolicyAssociations = [];
    }

    public function addregle($regleName, $regleDescription) {
        $this->regle[$regleName] = $regleDescription;
       
    }

    public function getregle() {
        return $this->regle;
    }

    public function removeregle($regleName) {
        if (isset($this->regle[$regleName])) {
            unset($this->regle[$regleName]);
        }
    }

    public function setPasswordLengthregle($passwordMinLength) {
        $regleDescription = "La longueur minimale du mot de passe doit être de " . $passwordMinLength . " caractères.";
        $this->addregle('PasswordLength', $regleDescription);
    }

    public function setSpecialCharsregle($passwordRequiresSpecialChars) {
        if ($passwordRequiresSpecialChars) {
            $regleDescription = "Le mot de passe doit contenir des caractères spéciaux.";
            $this->addregle('SpecialChars', $regleDescription);
        } else {
            $this->removeregle('SpecialChars');
        }
    }

    public function setUpperCaseregle($passwordRequiresUpperCase) {
        if ($passwordRequiresUpperCase) {
            $regleDescription = "Le mot de passe doit contenir des lettres majuscules.";
            $this->addregle('UpperCase', $regleDescription);
        } else {
            $this->removeregle('UpperCase');
        }
    }

    public function setNumbersregle($passwordRequiresNumbers) {
        if ($passwordRequiresNumbers) {
            $regleDescription = "Le mot de passe doit contenir des chiffres.";
            $this->addregle('Numbers', $regleDescription);
        } else {
            $this->removeregle('Numbers');
        }
    }

    public function setMaxLoginAttemptsregle($maxLoginAttempts) {
        $regleDescription = "Le nombre maximum de tentatives de connexion est fixé à " . $maxLoginAttempts . ".";
        $this->addregle('MaxLoginAttempts', $regleDescription);
    }

    public function setAccountLockDurationregle($lockDuration) {
        $regleDescription = "La durée de verrouillage du compte est de " . $lockDuration . " minutes.";
        $this->addregle('AccountLockDuration', $regleDescription);
    }

    public function setPasswordExpirationregle($passwordExpirationPeriod) {
        $regleDescription = "La durée de validité du mot de passe est de " . $passwordExpirationPeriod . " jours.";
        $this->addregle('PasswordExpiration', $regleDescription);
    }

    public function setRequiredPermissionregle($requiredPermission) {
        $regleDescription = "L'autorisation requise est " . $requiredPermission . ".";
        $this->addregle('RequiredPermission', $regleDescription);
    }


public function associateRuleWithPolicy($regleName, $policyName) {
if (!isset($this->reglePolicyAssociations[$regleName])) {
    $this->reglePolicyAssociations[$regleName] = [];
}

if (!in_array($policyName, $this->reglePolicyAssociations[$regleName])) {
    $this->reglePolicyAssociations[$regleName][] = $policyName;
}
}
}

?>
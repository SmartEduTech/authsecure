<?php
interface Data{
 
    public function applySecurityPolicies();
    public function isPasswordComplex();
    public function isAccountLocked();
    public function isPasswordExpired();
    public function hasPermission();
    
}

?>

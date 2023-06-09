<?php
namespace Smartedutech\Authsecure\Authentification;

interface iAuthentification{
 
    public function Verify();
    public function IsConnect();
    public function filterDataUser();
    public function getUserSession();
    public function getUserInfo();
    public function cryptInfoUser();
    public function decryptInfoUser();
    public function cookiesUserInfo();
    public function restrection();
    public function BlackIP();


}

?>

<?php

interface iIdentiteRecover{

    public function sendInvitToRecover();
    public function sendRecoverIdentite();
    public function verifyIdentite();
    public function secureRecoverIdentite();

}
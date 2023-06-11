<?php

namespace Smartedutech\Authsecure\MethoAuth;

use Firebase\JWT\JWT;

class TokenGen {
    public static function generateToken($payload, $secretKey) {
        // Génération du token JWT
        $token = JWT::encode($payload, $secretKey, 'HS256');
        return $token;
    }
}

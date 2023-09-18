<?php

require './vendor/autoload.php';

use Firebase\JWT\JWT;

class JwtHandler
{
    protected $jwt_secret;
    protected $token;
    protected $issuedAt;
    protected $expire;
    protected $jwt;

    public function __construct()
    {
        // Time Zone
        date_default_timezone_set('Asia/Jakarta');
        $this->issuedAt = time();

        // Token Validity (3600 Seconds = 1 Hour)
        $this->expire = $this->issuedAt + 3600;

        // Secret Signature
        $this->jwt_secret = 'change_your_secret';
    }

    public function jwtEncodeData($iss, $data)
    {
        $this->token = array(
            // Identifier to the Token
            'iss'    => $iss,
            'aud'    => $iss,
            // Current timestamp to the Token
            'iat'    => $this->issuedAt,
            // Toket expiration
            'exp'    => $this->expire,
            // Payload
            'data'   => $data
        );

        $this->jwt = JWT::encode($this->token, $this->jwt_secret, 'HS256');
        return $this->jwt;
    }

    public function jwtDecodeData($jwt_token)
    {
        try {
            $decode = JWT::decode($jwt_token, $this->jwt_secret, array('HS256'));
            return [
                'data' => $decode->$data
            ];
        }
        catch(Exception $e) {
            return [
                'message' => $e->getMessage()
            ];
        }
    }
}

?>
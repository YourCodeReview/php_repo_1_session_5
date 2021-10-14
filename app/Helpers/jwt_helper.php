<?php

use App\Models\UserModel;
use Config\Services;
use Firebase\JWT\JWT;

function getJWTFromRequest($authenticationHeader): string
{
    if (is_null($authenticationHeader)) {
        throw new Exception('Missing or invalid JWT in request');
    }
    return explode(' ', $authenticationHeader)[1];
}

function validateJWTFromRequest(string $encodedToken)
{
    $key = Services::getSecretKey();
    $decodedToken = JWT::decode($encodedToken, $key, ['HS256']);
    $userModel = new UserModel();
    return $userModel->findUserByUserID((int)$decodedToken->uid);
}

function getSignedJWTForUser(int $userid)
{
    $issuedAtTime = time();
    $tokenTimeToLive = Services::getJWTTTL();
    $tokenExpiration = $issuedAtTime + $tokenTimeToLive;
    $payload = [
        'uid' => $userid,
        'iat' => $issuedAtTime,
        'exp' => $tokenExpiration,
    ];

    $jwt = JWT::encode($payload, Services::getSecretKey());
    return $jwt;
}

<?php
require_once __DIR__ . "/../vendor/autoload.php";

use Firebase\JWT\JWT;

function generateAccessToken($userId, $user_id, $userRole, $secretKey)
{
    $issuedAt = time();
    $expirationTime = $issuedAt + 1200; // Token valid for 20 minutes (1200 seconds)

    $payload = [
        'sub' => $userId,
        "uid" => $user_id,
        'role' => $userRole,
        'iat' => $issuedAt,
        'exp' => $expirationTime,
    ];

    // global $secretKey;
    return JWT::encode($payload, $secretKey, 'HS256');
}

function generateRefreshToken($userId, $secretKey)
{
    $issuedAt = time();
    $expirationTime = $issuedAt + 86400; // Token valid for 1 days (86400 seconds)

    $payload = [
        'sub' => $userId,
        'iat' => $issuedAt,
        'exp' => $expirationTime,
    ];

    // global $secretKey;
    return JWT::encode($payload, $secretKey, 'HS256');
}

<?php

// require 'vendor/autoload.php';
require __DIR__ . '/../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$secretKey = $mySecretKey;

$headers = getallheaders();
if (!isset($headers['Authorization'])) {
    http_response_code(401);
    echo json_encode(['message' => 'Authorization header missing']);
    exit;
}

// // Decode JWT from Authorization header
$authHeader = $headers['Authorization'] ?? '';

if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
    $jwt = $matches[1];

    try {
        $decoded = JWT::decode($jwt, new Key($secretKey, 'HS256'));

        // DEBUG: Log decoded token contents
        error_log("Decoded JWT: " . print_r($decoded, true));

        // Verify required claims exist
        if (!isset($decoded->sub)) {
            http_response_code(401);
            echo json_encode(['error' => 'Token missing user_id']);
            exit;
        }

        // Check if the token is expired
        if ($decoded->exp < time()) {
            http_response_code(401);
            echo json_encode(['error' => 'Token expired']);
            exit();
        }
    } catch (Exception $e) {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid token']);
        exit();
    }
} else {
    http_response_code(401);
    echo json_encode(['error' => 'No token provided']);
    exit();
}
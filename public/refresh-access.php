<?php
require_once __DIR__ . "/../database/connection.php";

require_once __DIR__ . "/../components/header.php";

require_once __DIR__ . "/../functions/tokenFunctions.php";

require_once __DIR__ . "/../components/authorization.php";

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    if ($decoded) {

        try {
            // Generate a new access token
            $newAccessToken = generateAccessToken($decoded->sub, $decoded->uid, $decoded->role, $secretKey);
            http_response_code(200);
            echo json_encode(['token' => $newAccessToken]);
        } catch (Exception $e) {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid or expired token']);
        }
    } else {
        http_response_code(401);
        echo json_encode(['error' => 'No token provided']);
    }
}

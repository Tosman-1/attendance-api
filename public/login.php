<?php
require_once __DIR__ . "/../database/connection.php";

require_once __DIR__ . "/../components/header.php";

require_once __DIR__ . "/../functions/tokenFunctions.php";

require_once __DIR__ . "/../functions/logActivity.php";

$input = file_get_contents("php://input");

$data = json_decode($input, true);

$secretKey = $mySecretKey;


if (isset($data['UID'], $data['PASSWORD'], $data['ROLE'])) {


    $user_id = trim($data['UID']);
    $password = trim($data['PASSWORD']);
    $role = trim($data['ROLE']);

    $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ? ");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        //get role
        $user_role = $user['role'];

        // Check if role matches
        if ($user_role !== $role) {
            http_response_code(401); // Unauthorized
            echo json_encode(["status" => "Failed", "message" => "Invalid login"]);
            exit;
        }

        if (password_verify($password, $user['password'])) {
            // Generate token
            $token = generateAccessToken($user['id'], $user['user_id'], $user['role'], $secretKey);
            http_response_code(200); // OK
            echo json_encode(["status" => "Success", "message" => "Login successful", "token" => $token]);

            logActivity("Login", $user['id'], $user['user_id'], $pdo);
        } else {
            http_response_code(401); // Unauthorized
            echo json_encode(["status" => "Failed", "message" => "Invalid login"]);

            logActivity("Login Failed", $user['id'], $user['user_id'], $pdo);
        }
    } else {
        http_response_code(401); // Unauthorized
        echo json_encode(["status" => "Failed", "message" => "Invalid login"]);

        logActivity("Login Failed", $user['id'], $user['user_id'], $pdo);
    }   
}

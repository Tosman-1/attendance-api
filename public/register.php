<?php
require_once __DIR__ . "/../database/connection.php";

require_once __DIR__ . "/../database/createTable.php";

require_once __DIR__ . "/../components/header.php";

require_once __DIR__ . "/../functions/logActivity.php";

$input = file_get_contents("php://input");

$data = json_decode($input, true);

// Define required fields
$requiredFields = [
    'FN',
    'LN',
    'EMAIL',
    'UID',
    'DEPT',
    'PASS',
];
$filteredData = [];

// Validate and trim fields dynamically
foreach ($requiredFields as $field => $filter) {
    if (empty($data[$field])) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => "Registration failed"]);
        exit;
    }
    $filteredData[$field] = trim($data[$field]);
}

// Extract variables after validation
$firstname = $filteredData['FN'];
$lastname = $filteredData['LN'];
$email = $filteredData['EMAIL'];
$user_id = $filteredData['UID'];
$department = $filteredData['DEPT'];
$password = $filteredData['PASS'];

if (filter_var($email, FILTER_VALIDATE_EMAIL)) {

    $sql = "SELECT email, user_id FROM users WHERE email = ? OR user_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email, $user_id]);

    $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existingUser) {

        if ($existingUser['email'] === $email) {
            http_response_code(409);
            echo json_encode([
                "status" => "failed",
                "message" => "Email already exists"
            ]);
            return;
        }

        if ($existingUser['user_id'] === $user_id) {
            http_response_code(409);
            echo json_encode([
                "status" => "failed",
                "message" => "User ID already exists"
            ]);
            return;
        }
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);


    try {

        $sql = "INSERT INTO users(firstname, lastname, email, user_id, department, password)
                VALUES (?,?,?,?,?,?)
                RETURNING id";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $firstname,
            $lastname,
            $email,
            $user_id,
            $department,
            $hashed_password
        ]);

        $uid = $stmt->fetch(PDO::FETCH_ASSOC)['id'];

        http_response_code(201);
        echo json_encode([
            "status" => "success",
            "message" => "Registration successful"
        ]);

        logActivity("User registered", $uid, $user_id, $pdo);

    } catch (PDOException $e) {

        switch ($e->getCode()) {
            case 23505: // PostgreSQL duplicate entry error
                http_response_code(409);
                echo json_encode([
                    "status" => "failed",
                    "message" => "Email or User ID already exists"
                ]);
                break;

            default:
                http_response_code(500);
                echo json_encode([
                    "status" => "failed",
                    "message" => "Server error"
                ]);
                break;
        }
    }

} else {
    http_response_code(400); // Bad Request
    $res = json_encode([
        "status" => "failed",
        "message" => "Invalid email address",
        // "data" => $data
    ]);

    logActivity("Registration Failed", null, $user_id, $pdo);
    echo trim($res);
}

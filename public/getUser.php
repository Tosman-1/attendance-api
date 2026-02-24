<?php
require_once __DIR__ . "/../database/connection.php";

require_once __DIR__ . "/../components/header.php";

require_once __DIR__ . "/../components/authorization.php";

// Get user ID from decoded token
$userId = $decoded->sub;

// Fetch user data from the database
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("s", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    http_response_code(404);
    echo json_encode(['error' => 'User not found']);
    exit();
}

// Return user data
echo json_encode($user);

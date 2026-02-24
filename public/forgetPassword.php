<?php
require_once __DIR__ . "/../database/connection.php";
require_once __DIR__ . "/../components/header.php";
require_once __DIR__ . "/../functions/sendMail.php";

$input = file_get_contents("php://input");

$data = json_decode($input, true);

if (!isset($data['EMAIL'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => "Email is required"]);
    exit;
}

$email = trim($data['EMAIL']);

if (filter_var($email, FILTER_VALIDATE_EMAIL)) {

    // Check if email exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!$user) {
        http_response_code(404); // Not Found
        echo json_encode(['status' => 'error', 'message' => "Email not found"]);
        exit;
    }

    $userId = $user['id'];
    $token = bin2hex(random_bytes(16)); // unique token
    $otp_code = random_int(100000, 999999); // 6-digit code
    $expires_at = date("Y-m-d H:i:s", time() + (15 * 60)); // 15 mins

    // Save to password_resets table
    $stmt = $conn->prepare("INSERT INTO password_resets (user_id, token, otp_code, expires_at) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $user_id, $token, $otp_code, $expires_at);
    $stmt->execute();

    $message = "Your OTP code is: $otp_code. It expires in 15 minutes.";

    // Send otp code to user's email
    try {
        sendMail($email, "Password Reset", $message, $altMessage = $message);
    } catch (Exception $e) {
        // Log the error but do not fail the registration
        error_log("Email sending failed: " . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            "status" => "warning",
            "error" => $e->getMessage(),
            "message" => "Password reset email could not be sent. Please try again later."
        ]);
        exit;
    }

    http_response_code(200);
    echo json_encode(['status' => 'success', 'message' => "Password reset link has been sent to your email"]);
} else {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => "Invalid email format"]);
}

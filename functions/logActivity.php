<?php
function logActivity($action, $uid, $user_id, $pdo)
{
    $userId = $user_id;
    $uid;
    $browser = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
    $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $url = $_SERVER['REQUEST_URI'] ?? 'unknown';
    $method = $_SERVER['REQUEST_METHOD'] ?? 'unknown';
    $referrer = $_SERVER['HTTP_REFERER'] ?? null;

    $stmt = $pdo->prepare("
        INSERT INTO activity_logs (uid, user_id, browser, ip_address, action, url, method, referrer)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $uid,
        $userId,
        $browser,
        $ipAddress,
        $action,
        $url,
        $method,
        $referrer
    ]);
}
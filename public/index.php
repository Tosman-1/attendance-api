<?php
$request = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$scriptName = dirname($_SERVER['SCRIPT_NAME']);
$basePath = rtrim($scriptName, '/');

$cleanPath = '/' . trim(str_replace($basePath, '', $request), '/');

switch ($cleanPath) {
    case "/":
        echo json_encode(["message" => "Welcome to sqi attendance web app API"]);
        break;

    case "/register":
        require __DIR__ . '/register.php';
        break;

    case "/login":
        require __DIR__ . '/login.php';
        break;

    case "/refresh-token":
        require __DIR__ . '/refreshToken.php';
        break;

    case "/refresh-access":
        require __DIR__ . '/refresh-access.php';
        break;

    case "/get-user":
        require __DIR__ . '/getUser.php';
        break;

    case "/kyc":
        require __DIR__ . '/kyc.php';
        break;


    default:
        if (str_starts_with($cleanPath, "/admin")) {
            require __DIR__ . '/admin/api.php';
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Route not found"]);
        }
        break;
}

<?php
require_once __DIR__ . '/../vendor/autoload.php';

if (file_exists(__DIR__ . '/../.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->load();
}

// Get full DATABASE_URL and SECRET_KEY from env
$databaseUrl = $_ENV['DATABASE_URL'] ?? getenv('DATABASE_URL');
$secretKey = $_ENV['SECRET_KEY'] ?? getenv('SECRET_KEY');

try {
    if (!$databaseUrl) {
        throw new Exception('DATABASE_URL not set.');
    }

    $db = parse_url($databaseUrl);

    $host = $db['host'];
    $user = $db['user'];
    $pass = $db['pass'];
    $dbname = ltrim($db['path'], '/');

    // ✅ Extract endpoint ID (first part of host)
    preg_match('/^([^.]+)/', $host, $matches);
    $endpointId = $matches[1];

    // ✅ DSN with required SSL and options passed via query string
    $dsn = "pgsql:host=$host;dbname=$dbname;sslmode=require;options=endpoint=$endpointId";

    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => "❌ Connection failed: " . $e->getMessage()]);
    exit;
}

$mySecretKey = $secretKey;


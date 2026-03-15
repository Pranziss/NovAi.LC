<?php
// =====================
// Nova Learning Center
// Database Config + Session
// =====================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host = 'localhost';
$db   = 'nova_lc';
$user = 'root';
$pass = '';  // Default XAMPP — change if you set a MySQL password

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$db;charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

// Standard headers for all API responses
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Helper: get current logged-in user_id from localStorage-based token
// Since we use localStorage on the frontend, auth is passed via POST body or header
// We accept user_id + a simple token check via session fallback
function getAuthUserId() {
    // Try PHP session first
    if (!empty($_SESSION['user_id'])) {
        return (int)$_SESSION['user_id'];
    }
    return null;
}

function requireAuth() {
    $uid = getAuthUserId();
    if (!$uid) {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized. Please log in.']);
        exit;
    }
    return $uid;
}
<?php
// =====================
// Nova Learning Center
// POST /api/auth/register.php
// Body: { username, email, password }
// Returns: { success, user: { id, username, email, is_admin } }
// =====================

require '../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$data     = json_decode(file_get_contents('php://input'), true);
$username = trim($data['username'] ?? '');
$email    = trim($data['email'] ?? '');
$password = $data['password'] ?? '';

// Validation
if (!$username || !$email || !$password) {
    http_response_code(400);
    echo json_encode(['error' => 'All fields are required']);
    exit;
}
if (strlen($username) < 3 || strlen($username) > 50) {
    http_response_code(400);
    echo json_encode(['error' => 'Username must be 3–50 characters']);
    exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid email address']);
    exit;
}
if (strlen($password) < 6) {
    http_response_code(400);
    echo json_encode(['error' => 'Password must be at least 6 characters']);
    exit;
}

// Check duplicate
$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? OR username = ? LIMIT 1");
$stmt->execute([$email, $username]);
if ($stmt->fetch()) {
    http_response_code(409);
    echo json_encode(['error' => 'Email or username is already taken']);
    exit;
}

$hash = password_hash($password, PASSWORD_BCRYPT);
$stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash, is_admin) VALUES (?, ?, ?, 0)");
$stmt->execute([$username, $email, $hash]);
$userId = (int)$pdo->lastInsertId();

$_SESSION['user_id']  = $userId;
$_SESSION['username'] = $username;
$_SESSION['is_admin'] = 0;

echo json_encode([
    'success' => true,
    'user' => [
        'id'       => $userId,
        'username' => $username,
        'email'    => $email,
        'is_admin' => false,
    ]
]);
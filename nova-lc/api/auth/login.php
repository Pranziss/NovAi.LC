<?php
// =====================
// Nova Learning Center
// POST /api/auth/login.php
// Body: { email, password }
// Returns: { success, user: { id, username, email, is_admin } }
// =====================

require '../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$data     = json_decode(file_get_contents('php://input'), true);
$email    = trim($data['email'] ?? '');
$password = $data['password'] ?? '';

if (!$email || !$password) {
    http_response_code(400);
    echo json_encode(['error' => 'Email and password are required']);
    exit;
}

$stmt = $pdo->prepare("SELECT id, username, email, password_hash, is_admin FROM users WHERE email = ? LIMIT 1");
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, $user['password_hash'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Invalid email or password']);
    exit;
}

// Store session server-side too
$_SESSION['user_id']  = $user['id'];
$_SESSION['username'] = $user['username'];
$_SESSION['is_admin'] = $user['is_admin'];

echo json_encode([
    'success' => true,
    'user' => [
        'id'       => $user['id'],
        'username' => $user['username'],
        'email'    => $user['email'],
        'is_admin' => (bool)$user['is_admin'],
    ]
]);
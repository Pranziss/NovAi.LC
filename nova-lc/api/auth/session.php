<?php
// =====================
// Nova Learning Center
// GET /api/auth/session.php
// Returns current user from session, or 401
// Used as the auth guard on protected pages
// =====================

require '../config.php';

$uid = getAuthUserId();
if (!$uid) {
    http_response_code(401);
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

// Refresh user data from DB
$stmt = $pdo->prepare("SELECT id, username, email, is_admin FROM users WHERE id = ? LIMIT 1");
$stmt->execute([$uid]);
$user = $stmt->fetch();

if (!$user) {
    session_destroy();
    http_response_code(401);
    echo json_encode(['error' => 'User not found']);
    exit;
}

echo json_encode([
    'id'       => $user['id'],
    'username' => $user['username'],
    'email'    => $user['email'],
    'is_admin' => (bool)$user['is_admin'],
]);
<?php
// =====================
// Nova Learning Center
// POST /api/admin/delete_user.php
// Body: { user_id }
// Admin only — deletes a user and all their data
// =====================

require '../config.php';
$uid = requireAuth();

if (empty($_SESSION['is_admin'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Admin access required']);
    exit;
}

$data      = json_decode(file_get_contents('php://input'), true);
$targetId  = intval($data['user_id'] ?? 0);

if (!$targetId) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing user_id']);
    exit;
}

// Prevent deleting yourself
if ($targetId === $uid) {
    http_response_code(400);
    echo json_encode(['error' => 'You cannot delete your own account']);
    exit;
}

$stmt = $pdo->prepare("SELECT id FROM users WHERE id = ? LIMIT 1");
$stmt->execute([$targetId]);
if (!$stmt->fetch()) {
    http_response_code(404);
    echo json_encode(['error' => 'User not found']);
    exit;
}

// FK CASCADE will delete their quiz_sets, questions, attempts
$stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
$stmt->execute([$targetId]);

echo json_encode(['success' => true]);
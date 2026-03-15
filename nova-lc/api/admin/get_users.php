<?php
// =====================
// Nova Learning Center
// GET /api/admin/get_users.php
// Admin only — returns all users
// =====================

require '../config.php';
$uid = requireAuth();

if (empty($_SESSION['is_admin'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Admin access required']);
    exit;
}

$stmt = $pdo->query("
    SELECT 
        u.id,
        u.username,
        u.email,
        u.is_admin,
        u.created_at,
        COUNT(DISTINCT qs.id) AS quiz_count,
        COUNT(DISTINCT qa.id) AS attempt_count
    FROM users u
    LEFT JOIN quiz_sets qs ON qs.user_id = u.id
    LEFT JOIN quiz_attempts qa ON qa.user_id = u.id
    GROUP BY u.id
    ORDER BY u.created_at DESC
");
$users = $stmt->fetchAll();

foreach ($users as &$u) {
    $u['id']            = (int)$u['id'];
    $u['is_admin']      = (bool)$u['is_admin'];
    $u['quiz_count']    = (int)$u['quiz_count'];
    $u['attempt_count'] = (int)$u['attempt_count'];
}

echo json_encode($users);
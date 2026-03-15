<?php
// =====================
// Nova Learning Center
// GET /api/admin/get_all_quizzes.php
// Admin only — returns all quiz sets from all users
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
        qs.id,
        qs.title,
        qs.subject,
        qs.is_public,
        qs.created_at,
        u.username AS creator,
        u.id AS user_id,
        COUNT(q.id) AS question_count
    FROM quiz_sets qs
    JOIN users u ON u.id = qs.user_id
    LEFT JOIN questions q ON q.quiz_set_id = qs.id
    GROUP BY qs.id
    ORDER BY qs.created_at DESC
");
$quizzes = $stmt->fetchAll();

foreach ($quizzes as &$quiz) {
    $quiz['id']             = (int)$quiz['id'];
    $quiz['user_id']        = (int)$quiz['user_id'];
    $quiz['is_public']      = (bool)$quiz['is_public'];
    $quiz['question_count'] = (int)$quiz['question_count'];
}

echo json_encode($quizzes);
<?php
// =====================
// Nova Learning Center
// GET /api/quiz/get_one.php?id=X
// Returns quiz metadata + creator username
// (no questions — use get_by_quiz.php for that)
// =====================

require '../config.php';
$uid = requireAuth();

$id = intval($_GET['id'] ?? 0);
if (!$id) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing quiz id']);
    exit;
}

$stmt = $pdo->prepare("
    SELECT 
        qs.id,
        qs.user_id,
        qs.title,
        qs.description,
        qs.subject,
        qs.is_public,
        qs.created_at,
        u.username AS creator,
        COUNT(q.id) AS question_count
    FROM quiz_sets qs
    JOIN users u ON u.id = qs.user_id
    LEFT JOIN questions q ON q.quiz_set_id = qs.id
    WHERE qs.id = ?
    GROUP BY qs.id
    LIMIT 1
");
$stmt->execute([$id]);
$quiz = $stmt->fetch();

if (!$quiz) {
    http_response_code(404);
    echo json_encode(['error' => 'Quiz not found']);
    exit;
}

// Block access to private quizzes from non-owners
if (!$quiz['is_public'] && $quiz['user_id'] !== $uid) {
    http_response_code(403);
    echo json_encode(['error' => 'This quiz is private']);
    exit;
}

$quiz['id']             = (int)$quiz['id'];
$quiz['user_id']        = (int)$quiz['user_id'];
$quiz['is_public']      = (bool)$quiz['is_public'];
$quiz['question_count'] = (int)$quiz['question_count'];
$quiz['is_owner']       = ($quiz['user_id'] === $uid);

echo json_encode($quiz);
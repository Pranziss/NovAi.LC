<?php
// =====================
// Nova Learning Center
// GET /api/quiz/get_mine.php
// Returns all quiz sets created by the logged-in user
// =====================

require '../config.php';
$uid = requireAuth();

$stmt = $pdo->prepare("
    SELECT 
        qs.id,
        qs.title,
        qs.description,
        qs.subject,
        qs.is_public,
        qs.created_at,
        COUNT(q.id) AS question_count
    FROM quiz_sets qs
    LEFT JOIN questions q ON q.quiz_set_id = qs.id
    WHERE qs.user_id = ?
    GROUP BY qs.id
    ORDER BY qs.created_at DESC
");
$stmt->execute([$uid]);
$quizzes = $stmt->fetchAll();

// Cast types
foreach ($quizzes as &$quiz) {
    $quiz['id']             = (int)$quiz['id'];
    $quiz['is_public']      = (bool)$quiz['is_public'];
    $quiz['question_count'] = (int)$quiz['question_count'];
}

echo json_encode($quizzes);
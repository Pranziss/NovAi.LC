<?php
// =====================
// Nova Learning Center
// GET /api/quiz/get_public.php?subject=&search=
// Returns all public quiz sets for the Discover page
// =====================

require '../config.php';
$uid = requireAuth();

$subject = trim($_GET['subject'] ?? '');
$search  = trim($_GET['search'] ?? '');

$params = [];
$where  = ["qs.is_public = 1"];

if ($subject) {
    $where[]  = "qs.subject = ?";
    $params[] = $subject;
}
if ($search) {
    $where[]  = "(qs.title LIKE ? OR qs.description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$whereSQL = implode(' AND ', $where);

$stmt = $pdo->prepare("
    SELECT 
        qs.id,
        qs.title,
        qs.description,
        qs.subject,
        qs.created_at,
        u.username AS creator,
        COUNT(q.id) AS question_count
    FROM quiz_sets qs
    JOIN users u ON u.id = qs.user_id
    LEFT JOIN questions q ON q.quiz_set_id = qs.id
    WHERE $whereSQL
    GROUP BY qs.id
    ORDER BY qs.created_at DESC
");
$stmt->execute($params);
$quizzes = $stmt->fetchAll();

foreach ($quizzes as &$quiz) {
    $quiz['id']             = (int)$quiz['id'];
    $quiz['question_count'] = (int)$quiz['question_count'];
}

echo json_encode($quizzes);
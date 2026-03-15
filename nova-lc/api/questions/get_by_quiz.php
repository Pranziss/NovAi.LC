<?php
// =====================
// Nova Learning Center
// GET /api/questions/get_by_quiz.php?id=X
// Returns all questions for a given quiz_set_id
// Used by quiz-detail.html and quiz-play.html
// =====================

require '../config.php';
$uid = requireAuth();

$quizId = intval($_GET['id'] ?? 0);
if (!$quizId) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing quiz id']);
    exit;
}

// Check the quiz exists and user has access
$stmt = $pdo->prepare("SELECT user_id, is_public FROM quiz_sets WHERE id = ? LIMIT 1");
$stmt->execute([$quizId]);
$quiz = $stmt->fetch();

if (!$quiz) {
    http_response_code(404);
    echo json_encode(['error' => 'Quiz not found']);
    exit;
}

if (!$quiz['is_public'] && (int)$quiz['user_id'] !== $uid) {
    http_response_code(403);
    echo json_encode(['error' => 'Access denied']);
    exit;
}

$stmt = $pdo->prepare("
    SELECT id, question_text, choice_a, choice_b, choice_c, choice_d, correct_index
    FROM questions
    WHERE quiz_set_id = ?
    ORDER BY id ASC
");
$stmt->execute([$quizId]);
$questions = $stmt->fetchAll();

// Format choices as an array for the frontend
$result = array_map(function($q) {
    return [
        'id'            => (int)$q['id'],
        'question_text' => $q['question_text'],
        'choices'       => [$q['choice_a'], $q['choice_b'], $q['choice_c'], $q['choice_d']],
        'correct_index' => (int)$q['correct_index'],
    ];
}, $questions);

echo json_encode($result);
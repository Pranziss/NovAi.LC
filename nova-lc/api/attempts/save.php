<?php
// =====================
// Nova Learning Center
// POST /api/attempts/save.php
// Body: { quiz_id, score, total, answers: [0,2,1,3,...] }
// Saves attempt and returns per-question result breakdown
// =====================

require '../config.php';
$uid = requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$data    = json_decode(file_get_contents('php://input'), true);
$quizId  = intval($data['quiz_id'] ?? 0);
$score   = intval($data['score'] ?? 0);
$total   = intval($data['total'] ?? 0);
$answers = $data['answers'] ?? []; // array of chosen indices (int), indexed by question order

if (!$quizId || $total <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid attempt data']);
    exit;
}

// Verify quiz exists
$stmt = $pdo->prepare("SELECT id FROM quiz_sets WHERE id = ? LIMIT 1");
$stmt->execute([$quizId]);
if (!$stmt->fetch()) {
    http_response_code(404);
    echo json_encode(['error' => 'Quiz not found']);
    exit;
}

// Save attempt record
$stmt = $pdo->prepare(
    "INSERT INTO quiz_attempts (user_id, quiz_set_id, score, total) VALUES (?,?,?,?)"
);
$stmt->execute([$uid, $quizId, $score, $total]);
$attemptId = (int)$pdo->lastInsertId();

// Fetch questions to build result breakdown
$stmt = $pdo->prepare(
    "SELECT id, question_text, choice_a, choice_b, choice_c, choice_d, correct_index
     FROM questions WHERE quiz_set_id = ? ORDER BY id ASC"
);
$stmt->execute([$quizId]);
$questions = $stmt->fetchAll();

$results = [];
foreach ($questions as $i => $q) {
    $chosen  = isset($answers[$i]) ? intval($answers[$i]) : -1;
    $correct = $chosen === (int)$q['correct_index'];
    $results[] = [
        'question'       => $q['question_text'],
        'choices'        => [$q['choice_a'], $q['choice_b'], $q['choice_c'], $q['choice_d']],
        'correct_index'  => (int)$q['correct_index'],
        'chosen_index'   => $chosen,
        'is_correct'     => $correct,
    ];
}

echo json_encode([
    'success'    => true,
    'attempt_id' => $attemptId,
    'score'      => $score,
    'total'      => $total,
    'percent'    => $total > 0 ? round($score / $total * 100) : 0,
    'results'    => $results,
]);
<?php
// =====================
// Nova Learning Center
// POST /api/quiz/create.php
// Body: { title, description, subject, is_public, questions: [{question_text, choices:[A,B,C,D], correct_index}] }
// Returns: { success, quiz_id }
// =====================

require '../config.php';
$uid = requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$data      = json_decode(file_get_contents('php://input'), true);
$title     = trim($data['title'] ?? '');
$desc      = trim($data['description'] ?? '');
$subject   = trim($data['subject'] ?? '');
$isPublic  = isset($data['is_public']) ? (int)(bool)$data['is_public'] : 1;
$questions = $data['questions'] ?? [];

if (!$title) {
    http_response_code(400);
    echo json_encode(['error' => 'Quiz title is required']);
    exit;
}
if (count($questions) === 0) {
    http_response_code(400);
    echo json_encode(['error' => 'At least one question is required']);
    exit;
}

// Validate questions
foreach ($questions as $i => $q) {
    if (empty($q['question_text'])) {
        echo json_encode(['error' => "Question " . ($i+1) . " is missing its text"]); exit;
    }
    if (count($q['choices'] ?? []) !== 4) {
        echo json_encode(['error' => "Question " . ($i+1) . " must have exactly 4 choices"]); exit;
    }
    if (!isset($q['correct_index']) || $q['correct_index'] < 0 || $q['correct_index'] > 3) {
        echo json_encode(['error' => "Question " . ($i+1) . " has an invalid correct answer"]); exit;
    }
}

// Insert quiz set
$stmt = $pdo->prepare(
    "INSERT INTO quiz_sets (user_id, title, description, subject, is_public) VALUES (?,?,?,?,?)"
);
$stmt->execute([$uid, $title, $desc, $subject, $isPublic]);
$quizId = (int)$pdo->lastInsertId();

// Insert questions
$qStmt = $pdo->prepare(
    "INSERT INTO questions (quiz_set_id, question_text, choice_a, choice_b, choice_c, choice_d, correct_index)
     VALUES (?,?,?,?,?,?,?)"
);
foreach ($questions as $q) {
    $qStmt->execute([
        $quizId,
        $q['question_text'],
        $q['choices'][0],
        $q['choices'][1],
        $q['choices'][2],
        $q['choices'][3],
        (int)$q['correct_index'],
    ]);
}

echo json_encode(['success' => true, 'quiz_id' => $quizId]);
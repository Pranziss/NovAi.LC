<?php
// =====================
// Nova Learning Center
// DELETE /api/quiz/delete.php
// Body: { quiz_id }
// Only the owner OR an admin can delete
// =====================

require '../config.php';
$uid = requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$data   = json_decode(file_get_contents('php://input'), true);
$quizId = intval($data['quiz_id'] ?? 0);

if (!$quizId) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing quiz_id']);
    exit;
}

// Fetch the quiz
$stmt = $pdo->prepare("SELECT user_id FROM quiz_sets WHERE id = ? LIMIT 1");
$stmt->execute([$quizId]);
$quiz = $stmt->fetch();

if (!$quiz) {
    http_response_code(404);
    echo json_encode(['error' => 'Quiz not found']);
    exit;
}

$isAdmin = !empty($_SESSION['is_admin']);
if ($quiz['user_id'] !== $uid && !$isAdmin) {
    http_response_code(403);
    echo json_encode(['error' => 'You do not have permission to delete this quiz']);
    exit;
}

// Cascade deletes questions + attempts via FK ON DELETE CASCADE
$stmt = $pdo->prepare("DELETE FROM quiz_sets WHERE id = ?");
$stmt->execute([$quizId]);

echo json_encode(['success' => true]);
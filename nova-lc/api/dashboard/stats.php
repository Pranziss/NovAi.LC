<?php
// =====================
// Nova Learning Center
// GET /api/dashboard/stats.php
// Returns: { quiz_count, taken, avg_score, best_score }
// =====================

require '../config.php';
$uid = requireAuth();

// How many quizzes this user created
$stmt = $pdo->prepare("SELECT COUNT(*) as cnt FROM quiz_sets WHERE user_id = ?");
$stmt->execute([$uid]);
$quizCount = (int)$stmt->fetchColumn();

// How many quiz attempts this user has taken
$stmt = $pdo->prepare("SELECT COUNT(*) as cnt FROM quiz_attempts WHERE user_id = ?");
$stmt->execute([$uid]);
$taken = (int)$stmt->fetchColumn();

// Average score percentage across all attempts
$stmt = $pdo->prepare("
    SELECT 
        ROUND(AVG(score / total * 100)) as avg_score,
        MAX(score / total * 100) as best_score
    FROM quiz_attempts 
    WHERE user_id = ? AND total > 0
");
$stmt->execute([$uid]);
$scores = $stmt->fetch();

echo json_encode([
    'quiz_count' => $quizCount,
    'taken'      => $taken,
    'avg_score'  => $scores['avg_score'] ? (int)$scores['avg_score'] : 0,
    'best_score' => $scores['best_score'] ? (int)$scores['best_score'] : 0,
]);
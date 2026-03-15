<?php
// =====================
// Nova Learning Center
// POST /api/auth/logout.php
// Destroys server session
// =====================

require '../config.php';

session_unset();
session_destroy();

echo json_encode(['success' => true]);
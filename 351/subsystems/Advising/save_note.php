<?php
session_start();
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../includes/dbconnect.php';
require_role(['professor']);

$student_id   = isset($_POST['student_id'])   ? (int)$_POST['student_id']          : 0;
$professor_id = isset($_POST['professor_id']) ? (int)$_POST['professor_id']         : 0;
$grad         = isset($_POST['grad'])         ? trim($_POST['grad'])                : '';
$classes      = isset($_POST['classes'])      ? trim($_POST['classes'])             : '';

$stmt = $pdo->prepare(
    "INSERT INTO StudentNote (StudentID, ProfessorID, ExpectedGrad, Classes)
     VALUES (?, ?, ?, ?)"
);
$stmt->execute([$student_id, $professor_id, $grad, $classes]);

header("Location: student_notes.php?student_id=" . $student_id);
exit;
?>

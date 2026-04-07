<?php
session_start();
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../includes/dbconnect.php';
require_role(['professor']);

$note_id      = isset($_POST['note_id'])      ? (int)$_POST['note_id']      : 0;
$student_name = isset($_POST['student_name']) ? trim($_POST['student_name']) : '';
$grad         = isset($_POST['grad'])         ? trim($_POST['grad'])         : '';
$classes      = isset($_POST['classes'])      ? trim($_POST['classes'])      : '';

$stmt = $pdo->prepare(
    "UPDATE StudentNote
        SET StudentName = ?, StudentExpectedGrad = ?, StudentClasses = ?
      WHERE NoteID = ?"
);
$stmt->execute([$student_name, $grad, $classes, $note_id]);

// Look up the StudentID so we can redirect back to the right student's notes
$row = $pdo->prepare("SELECT StudentID FROM StudentNote WHERE NoteID = ?");
$row->execute([$note_id]);
$note = $row->fetch(PDO::FETCH_ASSOC);

$student_id = $note ? (int)$note['StudentID'] : 0;

header("Location: student_notes.php?student_id=" . $student_id);
exit;
?>

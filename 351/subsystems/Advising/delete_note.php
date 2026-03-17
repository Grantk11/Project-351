<?php
session_start();
require "../../includes/DB_Connect.php";

$note_id    = isset($_GET['note_id'])    ? intval($_GET['note_id'])    : 0;
$student_id = isset($_GET['student_id']) ? intval($_GET['student_id']) : 0;

$stmt = $pdo->prepare("DELETE FROM StudentNote WHERE NoteID = ?");
$stmt->execute([$note_id]);

header("Location: student_notes.php?student_id=" . $student_id);
exit;
?>
<?php
session_start();
require "DB_Connect.php"; 

$stmt = $pdo->prepare("
UPDATE StudentNote
SET StudentName=?, StudentExpectedGrad=?, StudentClasses=?
WHERE NoteID=?
");

$stmt->execute([
    $_POST['student_name'],
    $_POST['grad'],
    $_POST['classes'],
    $_POST['note_id']
]);

header("Location: student_notes.php?student_id=1"); // optional: redirect to student view
?>
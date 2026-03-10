<?php
session_start();
require "dbconnect.php";  

$stmt = $pdo->prepare("
INSERT INTO StudentNote
(StudentID, ProfessorID, StudentExpectedGrad, StudentClasses, StudentName)
VALUES (?, ?, ?, ?, ?)
");

$stmt->execute([
    $_POST['student_id'],
    $_POST['professor_id'],
    $_POST['grad'],
    $_POST['classes'],
    $_POST['student_name']
]);

header("Location: student_notes.php?student_id=" . $_POST['student_id']);
?>
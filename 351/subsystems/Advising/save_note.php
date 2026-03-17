<?php
session_start();
require "../../DB_Connect.php";


$student_id   = isset($_POST['student_id'])    ? intval($_POST['student_id'])          : 0;
$professor_id = isset($_POST['professor_id'])  ? intval($_POST['professor_id'])         : 0;
$grad         = isset($_POST['grad'])          ? trim($_POST['grad'])                   : '';
$classes      = isset($_POST['classes'])       ? trim($_POST['classes'])                : '';
$student_name = isset($_POST['student_name'])  ? trim($_POST['student_name'])           : '';

$check = mysqli_prepare($conn, "SELECT StudentID FROM student WHERE StudentID = ?");
mysqli_stmt_bind_param($check, "i", $student_id);
mysqli_stmt_execute($check);
mysqli_stmt_store_result($check);

if (mysqli_stmt_num_rows($check) === 0) {
    die("Error: StudentID $student_id does not exist in the student table.");
}
mysqli_stmt_close($check);


$check2 = mysqli_prepare($conn, "SELECT ProfessorID FROM professor WHERE ProfessorID = ?");
mysqli_stmt_bind_param($check2, "i", $professor_id);
mysqli_stmt_execute($check2);
mysqli_stmt_store_result($check2);

if (mysqli_stmt_num_rows($check2) === 0) {
    die("Error: ProfessorID $professor_id does not exist in the professor table.");
}

$stmt = mysqli_prepare($conn, "
    INSERT INTO StudentNote (StudentID, ProfessorID, StudentExpectedGrad, StudentClasses, StudentName)
    VALUES (?, ?, ?, ?, ?)
");

mysqli_stmt_bind_param($stmt, "iisss", $student_id, $professor_id, $grad, $classes, $student_name);

if (mysqli_stmt_execute($stmt)) {
    header("Location: student_notes.php?student_id=" . $student_id);
    exit;
} else {
    die("Insert failed: " . mysqli_stmt_error($stmt));
}
?>
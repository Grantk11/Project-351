<?php
session_start();
require "../../includes/dbconnect.php";

$student_id       = isset($_POST['student_id'])       ? intval($_POST['student_id'])       : 0;
$professor_id     = isset($_POST['professor_id'])     ? intval($_POST['professor_id'])     : 0;
$meeting_name     = isset($_POST['meeting_name'])     ? trim($_POST['meeting_name'])       : '';
$meeting_location = isset($_POST['meeting_location']) ? trim($_POST['meeting_location'])   : '';
$meeting_date     = isset($_POST['meeting_date'])     ? trim($_POST['meeting_date'])       : '';

$check = mysqli_prepare($conn, "SELECT StudentID FROM student WHERE StudentID = ?");
mysqli_stmt_bind_param($check, "i", $student_id);
mysqli_stmt_execute($check);
mysqli_stmt_store_result($check);
if (mysqli_stmt_num_rows($check) === 0) {
    die("Error: StudentID $student_id does not exist.");
}
mysqli_stmt_close($check);

$check2 = mysqli_prepare($conn, "SELECT ProfessorID FROM professor WHERE ProfessorID = ?");
mysqli_stmt_bind_param($check2, "i", $professor_id);
mysqli_stmt_execute($check2);
mysqli_stmt_store_result($check2);
if (mysqli_stmt_num_rows($check2) === 0) {
    die("Error: ProfessorID $professor_id does not exist.");
}
mysqli_stmt_close($check2);

$stmt = mysqli_prepare($conn, "
    INSERT INTO meeting (StudentID, ProfessorID, MeetingName, MeetingLocation, MeetingDate)
    VALUES (?, ?, ?, ?, ?)
");
mysqli_stmt_bind_param($stmt, "iisss", $student_id, $professor_id, $meeting_name, $meeting_location, $meeting_date);

if (mysqli_stmt_execute($stmt)) {
    header("Location: view_meetings.php?student_id=" . $student_id);
    exit;
} else {
    die("Insert failed: " . mysqli_stmt_error($stmt));
}
?>

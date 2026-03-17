<?php
session_start();
require "../../DB_Connect.php";

$meeting_id = isset($_GET['meeting_id']) ? intval($_GET['meeting_id']) : 0;
$student_id = isset($_GET['student_id']) ? intval($_GET['student_id']) : 0;

$stmt = mysqli_prepare($conn, "DELETE FROM Meeting WHERE MeetingID = ?");
mysqli_stmt_bind_param($stmt, "i", $meeting_id);

if (mysqli_stmt_execute($stmt)) {
    header("Location: view_meetings.php?student_id=" . $student_id);
    exit;
} else {
    die("Delete failed: " . mysqli_stmt_error($stmt));
}
?>

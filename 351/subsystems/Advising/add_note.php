<?php
session_start();
require "dbconnect.php";

$studentID = $_GET['student_id'];
?>

<form action="save_note.php" method="POST">

<input type="hidden" name="student_id" value="<?= $studentID ?>">
<input type="hidden" name="professor_id" value="<?= $_SESSION['user_id'] ?>">

<label>Student Name:</label>
<input type="text" name="student_name">

<label>Expected Graduation:</label>
<input type="text" name="grad">

<label>Classes:</label>
<input type="text" name="classes">

<button type="submit">Save Note</button>

</form>
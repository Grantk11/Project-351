<html>
<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

?>

<html>
<head>
<title>Advising Subsystem</title>
<link rel="stylesheet" href="../../351.css">
</head>

<body>

<h1>Advising Subsystem</h1>

<div class="cards">

<div class="card">
<h3>Student Notes</h3>
<p>View and manage advising notes</p>
<a href="student_notes.php?student_id=1">Open Module</a>
</div>

<div class="card">
<h3>Advising Meetings</h3>
<p>Schedule advising appointments</p>
<a href="meetings.php">Open Module</a>
</div>

</div>

</body>
</html>

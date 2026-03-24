<?php
session_start();
require "../../includes/dbconnect.php";

$studentID = $_GET['student_id'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule Meeting</title>
    <link rel="stylesheet" href="../../351.css">
</head>
<body>

    <div id="wrapper">

<header>
<h1><a href="add_meeting.php">Schedule A Meeting</a></h1>
</header>

<div class="site-logo">
<img src="../../ban.png" alt="CNU Banner">
</div>

<nav>
<ul>
<li><a href="advising_index.php">Back to Advising</a></li>
<li><a href="../../logout.php">Logout</a></li>
</ul>
</nav>
        
<div style="display: block; margin-bottom: 10px;">
    <a href="advising_index.php">Back to Advising</a>
</div>

<div id="wrapper">

    <header>
        <h1>Schedule an Advising Meeting</h1>
    </header>

    <main>

        <form action="save_meeting.php" method="POST">

            <input type="hidden" name="student_id"   value="<?= htmlspecialchars($studentID) ?>">
            <input type="hidden" name="professor_id" value="<?= htmlspecialchars($_SESSION['user_id']) ?>">

            <label>Meeting Name:</label>
            <input type="text" name="meeting_name" required>

            <label>Meeting Location:</label>
            <input type="text" name="meeting_location" required>

            <label>Meeting Date:</label>
            <input type="date" name="meeting_date" required>

            <button type="submit">Schedule Meeting</button>

        </form>

        <br>
        <a href="view_meetings.php?student_id=<?= htmlspecialchars($studentID) ?>">View All Meetings for This Student</a>

    </main>

</div>

</body>
</html>

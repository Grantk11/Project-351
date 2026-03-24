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

<div id="wrapper">

<header>
<h1><a href="dashboard.php">Advising Dashboard</a></h1>
</header>

<div class="site-logo">
<img src="../../ban.png" alt="CNU Banner">
</div>

<nav>
<ul>
<li><a href="../../dashboard.php">Back to Portal</a></li>
<li><a href="../../logout.php">Logout</a></li>
</ul>
</nav>

<div class="cards">

<div class="card">
<h3>Student Notes</h3>
<p>View and manage advising notes</p>
<a href="student_notes.php?student_id=1">Open Module</a>
</div>

<div class="card">
<h3>Advising Meetings</h3>
<p>Schedule advising appointments</p>
<a href="view_meetings.php?student_id=1">Open Module</a>
</div>

</div>

</body>
</html>

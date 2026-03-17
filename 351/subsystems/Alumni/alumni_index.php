<html>
<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

?>

<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>351</title>
    <link rel="stylesheet" href="../../351.css">
</head>
<body>

<div id="wrapper">

<header>
<h1><a href="alumni_index.php">Alumni Connection Dashboard</a></h1>
</header>

<div class="site-logo">
<img src="../../ban.png" alt="CNU Banner">
</div>

<nav>
<ul>
<li><a href="../../dashboard.php">Back to Portal</a></li>
<li><a href="alumni.php">Alumni</a></li>
<li><a href="student.php">Student</a></li>
<li><a href="../../logout.php">Logout</a></li>
</ul>
</nav>





</html>
<?php
session_start();
require "../../includes/dbconnect.php";  

$id = $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM StudentNote WHERE NoteID=?");
$stmt->execute([$id]);

$note = $stmt->fetch();
?>
<html>
  <head>
    <link rel="stylesheet" href="../../351.css">
  </head>
  <body>
    <div id="wrapper">

<header>
<h1><a href="edit_note.php">Edit Note</a></h1>
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
<form action="update_note.php" method="POST">

<input type="hidden" name="note_id" value="<?= $note['NoteID'] ?>">

<label>Student Name:</label>
<input type="text" name="student_name" value="<?= $note['StudentName'] ?>">

<label>Expected Graduation:</label>
<input type="text" name="grad" value="<?= $note['StudentExpectedGrad'] ?>">

<label>Classes:</label>
<input type="text" name="classes" value="<?= $note['StudentClasses'] ?>">

<button>Update Note</button>

</form>

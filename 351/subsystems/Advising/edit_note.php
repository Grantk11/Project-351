<?php
session_start();
require "DB_Connect.php";  

$id = $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM StudentNote WHERE NoteID=?");
$stmt->execute([$id]);

$note = $stmt->fetch();
?>

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
<?php
session_start();
require("../../includes/dbconnect.php");

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$studentID = $_GET['student_id'];

$stmt = $pdo->prepare(
    "SELECT * FROM StudentNote WHERE StudentID=?"
);
$stmt->execute([$studentID]);

$notes = $stmt->fetchAll();
?>

<html>
<head>
<title>Student Notes</title>
<link rel="stylesheet" href="../../351.css">
</head>

<body>
<div id="wrapper">

<header>
<h1><a href="student_notes.php">Student Advising Notes</a></h1>
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

<a href="add_note.php?student_id=<?= $studentID ?>">Add Note</a>

<hr>

<?php foreach ($notes as $note): ?>
<div>

<p><b>Name:</b> <?= $note['StudentName'] ?></p>
<p><b>Expected Grad:</b> <?= $note['StudentExpectedGrad'] ?></p>
<p><b>Classes:</b> <?= $note['StudentClasses'] ?></p>

<a href="edit_note.php?id=<?= $note['NoteID'] ?>">Edit</a>

<a href="delete_note.php?note_id=<?= $note['NoteID'] ?>&student_id=<?= $studentID ?>"
   onclick="return confirm('Delete this note?')">Delete</a>

<hr>

</div>
<?php endforeach; ?>

</div>
</body>

</html>

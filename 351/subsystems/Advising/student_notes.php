<?php
session_start();
require("../../includes/DB_Connect.php");

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
<h1>Student Advising Notes</h1>
</header>

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

<nav>
  <ul>
    <li><a href="../../dashboard.php">Dashboard</a></li>
    <li><a href="../../my_profile.php">My Profile</a></li>
    <li><a href="../../logout.php">Logout</a></li>
  </ul>
</nav>

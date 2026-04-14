<?php
session_start();
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../includes/dbconnect.php';
require_role(['professor', 'student']);

$role = $_SESSION['role'];

// Students always see their own notes; professors pass student_id via GET
if ($role === 'student') {
    $student_id = (int)$_SESSION['user_id'];
} else {
    $student_id = isset($_GET['student_id']) ? (int)$_GET['student_id'] : 0;
}

// Fetch student name for heading
$sStmt = $pdo->prepare("SELECT first_name, last_name FROM users WHERE id = ?");
$sStmt->execute([$student_id]);
$student = $sStmt->fetch(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("SELECT * FROM StudentNote WHERE StudentID = ? ORDER BY NoteID DESC");
$stmt->execute([$student_id]);
$notes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Notes</title>
    <link rel="stylesheet" href="../../351.css">
    <style>
        .module-header { margin-bottom: 1.5rem; }
        .module-header h2 { margin-bottom: .25rem; }
        .module-header p  { color: #555; margin: 0; }

        .card {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 1.25rem 1.5rem;
            margin-bottom: 2rem;
        }
        .card h3 { margin-top: 0; }

        table { width: 100%; border-collapse: collapse; font-size: .92rem; }
        th, td { padding: .55rem .75rem; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #004a8f; color: #fff; }
        tr:hover td { background: #f0f4ff; }

        .btn {
            display: inline-block;
            padding: .35rem .85rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: .85rem;
            font-weight: 600;
            text-decoration: none;
        }
        .btn-primary  { background: #004a8f; color: #fff; }
        .btn-primary:hover  { background: #003366; }
        .btn-warning  { background: #e67e22; color: #fff; }
        .btn-warning:hover  { background: #ca6f1e; }
        .btn-danger   { background: #c0392b; color: #fff; }
        .btn-danger:hover   { background: #922b21; }

        .action-row { display: flex; gap: .5rem; }
        .empty-msg  { color: #777; font-style: italic; }

        .read-only-badge {
            display: inline-block;
            background: #e8f4fd;
            color: #1a6fa8;
            border: 1px solid #b8d9f0;
            border-radius: 4px;
            padding: .2rem .65rem;
            font-size: .78rem;
            font-weight: 600;
            letter-spacing: .03em;
            vertical-align: middle;
            margin-left: .5rem;
        }
    </style>
</head>
<body>
<div id="wrapper">

    <header>
        <h1><a href="../../dashboard.php">CNU Portal</a></h1>
    </header>

    <div class="site-logo">
        <img src="../../ban.png" alt="CNU Banner">
    </div>

    <nav>
        <ul>
            <li><a href="advising_index.php">Advising Home</a></li>
            <li><a href="../../dashboard.php">Back to Portal</a></li>
            <li><a href="../../logout.php">Logout</a></li>
        </ul>
    </nav>

    <main style="max-width:900px;margin:2rem auto;padding:0 1rem;">

        <div class="module-header">
            <h2>
                <?= $role === 'student' ? 'My Advising Notes' : 'Student Advising Notes' ?>
            </h2>
            <?php if ($student): ?>
                <p>
                    <?= $role === 'professor' ? 'Viewing notes for ' : '' ?>
                    <strong><?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?></strong>
                    <?php if ($role === 'student'): ?>
                        <span class="read-only-badge">Read Only</span>
                    <?php endif; ?>
                </p>
            <?php endif; ?>
        </div>

        <div class="card">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;">
                <h3 style="margin:0;">Notes</h3>
                <?php if ($role === 'professor'): ?>
                    <a href="add_note.php?student_id=<?= $student_id ?>" class="btn btn-primary">+ Add Note</a>
                <?php endif; ?>
            </div>

            <?php if (empty($notes)): ?>
                <p class="empty-msg">
                    <?= $role === 'student'
                        ? 'Your professor has not added any notes for you yet.'
                        : 'No notes recorded for this student yet.' ?>
                </p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Expected Graduation</th>
                            <th>Classes</th>
                            <?php if ($role === 'professor'): ?>
                                <th>Actions</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($notes as $note): ?>
                        <tr>
                            <td><?= htmlspecialchars($note['StudentName']) ?></td>
                            <td><?= htmlspecialchars($note['StudentExpectedGrad']) ?></td>
                            <td><?= htmlspecialchars($note['StudentClasses']) ?></td>
                            <?php if ($role === 'professor'): ?>
                                <td>
                                    <div class="action-row">
                                        <a href="edit_note.php?id=<?= (int)$note['NoteID'] ?>" class="btn btn-warning">Edit</a>
                                        <a href="delete_note.php?note_id=<?= (int)$note['NoteID'] ?>&student_id=<?= $student_id ?>"
                                           class="btn btn-danger"
                                           onclick="return confirm('Delete this note?')">Delete</a>
                                    </div>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

    </main>

    <footer>
        <br>sam.kirkhorn.23@cnu.edu
        <br>grant.kochan.23@cnu.edu
        <br>alijah.levenberry.23@cnu.edu
        <br>colin.anderson.22@cnu.edu
    </footer>

</div>
</body>
</html>

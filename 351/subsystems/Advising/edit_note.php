<?php
/*  Responsible Party: Alijah */
session_start();
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../includes/dbconnect.php';
require_role(['professor']);

$note_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("SELECT * FROM StudentNote WHERE NoteID = ?");
$stmt->execute([$note_id]);
$note = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$note) {
    header("Location: advising_index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Note</title>
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

        .form-group { margin-bottom: 1rem; }
        label {
            display: block;
            font-weight: 600;
            font-size: .9rem;
            margin-bottom: .3rem;
        }
        input[type="text"] {
            width: 100%;
            padding: .45rem .65rem;
            border: 1px solid #bbb;
            border-radius: 4px;
            font-size: .95rem;
            box-sizing: border-box;
        }
        input[type="text"]:focus {
            outline: 2px solid #004a8f;
            border-color: #004a8f;
        }

        .btn {
            display: inline-block;
            padding: .5rem 1.2rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: .95rem;
            font-weight: 600;
            text-decoration: none;
        }
        .btn-primary   { background: #004a8f; color: #fff; }
        .btn-primary:hover   { background: #003366; }
        .btn-secondary { background: #e2e8f0; color: #333; }
        .btn-secondary:hover { background: #cbd5e0; }

        .form-actions { display: flex; gap: .75rem; margin-top: 1.25rem; }
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
            <li><a href="student_notes.php?student_id=<?= (int)$note['StudentID'] ?>">Back to Notes</a></li>
            <li><a href="advising_index.php">Advising Home</a></li>
            <li><a href="../../logout.php">Logout</a></li>
        </ul>
    </nav>

    <main style="max-width:600px;margin:2rem auto;padding:0 1rem;">

        <div class="module-header">
            <h2>Edit Advising Note</h2>
            <p>Update the note for <strong><?= htmlspecialchars($note['StudentName']) ?></strong></p>
        </div>

        <div class="card">
            <form action="update_note.php" method="POST">

                <input type="hidden" name="note_id" value="<?= (int)$note['NoteID'] ?>">

                <div class="form-group">
                    <label for="student_name">Student Name</label>
                    <input type="text" id="student_name" name="student_name"
                           value="<?= htmlspecialchars($note['StudentName']) ?>">
                </div>

                <div class="form-group">
                    <label for="grad">Expected Graduation</label>
                    <input type="text" id="grad" name="grad"
                           value="<?= htmlspecialchars($note['StudentExpectedGrad']) ?>">
                </div>

                <div class="form-group">
                    <label for="classes">Classes</label>
                    <input type="text" id="classes" name="classes"
                           value="<?= htmlspecialchars($note['StudentClasses']) ?>">
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Update Note</button>
                    <a href="student_notes.php?student_id=<?= (int)$note['StudentID'] ?>" class="btn btn-secondary">Cancel</a>
                </div>

            </form>
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

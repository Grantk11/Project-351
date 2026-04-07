<?php
session_start();
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../includes/dbconnect.php';
require_login();

$role = $_SESSION['role'] ?? '';

// Fetch student list for professor note-lookup dropdown
$students = [];
if ($role === 'professor') {
    $stmt = $pdo->query("SELECT id, first_name, last_name FROM users WHERE Position = 'student' ORDER BY last_name, first_name");
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Advising</title>
    <link rel="stylesheet" href="../../351.css">
    <style>
        .adv-hero {
            text-align: center;
            padding: 2rem 1rem 1rem;
        }
        .adv-hero h2 {
            font-size: 1.7rem;
            margin-bottom: .25rem;
        }
        .adv-hero p {
            color: #555;
            margin: 0;
        }

        .adv-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            max-width: 860px;
            margin: 2rem auto;
            padding: 0 1.25rem;
        }

        .adv-card {
            background: #fff;
            border: 1px solid #dde3ec;
            border-radius: 10px;
            padding: 1.75rem 1.5rem 1.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,.06);
            display: flex;
            flex-direction: column;
            gap: .75rem;
        }
        .adv-card .card-icon {
            font-size: 2rem;
            line-height: 1;
        }
        .adv-card h3 {
            margin: 0;
            font-size: 1.1rem;
            color: #1a1a2e;
        }
        .adv-card p {
            margin: 0;
            font-size: .9rem;
            color: #555;
            flex: 1;
        }

        .btn-card {
            display: inline-block;
            padding: .55rem 1.2rem;
            background: #004a8f;
            color: #fff;
            border-radius: 5px;
            text-decoration: none;
            font-size: .9rem;
            font-weight: 600;
            text-align: center;
            border: none;
            cursor: pointer;
            transition: background .2s;
        }
        .btn-card:hover { background: #003366; }

        /* Student picker inside the notes card */
        .student-picker {
            display: flex;
            flex-direction: column;
            gap: .5rem;
            margin-top: .25rem;
        }
        .student-picker select {
            width: 100%;
            padding: .45rem .6rem;
            border: 1px solid #bbb;
            border-radius: 4px;
            font-size: .88rem;
            background: #fafafa;
        }
        .student-picker select:focus {
            outline: 2px solid #004a8f;
            border-color: #004a8f;
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
            <li><a href="../../dashboard.php">Back to Portal</a></li>
            <li><a href="../../logout.php">Logout</a></li>
        </ul>
    </nav>

    <div class="adv-hero">
        <h2>Course Advising</h2>
        <p>
            <?php if ($role === 'professor'): ?>
                Manage your advising slots and student notes.
            <?php else: ?>
                View your advising appointments and resources.
            <?php endif; ?>
        </p>
    </div>

    <div class="adv-grid">

        <?php if ($role === 'professor'): ?>

            <!-- ── Advising Slots ── -->
            <div class="adv-card">
                <div class="card-icon">&#128197;</div>
                <h3>Advising Slots</h3>
                <p>Create open time slots that students can sign up for. View who has booked each slot.</p>
                <a href="faculty_advising.php" class="btn-card">Manage Slots</a>
            </div>

            <!-- ── Student Notes ── -->
            <div class="adv-card">
                <div class="card-icon">&#128221;</div>
                <h3>Student Notes</h3>
                <p>View and add advising notes for a student. Select a student below to get started.</p>
                <form class="student-picker" action="student_notes.php" method="GET">
                    <?php if (empty($students)): ?>
                        <p style="color:#888;font-size:.85rem;margin:0;">No students found.</p>
                    <?php else: ?>
                        <select name="student_id" required>
                            <option value="" disabled selected>-- Select a student --</option>
                            <?php foreach ($students as $s): ?>
                                <option value="<?= (int)$s['id'] ?>">
                                    <?= htmlspecialchars($s['last_name'] . ', ' . $s['first_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="btn-card">View Notes</button>
                    <?php endif; ?>
                </form>
            </div>

        <?php elseif ($role === 'student'): ?>

            <!-- ── Sign Up ── -->
            <div class="adv-card">
                <div class="card-icon">&#128197;</div>
                <h3>Advising Sign-Up</h3>
                <p>Browse available advising slots and book an appointment with a professor.</p>
                <a href="student_advising.php" class="btn-card">View Slots</a>
            </div>

        <?php else: ?>

            <!-- Fallback for admin / other roles -->
            <div class="adv-card">
                <div class="card-icon">&#128203;</div>
                <h3>Student Notes</h3>
                <p>View advising notes.</p>
                <a href="student_notes.php" class="btn-card">Open</a>
            </div>

            <div class="adv-card">
                <div class="card-icon">&#128197;</div>
                <h3>Advising Meetings</h3>
                <p>View scheduled advising meetings.</p>
                <a href="view_meetings.php" class="btn-card">Open</a>
            </div>

        <?php endif; ?>

    </div>

    <footer>
        <br>sam.kirkhorn.23@cnu.edu
        <br>grant.kochan.23@cnu.edu
        <br>alijah.levenberry.23@cnu.edu
        <br>colin.anderson.22@cnu.edu
    </footer>

</div>
</body>
</html>

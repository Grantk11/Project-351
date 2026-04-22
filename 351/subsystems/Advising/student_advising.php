<?php
/*  Responsible Party: Alijah */
session_start();
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../includes/dbconnect.php';
require_role(['student']);

$student_id = (int) $_SESSION['user_id'];
$errors     = [];
$success    = "";

// ── Handle POST actions ──────────────────────────────────────────────────────

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

    if ($_POST['action'] === 'book') {
        $slot_id = (int) ($_POST['slot_id'] ?? 0);

        // Verify slot is still open
        $check = $pdo->prepare(
            "SELECT slot_id FROM advising_slots WHERE slot_id = ? AND is_booked = 0"
        );
        $check->execute([$slot_id]);

        if (!$check->fetch()) {
            $errors[] = "Sorry, that slot is no longer available.";
        } else {
            $stmt = $pdo->prepare(
                "UPDATE advising_slots
                    SET is_booked = 1, student_id = ?
                  WHERE slot_id = ? AND is_booked = 0"
            );
            $stmt->execute([$student_id, $slot_id]);

            if ($stmt->rowCount() > 0) {
                $success = "Appointment booked! See your upcoming meetings below.";
            } else {
                $errors[] = "That slot was just taken. Please choose another.";
            }
        }

    } elseif ($_POST['action'] === 'cancel') {
        $slot_id = (int) ($_POST['slot_id'] ?? 0);

        $stmt = $pdo->prepare(
            "UPDATE advising_slots
                SET is_booked = 0, student_id = NULL
              WHERE slot_id = ? AND student_id = ?
                AND CONCAT(slot_date, ' ', slot_time) > NOW()"
        );
        $stmt->execute([$slot_id, $student_id]);

        if ($stmt->rowCount() > 0) {
            $success = "Appointment cancelled.";
        } else {
            $errors[] = "Unable to cancel — the appointment may already be in the past.";
        }
    }
}

// ── Fetch available slots (open, future) ─────────────────────────────────────

$avail_stmt = $pdo->prepare(
    "SELECT s.slot_id, s.slot_date, s.slot_time, s.location, s.notes,
            CONCAT(u.first_name, ' ', u.last_name) AS faculty_name
       FROM advising_slots s
       JOIN users u ON u.id = s.faculty_id
      WHERE s.is_booked = 0
        AND CONCAT(s.slot_date, ' ', s.slot_time) > NOW()
   ORDER BY s.slot_date, s.slot_time"
);
$avail_stmt->execute();
$available = $avail_stmt->fetchAll(PDO::FETCH_ASSOC);

// ── Fetch this student's booked appointments ──────────────────────────────────

$booked_stmt = $pdo->prepare(
    "SELECT s.slot_id, s.slot_date, s.slot_time, s.location, s.notes,
            CONCAT(u.first_name, ' ', u.last_name) AS faculty_name
       FROM advising_slots s
       JOIN users u ON u.id = s.faculty_id
      WHERE s.student_id = ? AND s.is_booked = 1
   ORDER BY s.slot_date, s.slot_time"
);
$booked_stmt->execute([$student_id]);
$booked = $booked_stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Advising Sign-Up</title>
    <link rel="stylesheet" href="../../351.css">
    <style>
        .module-header { margin-bottom: 1.5rem; }
        .module-header h2 { margin-bottom: .25rem; }

        .alert { padding: .75rem 1rem; border-radius: 4px; margin-bottom: 1rem; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error   { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }

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
            padding: .4rem 1rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: .88rem;
            font-weight: bold;
        }
        .btn-primary { background: #004a8f; color: #fff; }
        .btn-primary:hover { background: #003366; }
        .btn-cancel  { background: #c0392b; color: #fff; }
        .btn-cancel:hover { background: #922b21; }
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
            <li><a href="../../logout.php">Log Out</a></li>
        </ul>
    </nav>

    <main style="max-width:900px;margin:2rem auto;padding:0 1rem;">
        <div class="module-header">
            <h2>Course Advising &mdash; Sign-Up</h2>
            <p>Choose an open advising slot below. You can cancel future appointments at any time.</p>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        <?php if ($errors): ?>
            <div class="alert alert-error">
                <ul style="margin:0;padding-left:1.2rem;">
                    <?php foreach ($errors as $e): ?>
                        <li><?= htmlspecialchars($e) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- ── My Appointments ── -->
        <div class="card">
            <h3>My Upcoming Appointments</h3>
            <?php if (empty($booked)): ?>
                <p>You have no booked appointments yet.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Professor</th>
                            <th>Location</th>
                            <th>Notes</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($booked as $appt): ?>
                        <?php $isPast = strtotime($appt['slot_date'] . ' ' . $appt['slot_time']) < time(); ?>
                        <tr>
                            <td><?= htmlspecialchars(date('D, M j, Y', strtotime($appt['slot_date']))) ?></td>
                            <td><?= htmlspecialchars(date('g:i A', strtotime($appt['slot_time']))) ?></td>
                            <td><?= htmlspecialchars($appt['faculty_name']) ?></td>
                            <td><?= htmlspecialchars($appt['location']) ?></td>
                            <td><?= htmlspecialchars($appt['notes'] ?: '—') ?></td>
                            <td>
                                <?php if (!$isPast): ?>
                                    <form method="POST" action="student_advising.php"
                                          onsubmit="return confirm('Cancel this appointment?')">
                                        <input type="hidden" name="action"  value="cancel">
                                        <input type="hidden" name="slot_id" value="<?= (int)$appt['slot_id'] ?>">
                                        <button type="submit" class="btn btn-cancel">Cancel</button>
                                    </form>
                                <?php else: ?>
                                    <em style="color:#888;font-size:.85rem;">Past</em>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <!-- ── Available Slots ── -->
        <div class="card">
            <h3>Available Advising Slots</h3>
            <?php if (empty($available)): ?>
                <p>No open slots are available right now. Check back later.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Professor</th>
                            <th>Location</th>
                            <th>Notes</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($available as $slot): ?>
                        <tr>
                            <td><?= htmlspecialchars(date('D, M j, Y', strtotime($slot['slot_date']))) ?></td>
                            <td><?= htmlspecialchars(date('g:i A', strtotime($slot['slot_time']))) ?></td>
                            <td><?= htmlspecialchars($slot['faculty_name']) ?></td>
                            <td><?= htmlspecialchars($slot['location']) ?></td>
                            <td><?= htmlspecialchars($slot['notes'] ?: '—') ?></td>
                            <td>
                                <form method="POST" action="student_advising.php">
                                    <input type="hidden" name="action"  value="book">
                                    <input type="hidden" name="slot_id" value="<?= (int)$slot['slot_id'] ?>">
                                    <button type="submit" class="btn btn-primary">Book</button>
                                </form>
                            </td>
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

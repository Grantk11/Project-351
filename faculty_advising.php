<?php
session_start();
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../includes/dbconnect.php';
require_role(['professor']);

$faculty_id = (int) $_SESSION['user_id'];
$errors     = [];
$success    = "";

// ── Handle POST actions ──────────────────────────────────────────────────────

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

    if ($_POST['action'] === 'create') {
        $date     = trim($_POST['slot_date'] ?? '');
        $time     = trim($_POST['slot_time'] ?? '');
        $location = trim($_POST['location']  ?? '');
        $notes    = trim($_POST['notes']     ?? '');

        if (!$date)     $errors[] = "Date is required.";
        if (!$time)     $errors[] = "Time is required.";
        if (!$location) $errors[] = "Location is required.";

        if (empty($errors)) {
            try {
                $stmt = $pdo->prepare(
                    "INSERT INTO advising_slots (faculty_id, slot_date, slot_time, location, notes)
                     VALUES (?, ?, ?, ?, ?)"
                );
                $stmt->execute([$faculty_id, $date, $time, $location, $notes]);
                $success = "Slot created successfully.";
            } catch (PDOException $e) {
                if ($e->getCode() === '23000') {
                    $errors[] = "A slot already exists for that date and time.";
                } else {
                    $errors[] = "Could not create slot. Please try again.";
                }
            }
        }

    } elseif ($_POST['action'] === 'delete') {
        $slot_id = (int) ($_POST['slot_id'] ?? 0);
        $stmt = $pdo->prepare(
            "DELETE FROM advising_slots
              WHERE slot_id = ? AND faculty_id = ? AND is_booked = 0"
        );
        $stmt->execute([$slot_id, $faculty_id]);
        $success = "Slot removed.";
    }
}

// ── Fetch all slots for this professor ───────────────────────────────────────

$stmt = $pdo->prepare(
    "SELECT s.slot_id, s.slot_date, s.slot_time, s.location, s.notes,
            s.is_booked, s.student_id,
            CONCAT(u.first_name, ' ', u.last_name) AS student_name
       FROM advising_slots s
  LEFT JOIN users u ON u.id = s.student_id
      WHERE s.faculty_id = ?
   ORDER BY s.slot_date, s.slot_time"
);
$stmt->execute([$faculty_id]);
$slots = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Advising Slots</title>
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

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: .75rem 1.25rem;
        }
        .form-grid .full { grid-column: 1 / -1; }

        label { display: block; font-weight: bold; margin-bottom: .25rem; font-size: .9rem; }
        input[type="date"],
        input[type="time"],
        input[type="text"],
        textarea {
            width: 100%;
            padding: .45rem .65rem;
            border: 1px solid #bbb;
            border-radius: 4px;
            font-size: .95rem;
            box-sizing: border-box;
        }
        textarea { resize: vertical; min-height: 70px; }

        .btn {
            display: inline-block;
            padding: .5rem 1.2rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: .95rem;
            font-weight: bold;
        }
        .btn-primary { background: #004a8f; color: #fff; }
        .btn-primary:hover { background: #003366; }
        .btn-danger  { background: #c0392b; color: #fff; font-size: .8rem; padding: .3rem .7rem; }
        .btn-danger:hover { background: #922b21; }

        table { width: 100%; border-collapse: collapse; font-size: .92rem; }
        th, td { padding: .55rem .75rem; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #004a8f; color: #fff; }
        tr:hover td { background: #f0f4ff; }

        .badge {
            display: inline-block;
            padding: .2rem .55rem;
            border-radius: 12px;
            font-size: .78rem;
            font-weight: bold;
        }
        .badge-open   { background: #d4edda; color: #155724; }
        .badge-booked { background: #fff3cd; color: #856404; }

        @media (max-width: 600px) {
            .form-grid { grid-template-columns: 1fr; }
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
            <li><a href="../../logout.php">Log Out</a></li>
        </ul>
    </nav>

    <main style="max-width:900px;margin:2rem auto;padding:0 1rem;">
        <div class="module-header">
            <h2>Course Advising &mdash; Professor Portal</h2>
            <p>Create open advising slots that students can sign up for.</p>
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

        <!-- ── Create Slot Form ── -->
        <div class="card">
            <h3>Add a New Advising Slot</h3>
            <form method="POST" action="faculty_advising.php">
                <input type="hidden" name="action" value="create">
                <div class="form-grid">
                    <div>
                        <label for="slot_date">Date</label>
                        <input type="date" id="slot_date" name="slot_date"
                               value="<?= htmlspecialchars($_POST['slot_date'] ?? '') ?>"
                               min="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div>
                        <label for="slot_time">Time</label>
                        <input type="time" id="slot_time" name="slot_time"
                               value="<?= htmlspecialchars($_POST['slot_time'] ?? '') ?>" required>
                    </div>
                    <div>
                        <label for="location">Location / Room</label>
                        <input type="text" id="location" name="location" maxlength="100"
                               placeholder="e.g. McMurran 210"
                               value="<?= htmlspecialchars($_POST['location'] ?? '') ?>" required>
                    </div>
                    <div><!-- spacer --></div>
                    <div class="full">
                        <label for="notes">Notes (optional)</label>
                        <textarea id="notes" name="notes"
                                  placeholder="e.g. Bring your degree audit printout"><?= htmlspecialchars($_POST['notes'] ?? '') ?></textarea>
                    </div>
                </div>
                <br>
                <button type="submit" class="btn btn-primary">Create Slot</button>
            </form>
        </div>

        <!-- ── Existing Slots Table ── -->
        <div class="card">
            <h3>Your Advising Slots</h3>
            <?php if (empty($slots)): ?>
                <p>No slots created yet.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Location</th>
                            <th>Notes</th>
                            <th>Status</th>
                            <th>Booked By</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($slots as $slot): ?>
                        <tr>
                            <td><?= htmlspecialchars(date('D, M j, Y', strtotime($slot['slot_date']))) ?></td>
                            <td><?= htmlspecialchars(date('g:i A', strtotime($slot['slot_time']))) ?></td>
                            <td><?= htmlspecialchars($slot['location']) ?></td>
                            <td><?= htmlspecialchars($slot['notes'] ?: '—') ?></td>
                            <td>
                                <?php if ($slot['is_booked']): ?>
                                    <span class="badge badge-booked">Booked</span>
                                <?php else: ?>
                                    <span class="badge badge-open">Open</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($slot['student_name'] ?: '—') ?></td>
                            <td>
                                <?php if (!$slot['is_booked']): ?>
                                    <form method="POST" action="faculty_advising.php"
                                          onsubmit="return confirm('Delete this slot?')">
                                        <input type="hidden" name="action"  value="delete">
                                        <input type="hidden" name="slot_id" value="<?= (int)$slot['slot_id'] ?>">
                                        <button type="submit" class="btn btn-danger">Delete</button>
                                    </form>
                                <?php else: ?>
                                    —
                                <?php endif; ?>
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

<?php
session_start();
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../includes/dbconnect.php';
require_role(['professor']);

$professor_id = (int)$_SESSION['user_id'];
$errors  = [];
$success = '';

// Fetch this professor's advisees for the recipient dropdown
$advStmt = $pdo->prepare(
    "SELECT u.id, u.first_name, u.last_name
       FROM advisee_list al
       JOIN users u ON u.id = al.student_id
      WHERE al.professor_id = ?
      ORDER BY u.last_name, u.first_name"
);
$advStmt->execute([$professor_id]);
$advisees = $advStmt->fetchAll(PDO::FETCH_ASSOC);

// Handle send
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $recipient = trim($_POST['recipient'] ?? '');
    $subject   = trim($_POST['subject']   ?? '');
    $body      = trim($_POST['body']      ?? '');

    if (!$recipient) $errors[] = "Please select a recipient.";
    if (!$subject)   $errors[] = "Subject is required.";
    if (!$body)      $errors[] = "Message body is required.";

    if (empty($errors)) {
        $insert = $pdo->prepare(
            "INSERT INTO messages (sender_id, recipient_id, subject, body)
             VALUES (?, ?, ?, ?)"
        );

        if ($recipient === 'all') {
            // Send to every advisee
            foreach ($advisees as $a) {
                $insert->execute([$professor_id, (int)$a['id'], $subject, $body]);
            }
            $success = "Message sent to all " . count($advisees) . " advisee(s).";
        } else {
            $insert->execute([$professor_id, (int)$recipient, $subject, $body]);
            $success = "Message sent.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compose Message</title>
    <link rel="stylesheet" href="../../351.css">
    <style>
        .module-header { margin-bottom:1.5rem; }
        .module-header h2 { margin-bottom:.25rem; }
        .module-header p  { color:#555; margin:0; }

        .alert { padding:.75rem 1rem; border-radius:4px; margin-bottom:1rem; }
        .alert-success { background:#d4edda; color:#155724; border:1px solid #c3e6cb; }
        .alert-error   { background:#f8d7da; color:#721c24; border:1px solid #f5c6cb; }

        .card {
            background:#fff;
            border:1px solid #ddd;
            border-radius:6px;
            padding:1.25rem 1.5rem;
            margin-bottom:2rem;
        }

        .form-group { margin-bottom:1.1rem; }
        label { display:block; font-weight:600; font-size:.9rem; margin-bottom:.3rem; }

        select,
        input[type="text"],
        textarea {
            width:100%;
            padding:.45rem .65rem;
            border:1px solid #bbb;
            border-radius:4px;
            font-size:.95rem;
            box-sizing:border-box;
            background:#fafafa;
        }
        select:focus,
        input[type="text"]:focus,
        textarea:focus { outline:2px solid #004a8f; border-color:#004a8f; background:#fff; }
        textarea { resize:vertical; min-height:160px; }

        .btn {
            display:inline-block;
            padding:.5rem 1.2rem;
            border:none;
            border-radius:4px;
            cursor:pointer;
            font-size:.95rem;
            font-weight:600;
            text-decoration:none;
        }
        .btn-primary   { background:#004a8f; color:#fff; }
        .btn-primary:hover   { background:#003366; }
        .btn-secondary { background:#e2e8f0; color:#333; }
        .btn-secondary:hover { background:#cbd5e0; }

        .form-actions { display:flex; gap:.75rem; margin-top:1.25rem; }

        .no-advisees {
            background:#fff8e1;
            border:1px solid #ffe082;
            border-radius:4px;
            padding:.75rem 1rem;
            font-size:.9rem;
            color:#795548;
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
            <li><a href="messaging_index.php">Messaging</a></li>
            <li><a href="advising_index.php">Advising Home</a></li>
            <li><a href="../../logout.php">Logout</a></li>
        </ul>
    </nav>

    <main style="max-width:660px;margin:2rem auto;padding:0 1rem;">

        <div class="module-header">
            <h2>Compose Message</h2>
            <p>Send a message to all your advisees or to a specific student.</p>
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

        <?php if (empty($advisees)): ?>
            <div class="no-advisees">
                You have no advisees yet.
                <a href="manage_advisees.php" style="color:#004a8f;font-weight:600;">Add advisees</a>
                before sending a message.
            </div>
        <?php else: ?>

        <div class="card">
            <form method="POST" action="compose_message.php">

                <div class="form-group">
                    <label for="recipient">To</label>
                    <select id="recipient" name="recipient" required>
                        <option value="" disabled <?= empty($_POST['recipient']) ? 'selected' : '' ?>>-- Select recipient --</option>
                        <option value="all" <?= ($_POST['recipient'] ?? '') === 'all' ? 'selected' : '' ?>>
                            &#128101; All Advisees (<?= count($advisees) ?>)
                        </option>
                        <optgroup label="Individual Advisees">
                        <?php foreach ($advisees as $a): ?>
                            <option value="<?= (int)$a['id'] ?>"
                                <?= (string)($_POST['recipient'] ?? '') === (string)$a['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($a['last_name'] . ', ' . $a['first_name']) ?>
                            </option>
                        <?php endforeach; ?>
                        </optgroup>
                    </select>
                </div>

                <div class="form-group">
                    <label for="subject">Subject</label>
                    <input type="text" id="subject" name="subject" maxlength="150"
                           placeholder="e.g. Registration reminder"
                           value="<?= htmlspecialchars($_POST['subject'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="body">Message</label>
                    <textarea id="body" name="body"
                              placeholder="Write your message here..."><?= htmlspecialchars($_POST['body'] ?? '') ?></textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">&#9993; Send Message</button>
                    <a href="messaging_index.php" class="btn btn-secondary">Cancel</a>
                </div>

            </form>
        </div>

        <?php endif; ?>

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

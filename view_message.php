<?php
session_start();
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../includes/dbconnect.php';
require_role(['professor', 'student']);

$role       = $_SESSION['role'];
$user_id    = (int)$_SESSION['user_id'];
$message_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch the message — only allow access if user is sender or recipient
$stmt = $pdo->prepare(
    "SELECT m.*,
            CONCAT(s.first_name, ' ', s.last_name) AS sender_name,
            CONCAT(r.first_name, ' ', r.last_name) AS recipient_name
       FROM messages m
       JOIN users s ON s.id = m.sender_id
       JOIN users r ON r.id = m.recipient_id
      WHERE m.message_id = ?
        AND (m.sender_id = ? OR m.recipient_id = ?)"
);
$stmt->execute([$message_id, $user_id, $user_id]);
$msg = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$msg) {
    header("Location: inbox.php");
    exit;
}

// Mark as read when the recipient opens it
if ($role === 'student' && !$msg['is_read']) {
    $pdo->prepare("UPDATE messages SET is_read = 1 WHERE message_id = ?")
        ->execute([$message_id]);
    $msg['is_read'] = 1;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Message</title>
    <link rel="stylesheet" href="../../351.css">
    <style>
        .module-header { margin-bottom:1.5rem; }
        .module-header h2 { margin-bottom:.25rem; }

        .card {
            background:#fff;
            border:1px solid #ddd;
            border-radius:6px;
            padding:1.5rem;
            margin-bottom:2rem;
        }

        .msg-meta {
            display: grid;
            grid-template-columns: auto 1fr;
            gap: .35rem 1rem;
            font-size: .92rem;
            margin-bottom: 1.25rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #eee;
        }
        .msg-meta .label { font-weight:700; color:#444; white-space:nowrap; }
        .msg-meta .value { color:#222; }

        .msg-subject {
            font-size: 1.2rem;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 1.25rem;
        }

        .msg-body {
            font-size: .95rem;
            line-height: 1.7;
            color: #333;
            white-space: pre-wrap;
            background: #f9fbff;
            border: 1px solid #e4eaf4;
            border-radius: 5px;
            padding: 1rem 1.25rem;
        }

        .btn {
            display:inline-block;
            padding:.5rem 1.2rem;
            border:none;
            border-radius:4px;
            cursor:pointer;
            font-size:.9rem;
            font-weight:600;
            text-decoration:none;
        }
        .btn-secondary { background:#e2e8f0; color:#333; }
        .btn-secondary:hover { background:#cbd5e0; }
        .btn-primary   { background:#004a8f; color:#fff; }
        .btn-primary:hover   { background:#003366; }

        .btn-row { display:flex; gap:.75rem; margin-top:1.5rem; }
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
            <li><a href="inbox.php">
                <?= $role === 'professor' ? 'Sent Messages' : 'Inbox' ?>
            </a></li>
            <li><a href="messaging_index.php">Messaging</a></li>
            <li><a href="../../logout.php">Logout</a></li>
        </ul>
    </nav>

    <main style="max-width:760px;margin:2rem auto;padding:0 1rem;">

        <div class="module-header">
            <h2>Message</h2>
        </div>

        <div class="card">

            <div class="msg-meta">
                <span class="label">From:</span>
                <span class="value"><?= htmlspecialchars($msg['sender_name']) ?></span>

                <span class="label">To:</span>
                <span class="value"><?= htmlspecialchars($msg['recipient_name']) ?></span>

                <span class="label">Date:</span>
                <span class="value"><?= htmlspecialchars(date('l, F j, Y \a\t g:i A', strtotime($msg['sent_at']))) ?></span>
            </div>

            <div class="msg-subject"><?= htmlspecialchars($msg['subject']) ?></div>

            <div class="msg-body"><?= htmlspecialchars($msg['body']) ?></div>

            <div class="btn-row">
                <a href="inbox.php" class="btn btn-secondary">
                    &larr; Back to <?= $role === 'professor' ? 'Sent' : 'Inbox' ?>
                </a>
                <?php if ($role === 'professor'): ?>
                    <a href="compose_message.php" class="btn btn-primary">+ Compose New</a>
                <?php endif; ?>
            </div>

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

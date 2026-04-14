<?php
session_start();
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../includes/dbconnect.php';
require_role(['professor', 'student']);

$role    = $_SESSION['role'];
$user_id = (int)$_SESSION['user_id'];

if ($role === 'professor') {
    // Show sent messages
    $stmt = $pdo->prepare(
        "SELECT m.message_id, m.subject, m.sent_at, m.is_read,
                CONCAT(u.first_name, ' ', u.last_name) AS recipient_name
           FROM messages m
           JOIN users u ON u.id = m.recipient_id
          WHERE m.sender_id = ?
          ORDER BY m.sent_at DESC"
    );
    $stmt->execute([$user_id]);
} else {
    // Show received messages
    $stmt = $pdo->prepare(
        "SELECT m.message_id, m.subject, m.sent_at, m.is_read,
                CONCAT(u.first_name, ' ', u.last_name) AS sender_name
           FROM messages m
           JOIN users u ON u.id = m.sender_id
          WHERE m.recipient_id = ?
          ORDER BY m.sent_at DESC"
    );
    $stmt->execute([$user_id]);
}

$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

$unread = 0;
if ($role === 'student') {
    foreach ($messages as $m) {
        if (!$m['is_read']) $unread++;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $role === 'professor' ? 'Sent Messages' : 'Inbox' ?></title>
    <link rel="stylesheet" href="../../351.css">
    <style>
        .module-header { margin-bottom:1.5rem; }
        .module-header h2 { margin-bottom:.25rem; }
        .module-header p  { color:#555; margin:0; }

        .card {
            background:#fff;
            border:1px solid #ddd;
            border-radius:6px;
            padding:1.25rem 1.5rem;
            margin-bottom:2rem;
        }
        .card-toolbar {
            display:flex;
            justify-content:space-between;
            align-items:center;
            margin-bottom:1rem;
        }
        .card-toolbar h3 { margin:0; }

        table { width:100%; border-collapse:collapse; font-size:.92rem; }
        th, td { padding:.6rem .75rem; text-align:left; border-bottom:1px solid #ddd; }
        th { background:#004a8f; color:#fff; }
        tr:hover td { background:#f0f4ff; }

        .unread td { font-weight:700; background:#f0f7ff; }
        .unread td:first-child::before {
            content: '● ';
            color: #004a8f;
            font-size: .65rem;
            vertical-align: middle;
        }

        .btn {
            display:inline-block;
            padding:.35rem .85rem;
            border:none;
            border-radius:4px;
            cursor:pointer;
            font-size:.85rem;
            font-weight:600;
            text-decoration:none;
        }
        .btn-primary { background:#004a8f; color:#fff; }
        .btn-primary:hover { background:#003366; }

        .badge-unread {
            display:inline-block;
            background:#c0392b;
            color:#fff;
            border-radius:10px;
            padding:.1rem .55rem;
            font-size:.75rem;
            font-weight:700;
            margin-left:.4rem;
            vertical-align:middle;
        }

        .empty-msg { color:#777; font-style:italic; }
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

    <main style="max-width:900px;margin:2rem auto;padding:0 1rem;">

        <div class="module-header">
            <?php if ($role === 'professor'): ?>
                <h2>Sent Messages</h2>
                <p>All messages you have sent to your advisees.</p>
            <?php else: ?>
                <h2>
                    Inbox
                    <?php if ($unread > 0): ?>
                        <span class="badge-unread"><?= $unread ?> unread</span>
                    <?php endif; ?>
                </h2>
                <p>Messages from your professor.</p>
            <?php endif; ?>
        </div>

        <div class="card">
            <div class="card-toolbar">
                <h3><?= $role === 'professor' ? 'Sent' : 'Received' ?> (<?= count($messages) ?>)</h3>
                <?php if ($role === 'professor'): ?>
                    <a href="compose_message.php" class="btn btn-primary">+ Compose</a>
                <?php endif; ?>
            </div>

            <?php if (empty($messages)): ?>
                <p class="empty-msg">
                    <?= $role === 'professor' ? 'You have not sent any messages yet.' : 'Your inbox is empty.' ?>
                </p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th><?= $role === 'professor' ? 'To' : 'From' ?></th>
                            <th>Subject</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($messages as $msg): ?>
                        <tr class="<?= ($role === 'student' && !$msg['is_read']) ? 'unread' : '' ?>">
                            <td>
                                <?php if ($role === 'professor'): ?>
                                    <?= htmlspecialchars($msg['recipient_name']) ?>
                                <?php else: ?>
                                    <?= htmlspecialchars($msg['sender_name']) ?>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($msg['subject']) ?></td>
                            <td><?= htmlspecialchars(date('M j, Y g:i A', strtotime($msg['sent_at']))) ?></td>
                            <td>
                                <a href="view_message.php?id=<?= (int)$msg['message_id'] ?>" class="btn btn-primary">
                                    <?= $role === 'professor' ? 'View' : 'Read' ?>
                                </a>
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

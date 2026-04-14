<?php
session_start();
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../includes/dbconnect.php';
require_role(['professor', 'student']);

$role    = $_SESSION['role'];
$user_id = (int)$_SESSION['user_id'];

// Unread count for students
$unread = 0;
if ($role === 'student') {
    $u = $pdo->prepare("SELECT COUNT(*) FROM messages WHERE recipient_id = ? AND is_read = 0");
    $u->execute([$user_id]);
    $unread = (int)$u->fetchColumn();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messaging</title>
    <link rel="stylesheet" href="../../351.css">
    <style>
        .adv-hero { text-align:center; padding:2rem 1rem 1rem; }
        .adv-hero h2 { font-size:1.7rem; margin-bottom:.25rem; }
        .adv-hero p  { color:#555; margin:0; }

        .adv-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
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
        .adv-card .card-icon { font-size:2rem; line-height:1; }
        .adv-card h3 { margin:0; font-size:1.1rem; color:#1a1a2e; }
        .adv-card p  { margin:0; font-size:.9rem; color:#555; flex:1; }

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
            transition: background .2s;
        }
        .btn-card:hover { background:#003366; }

        .badge {
            display: inline-block;
            background: #c0392b;
            color: #fff;
            border-radius: 10px;
            padding: .1rem .55rem;
            font-size: .75rem;
            font-weight: 700;
            margin-left: .4rem;
            vertical-align: middle;
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

    <div class="adv-hero">
        <h2>Messaging</h2>
        <p><?= $role === 'professor' ? 'Manage your advisee list and send messages.' : 'View messages from your professor.' ?></p>
    </div>

    <div class="adv-grid">

        <?php if ($role === 'professor'): ?>

            <div class="adv-card">
                <div class="card-icon">&#128101;</div>
                <h3>Manage Advisee List</h3>
                <p>Select which registered students are on your advisee list.</p>
                <a href="manage_advisees.php" class="btn-card">Manage Advisees</a>
            </div>

            <div class="adv-card">
                <div class="card-icon">&#9997;&#65039;</div>
                <h3>Send a Message</h3>
                <p>Send a message to all your advisees at once, or to a single student.</p>
                <a href="compose_message.php" class="btn-card">Compose</a>
            </div>

            <div class="adv-card">
                <div class="card-icon">&#128228;</div>
                <h3>Sent Messages</h3>
                <p>View all messages you have previously sent to your advisees.</p>
                <a href="inbox.php" class="btn-card">View Sent</a>
            </div>

        <?php else: ?>

            <div class="adv-card">
                <div class="card-icon">&#128140;</div>
                <h3>
                    Inbox
                    <?php if ($unread > 0): ?>
                        <span class="badge"><?= $unread ?></span>
                    <?php endif; ?>
                </h3>
                <p>View messages sent to you by your professor.</p>
                <a href="inbox.php" class="btn-card">Open Inbox</a>
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

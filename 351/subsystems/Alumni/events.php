<?php
session_start();
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../includes/dbconnect.php';
require_login();

$role = $_SESSION['role'] ?? '';
$user_id = $_SESSION['user_id'];
$can_manage = in_array($role, ['alumni', 'professor'], true);
$is_student = $role === 'student';

if ($can_manage) {
    $action = $_POST['action'] ?? '';
    if ($action === 'post_event') {
        $stmt = $pdo->prepare("INSERT INTO events (title, event_date, location, posted_by, description) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$_POST['title'], $_POST['event_date'], $_POST['location'], $_SESSION['first_name'] . ' ' . $_SESSION['last_name'], $_POST['description']]);
        header("Location: events.php?msg=event_added"); exit;
    }
    if (isset($_GET['delete']) && isset($_GET['id'])) {
        $id = (int) $_GET['id'];
        $pdo->prepare("DELETE FROM event_rsvps WHERE event_id = ?")->execute([$id]);
        $pdo->prepare("DELETE FROM events WHERE id = ?")->execute([$id]);
        header("Location: events.php?msg=event_deleted"); exit;
    }
}

if ($is_student && isset($_POST['action'])) {
    $event_id = (int) $_POST['event_id'];
    if ($_POST['action'] === 'rsvp') {
        $check = $pdo->prepare("SELECT id FROM event_rsvps WHERE event_id = ? AND user_id = ?");
        $check->execute([$event_id, $user_id]);
        if (!$check->fetch()) {
            $pdo->prepare("INSERT INTO event_rsvps (event_id, user_id) VALUES (?, ?)")->execute([$event_id, $user_id]);
        }
        header("Location: events.php?msg=rsvp_added"); exit;
    }
    if ($_POST['action'] === 'unrsvp') {
        $pdo->prepare("DELETE FROM event_rsvps WHERE event_id = ? AND user_id = ?")->execute([$event_id, $user_id]);
        header("Location: events.php?msg=rsvp_removed"); exit;
    }
}

$events = $pdo->query("SELECT * FROM events ORDER BY event_date ASC")->fetchAll(PDO::FETCH_ASSOC);

$my_rsvps = [];
if ($is_student) {
    $rows = $pdo->prepare("SELECT event_id FROM event_rsvps WHERE user_id = ?");
    $rows->execute([$user_id]);
    $my_rsvps = array_column($rows->fetchAll(), 'event_id');
}

$rsvp_data = [];
foreach ($events as $e) {
    if ($can_manage) {
        $stmt = $pdo->prepare("SELECT u.first_name, u.last_name FROM event_rsvps r JOIN users u ON u.id = r.user_id WHERE r.event_id = ?");
        $stmt->execute([$e['id']]);
        $rsvp_data[$e['id']] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM event_rsvps WHERE event_id = ?");
        $stmt->execute([$e['id']]);
        $rsvp_data[$e['id']] = $stmt->fetchColumn();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events — CNU Alumni</title>
    <link rel="stylesheet" href="../../351.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; }
        .page-title { font-size: 1.6rem; font-weight: 800; color: #2f3640; margin-bottom: 6px; }
        .role-badge { display: inline-block; background: #00a8ff; color: #fff; font-size: 0.78rem; font-weight: 700; padding: 3px 12px; border-radius: 20px; margin-bottom: 24px; text-transform: uppercase; letter-spacing: 0.5px; }
        .msg-success { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; padding: 10px 16px; border-radius: 8px; margin-bottom: 20px; font-size: 0.9rem; }
        .section { margin-bottom: 40px; }
        .section-heading { font-size: 0.95rem; font-weight: 700; color: #00a8ff; margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.5px; }
        .form-wrap { background: #f5f6fa; border: 1px solid #edeff2; border-radius: 10px; padding: 20px 24px; margin-bottom: 20px; }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
        .span2 { grid-column: 1 / -1; }
        .field { display: flex; flex-direction: column; gap: 5px; }
        .field label { font-size: 0.75rem; font-weight: 700; color: #57606f; text-transform: uppercase; letter-spacing: 0.5px; }
        .field input, .field textarea { padding: 9px 12px; border: 2px solid #edeff2; border-radius: 8px; font-size: 0.9rem; font-family: inherit; color: #2f3640; background: #fff; transition: border-color 0.2s; }
        .field input:focus, .field textarea:focus { outline: none; border-color: #00a8ff; }
        .field textarea { height: 76px; resize: vertical; }
        .table-wrap { border-radius: 10px; overflow: hidden; border: 1px solid #edeff2; }
        table { width: 100%; border-collapse: collapse; font-size: 0.93rem; }
        thead th { background: #2f3640; color: #fff; padding: 12px 18px; text-align: left; font-weight: 600; font-size: 0.88rem; }
        tbody td { padding: 13px 18px; border-bottom: 1px solid #f5f6fa; color: #57606f; vertical-align: middle; }
        tbody tr:last-child td { border-bottom: none; }
        tbody tr:hover td { background: #f5f6fa; }
        .empty-row td { text-align: center; color: #a4b0be; font-style: italic; padding: 22px; }
        .btn { display: inline-flex; align-items: center; justify-content: center; padding: 7px 16px; border-radius: 8px; font-size: 0.85rem; font-weight: 600; font-family: inherit; cursor: pointer; border: none; text-decoration: none; transition: opacity 0.15s; }
        .btn:hover { opacity: 0.85; }
        .btn-blue  { background: #00a8ff; color: #fff; padding: 10px 24px; font-size: 0.9rem; }
        .btn-green { background: #22c55e; color: #fff; }
        .btn-gray  { background: #a4b0be; color: #fff; }
        .btn-red   { background: #ef4444; color: #fff; }
        .rsvp-count { font-size: 0.85rem; color: #22c55e; font-weight: 700; }
        .attendee-list { margin-top: 5px; display: flex; flex-wrap: wrap; gap: 4px; }
        .attendee-list span { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; border-radius: 4px; padding: 2px 7px; font-size: 0.78rem; }
        .td-actions { display: flex; gap: 6px; align-items: center; flex-wrap: wrap; }
    </style>
</head>
<body>
<div id="wrapper">
    <header><h1><a href="alumni_index.php">Alumni Connection System</a></h1></header>
    <div class="site-logo"><img src="../../ban.png" alt="CNU Banner"></div>
    <nav>
        <ul>
            <li><a href="alumni_index.php">Back</a></li>
            <li><a href="jobs.php">Job Board</a></li>
            <li><a href="mentors.php">Mentorship</a></li>
            <li><a href="../../logout.php">Logout</a></li>
        </ul>
    </nav>
    <main>
        <div class="page-title">📅 Upcoming Events</div>
        <span class="role-badge"><?php echo htmlspecialchars(ucfirst($role)); ?></span>

        <?php if (isset($_GET['msg'])): ?>
            <div class="msg-success"><?php
                $msgs = ['event_added'=>'✅ Event posted.','event_deleted'=>'✅ Event deleted.','rsvp_added'=>'✅ RSVP recorded — you\'re going!','rsvp_removed'=>'✅ RSVP cancelled.'];
                echo $msgs[$_GET['msg']] ?? '';
            ?></div>
        <?php endif; ?>

        <?php if ($can_manage): ?>
        <div class="section">
            <div class="section-heading">Post a New Event</div>
            <div class="form-wrap">
                <form method="POST">
                    <input type="hidden" name="action" value="post_event">
                    <div class="form-grid">
                        <div class="field"><label>Event Title</label><input type="text" name="title" required></div>
                        <div class="field"><label>Event Date</label><input type="date" name="event_date" required></div>
                        <div class="field"><label>Location</label><input type="text" name="location" required></div>
                        <div class="field span2"><label>Description</label><textarea name="description"></textarea></div>
                    </div>
                    <div style="margin-top:14px;"><button type="submit" class="btn btn-blue">Post Event</button></div>
                </form>
            </div>
        </div>
        <?php endif; ?>

        <div class="section">
            <div class="section-heading">All Upcoming Events</div>
            <div class="table-wrap">
                <table>
                    <thead><tr>
                        <th>Title</th><th>Date</th><th>Location</th><th>Posted By</th>
                        <th><?php echo $can_manage ? 'Attendees' : 'RSVPs'; ?></th>
                        <th>Actions</th>
                    </tr></thead>
                    <tbody>
                        <?php if (empty($events)): ?>
                            <tr class="empty-row"><td colspan="6">No events posted yet.</td></tr>
                        <?php else: foreach ($events as $e): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($e['title']); ?></td>
                            <td><?php echo htmlspecialchars($e['event_date']); ?></td>
                            <td><?php echo htmlspecialchars($e['location']); ?></td>
                            <td><?php echo htmlspecialchars($e['posted_by']); ?></td>
                            <td>
                                <?php if ($can_manage):
                                    $attendees = $rsvp_data[$e['id']] ?? []; ?>
                                    <span class="rsvp-count"><?php echo count($attendees); ?> RSVP(s)</span>
                                    <?php if (!empty($attendees)): ?>
                                        <div class="attendee-list">
                                            <?php foreach ($attendees as $a): ?>
                                                <span><?php echo htmlspecialchars($a['first_name'] . ' ' . $a['last_name']); ?></span>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="rsvp-count"><?php echo $rsvp_data[$e['id']]; ?> going</span>
                                <?php endif; ?>
                            </td>
                            <td><div class="td-actions">
                                <?php if ($is_student): ?>
                                    <?php if (in_array($e['id'], $my_rsvps)): ?>
                                        <form method="POST"><input type="hidden" name="action" value="unrsvp"><input type="hidden" name="event_id" value="<?php echo $e['id']; ?>"><button type="submit" class="btn btn-gray">Cancel RSVP</button></form>
                                    <?php else: ?>
                                        <form method="POST"><input type="hidden" name="action" value="rsvp"><input type="hidden" name="event_id" value="<?php echo $e['id']; ?>"><button type="submit" class="btn btn-green">RSVP</button></form>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <?php if ($can_manage): ?>
                                    <a href="events.php?delete=event&id=<?php echo $e['id']; ?>" class="btn btn-red" onclick="return confirm('Delete this event?')">Delete</a>
                                <?php endif; ?>
                            </div></td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
    <footer>
        <br>sam.kirkhorn.23@cnu.edu<br>grant.kochan.23@cnu.edu
        <br>alijah.levenberry.23@cnu.edu<br>colin.anderson.22@cnu.edu
    </footer>
</div>
</body>
</html>
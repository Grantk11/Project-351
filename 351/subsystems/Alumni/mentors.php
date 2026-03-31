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
    if ($action === 'post_mentor') {
        $stmt = $pdo->prepare("INSERT INTO mentors (mentor_name, mentor_email, industry) VALUES (?, ?, ?)");
        $stmt->execute([$_POST['mentor_name'], $_POST['mentor_email'], $_POST['industry']]);
        header("Location: mentors.php?msg=mentor_added"); exit;
    }
    if (isset($_GET['delete']) && isset($_GET['id'])) {
        $id = (int) $_GET['id'];
        $pdo->prepare("DELETE FROM mentor_requests WHERE mentor_id = ?")->execute([$id]);
        $pdo->prepare("DELETE FROM mentors WHERE id = ?")->execute([$id]);
        header("Location: mentors.php?msg=mentor_deleted"); exit;
    }
}

if ($is_student && isset($_POST['action'])) {
    $mentor_id = (int) $_POST['mentor_id'];
    if ($_POST['action'] === 'request') {
        $check = $pdo->prepare("SELECT id FROM mentor_requests WHERE mentor_id = ? AND user_id = ?");
        $check->execute([$mentor_id, $user_id]);
        if (!$check->fetch()) {
            $pdo->prepare("INSERT INTO mentor_requests (mentor_id, user_id) VALUES (?, ?)")->execute([$mentor_id, $user_id]);
        }
        header("Location: mentors.php?msg=requested"); exit;
    }
    if ($_POST['action'] === 'unrequest') {
        $pdo->prepare("DELETE FROM mentor_requests WHERE mentor_id = ? AND user_id = ?")->execute([$mentor_id, $user_id]);
        header("Location: mentors.php?msg=unrequested"); exit;
    }
}

$mentors = $pdo->query("SELECT * FROM mentors ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);

$my_requests = [];
if ($is_student) {
    $rows = $pdo->prepare("SELECT mentor_id FROM mentor_requests WHERE user_id = ?");
    $rows->execute([$user_id]);
    $my_requests = array_column($rows->fetchAll(), 'mentor_id');
}

$request_data = [];
foreach ($mentors as $m) {
    if ($can_manage) {
        $stmt = $pdo->prepare("SELECT u.first_name, u.last_name FROM mentor_requests r JOIN users u ON u.id = r.user_id WHERE r.mentor_id = ?");
        $stmt->execute([$m['id']]);
        $request_data[$m['id']] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM mentor_requests WHERE mentor_id = ?");
        $stmt->execute([$m['id']]);
        $request_data[$m['id']] = $stmt->fetchColumn();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mentorship — CNU Alumni</title>
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
        .field input { padding: 9px 12px; border: 2px solid #edeff2; border-radius: 8px; font-size: 0.9rem; font-family: inherit; color: #2f3640; background: #fff; transition: border-color 0.2s; }
        .field input:focus { outline: none; border-color: #00a8ff; }
        .mentor-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 18px; }
        .mentor-card { background: #f5f6fa; border: 1px solid #edeff2; border-radius: 10px; padding: 20px; }
        .mentor-card h4 { margin: 0 0 4px; font-size: 1rem; color: #2f3640; }
        .mentor-card .industry { font-size: 0.85rem; color: #00a8ff; font-weight: 600; margin-bottom: 6px; }
        .mentor-card .email { font-size: 0.85rem; color: #57606f; word-break: break-all; margin-bottom: 10px; }
        .req-count { font-size: 0.82rem; color: #22c55e; font-weight: 700; margin-bottom: 6px; }
        .requester-list { display: flex; flex-wrap: wrap; gap: 4px; margin-bottom: 10px; }
        .requester-list span { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; border-radius: 4px; padding: 2px 7px; font-size: 0.78rem; }
        .card-actions { display: flex; gap: 8px; flex-wrap: wrap; margin-top: 10px; }
        .btn { display: inline-flex; align-items: center; justify-content: center; padding: 7px 16px; border-radius: 8px; font-size: 0.85rem; font-weight: 600; font-family: inherit; cursor: pointer; border: none; text-decoration: none; transition: opacity 0.15s; }
        .btn:hover { opacity: 0.85; }
        .btn-blue  { background: #00a8ff; color: #fff; padding: 10px 24px; font-size: 0.9rem; }
        .btn-green { background: #22c55e; color: #fff; }
        .btn-gray  { background: #a4b0be; color: #fff; }
        .btn-red   { background: #ef4444; color: #fff; }
        .empty-state { text-align: center; color: #a4b0be; font-style: italic; padding: 30px; background: #f5f6fa; border-radius: 10px; border: 1px solid #edeff2; }
    </style>
</head>
<body>
<div id="wrapper">
    <header><h1><a href="alumni_index.php">Alumni Connection System</a></h1></header>
    <div class="site-logo"><img src="../../ban.png" alt="CNU Banner"></div>
    <nav>
        <ul>
            <li><a href="alumni_index.php">Back</a></li>
            <li><a href="events.php">Events</a></li>
            <li><a href="jobs.php">Job Board</a></li>
            <li><a href="../../logout.php">Logout</a></li>
        </ul>
    </nav>
    <main>
        <div class="page-title">🤝 Mentorship</div>
        <span class="role-badge"><?php echo htmlspecialchars(ucfirst($role)); ?></span>

        <?php if (isset($_GET['msg'])): ?>
            <div class="msg-success"><?php
                $msgs = ['mentor_added'=>'✅ Mentor added.','mentor_deleted'=>'✅ Mentor removed.','requested'=>'✅ Mentorship request sent!','unrequested'=>'✅ Request cancelled.'];
                echo $msgs[$_GET['msg']] ?? '';
            ?></div>
        <?php endif; ?>

        <?php if ($can_manage): ?>
        <div class="section">
            <div class="section-heading">Add a Mentor</div>
            <div class="form-wrap">
                <form method="POST">
                    <input type="hidden" name="action" value="post_mentor">
                    <div class="form-grid">
                        <div class="field"><label>Mentor Name</label><input type="text" name="mentor_name" required></div>
                        <div class="field"><label>Mentor Email</label><input type="email" name="mentor_email" required></div>
                        <div class="field span2"><label>Industry / Field</label><input type="text" name="industry" required></div>
                    </div>
                    <div style="margin-top:14px;"><button type="submit" class="btn btn-blue">Add Mentor</button></div>
                </form>
            </div>
        </div>
        <?php endif; ?>

        <div class="section">
            <div class="section-heading">Available Mentors</div>
            <?php if (empty($mentors)): ?>
                <div class="empty-state">No mentors added yet.</div>
            <?php else: ?>
                <div class="mentor-grid">
                    <?php foreach ($mentors as $m): ?>
                    <div class="mentor-card">
                        <h4><?php echo htmlspecialchars($m['mentor_name']); ?></h4>
                        <div class="industry"><?php echo htmlspecialchars($m['industry']); ?></div>
                        <div class="email">✉ <?php echo htmlspecialchars($m['mentor_email']); ?></div>

                        <?php if ($can_manage):
                            $requesters = $request_data[$m['id']] ?? []; ?>
                            <div class="req-count"><?php echo count($requesters); ?> request(s)</div>
                            <?php if (!empty($requesters)): ?>
                                <div class="requester-list">
                                    <?php foreach ($requesters as $r): ?>
                                        <span><?php echo htmlspecialchars($r['first_name'] . ' ' . $r['last_name']); ?></span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="req-count"><?php echo $request_data[$m['id']]; ?> student(s) interested</div>
                        <?php endif; ?>

                        <div class="card-actions">
                            <?php if ($is_student): ?>
                                <?php if (in_array($m['id'], $my_requests)): ?>
                                    <form method="POST"><input type="hidden" name="action" value="unrequest"><input type="hidden" name="mentor_id" value="<?php echo $m['id']; ?>"><button type="submit" class="btn btn-gray">Cancel Request</button></form>
                                <?php else: ?>
                                    <form method="POST"><input type="hidden" name="action" value="request"><input type="hidden" name="mentor_id" value="<?php echo $m['id']; ?>"><button type="submit" class="btn btn-green">Request Mentor</button></form>
                                <?php endif; ?>
                            <?php endif; ?>
                            <?php if ($can_manage): ?>
                                <a href="mentors.php?delete=mentor&id=<?php echo $m['id']; ?>" class="btn btn-red" onclick="return confirm('Remove this mentor?')">Remove</a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>
    <footer>
        <br>sam.kirkhorn.23@cnu.edu<br>grant.kochan.23@cnu.edu
        <br>alijah.levenberry.23@cnu.edu<br>colin.anderson.22@cnu.edu
    </footer>
</div>
</body>
</html>
<?php
session_start();
 
if (!isset($_SESSION["user_id"])) {
    header("Location: ../../login.php");
    exit;
}
 
// Students only
$role = $_SESSION["role"] ?? "";
if ($role !== "student") {
    header("Location: ../../dashboard.php?error=access_denied");
    exit;
}
 
require_once "../../includes/dbconnect.php";
 
$events  = $pdo->query("SELECT * FROM events  ORDER BY event_date ASC")->fetchAll(PDO::FETCH_ASSOC);
$jobs    = $pdo->query("SELECT * FROM jobs    ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
$mentors = $pdo->query("SELECT * FROM mentors ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alumni Events — CNU</title>
    <link rel="stylesheet" href="../../351.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: #ffffff; color: #111827; padding: 60px 24px 80px; }
        .page-title { font-size: 2rem; font-weight: 800; text-align: center; color: #111827; margin-bottom: 8px; }
        .page-sub { text-align: center; color: #6b7280; font-size: .95rem; margin-bottom: 36px; }
        .role-badge { text-align: center; margin-bottom: 32px; }
        .role-badge span { display: inline-block; padding: 3px 12px; border-radius: 6px; font-size: .8em; font-weight: 700; background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }
        .section { margin-bottom: 48px; }
        .section-heading { font-size: .95rem; font-weight: 700; color: #4a6fa5; margin-bottom: 12px; }
        .centered { max-width: 780px; margin: 0 auto; }
        .table-wrap { max-width: 780px; margin: 0 auto; border-radius: 8px; overflow: hidden; border: 1px solid #e5e7eb; }
        table { width: 100%; border-collapse: collapse; font-size: .93rem; }
        thead th { background: #1e2a4a; color: #fff; padding: 12px 18px; text-align: left; font-weight: 600; font-size: .88rem; }
        tbody td { padding: 13px 18px; border-bottom: 1px solid #f3f4f6; color: #374151; vertical-align: middle; }
        tbody tr:last-child td { border-bottom: none; }
        tbody tr:hover td { background: #f9fafb; }
        .empty-row td { text-align: center; color: #9ca3af; font-style: italic; padding: 22px; }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 6px; font-size: .78em; font-weight: 700; }
        .badge-job  { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
    </style>
</head>
<body>
 
<div id="wrapper">
    <header>
        <h1><a href="alumni_events.php">Alumni Connection</a></h1>
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
</div>
 
<div class="page-title">Alumni Connection</div>
<div class="page-sub">Browse events, job postings, and mentors shared by alumni and faculty.</div>
<div class="role-badge"><span>Student View</span></div>
 
<!-- ── UPCOMING EVENTS ── -->
<div class="section">
    <div class="section-heading centered">Upcoming Events</div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>Title</th><th>Date</th><th>Location</th><th>Posted By</th><th>Description</th></tr>
            </thead>
            <tbody>
                <?php if (empty($events)): ?>
                    <tr class="empty-row"><td colspan="5">No upcoming events</td></tr>
                <?php else: ?>
                    <?php foreach ($events as $e): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($e["title"]); ?></td>
                        <td><?php echo htmlspecialchars($e["event_date"]); ?></td>
                        <td><?php echo htmlspecialchars($e["location"]); ?></td>
                        <td><?php echo htmlspecialchars($e["posted_by"]); ?></td>
                        <td><?php echo htmlspecialchars($e["description"]); ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
 
<!-- ── JOB BOARD ── -->
<div class="section">
    <div class="section-heading centered">Job Board</div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>Title</th><th>Company</th><th>Location</th><th>Type</th><th>Posted By</th><th>Description</th></tr>
            </thead>
            <tbody>
                <?php if (empty($jobs)): ?>
                    <tr class="empty-row"><td colspan="6">No job postings yet</td></tr>
                <?php else: ?>
                    <?php foreach ($jobs as $j): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($j["title"]); ?></td>
                        <td><?php echo htmlspecialchars($j["company"]); ?></td>
                        <td><?php echo htmlspecialchars($j["location"]); ?></td>
                        <td><span class="badge badge-job"><?php echo htmlspecialchars($j["job_type"]); ?></span></td>
                        <td><?php echo htmlspecialchars($j["posted_by"]); ?></td>
                        <td><?php echo htmlspecialchars($j["description"]); ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
 
<!-- ── AVAILABLE MENTORS ── -->
<div class="section">
    <div class="section-heading centered">Available Mentors</div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>Mentor</th><th>Email</th><th>Industry</th></tr>
            </thead>
            <tbody>
                <?php if (empty($mentors)): ?>
                    <tr class="empty-row"><td colspan="3">No mentors available right now</td></tr>
                <?php else: ?>
                    <?php foreach ($mentors as $m): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($m["mentor_name"]); ?></td>
                        <td><?php echo htmlspecialchars($m["mentor_email"]); ?></td>
                        <td><?php echo htmlspecialchars($m["industry"]); ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
 
</body>
</html>
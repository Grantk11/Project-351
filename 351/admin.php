<?php
session_start();

// Include database connection
$db_path = "dbconnect.php";
if (!file_exists($db_path)) {
    die("dbconnect.php not found at: " . $db_path);
}
require_once $db_path;

// Ensure only admins can access
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header("Location: /dashboard.php?error=access_denied");
    exit;
}

$message = "";

// ── Handle actions ───────────────────────────────────────────────────────────

// Delete a user
if (isset($_GET["delete_user"])) {
    $uid = (int) $_GET["delete_user"];
    if ($uid === (int) $_SESSION["user_id"]) {
        $message = "error:You cannot delete your own account.";
    } else {
        $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$uid]);
        $message = "success:User deleted.";
    }
}

// Approve or deny a trip
if (isset($_GET["trip_action"], $_GET["trip_id"])) {
    $tid    = (int) $_GET["trip_id"];
    $action = $_GET["trip_action"];
    if (in_array($action, ["Approved", "Denied"], true)) {
        $pdo->prepare("UPDATE Trip SET Status = ? WHERE TripID = ?")
            ->execute([$action, $tid]);
        $message = "success:Trip marked as " . $action . ".";
    }
}

// Approve or deny a receipt
if (isset($_GET["receipt_action"], $_GET["receipt_id"])) {
    $rid    = (int) $_GET["receipt_id"];
    $action = $_GET["receipt_action"];
    if (in_array($action, ["Approved", "Denied"], true)) {
        $pdo->prepare("UPDATE Receipt SET Status = ? WHERE ReceiptID = ?")
            ->execute([$action, $rid]);
        $message = "success:Reimbursement marked as " . $action . ".";
    }
}



// ── Fetch data ───────────────────────────────────────────────────────────────

$users = $pdo->query(
    "SELECT id, first_name, last_name, email, Position
     FROM users
     ORDER BY id DESC"
)->fetchAll(PDO::FETCH_ASSOC);

$trips = $pdo->query(
    "SELECT t.*, u.first_name, u.last_name, u.email
     FROM trip t
     JOIN users u ON t.user_id = u.id
     ORDER BY t.TripID DESC"
)->fetchAll(PDO::FETCH_ASSOC);

$receipts = $pdo->query(
    "SELECT r.*, t.user_id, u.first_name, u.last_name, u.email
     FROM Receipt r
     LEFT JOIN Trip t ON r.TripID = t.TripID
     LEFT JOIN users u ON t.user_id = u.id
     ORDER BY r.ReceiptID DESC"
)->fetchAll(PDO::FETCH_ASSOC);

// Split message type
$msg_type = "";
$msg_text = "";
if ($message) {
    [$msg_type, $msg_text] = explode(":", $message, 2);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel — CNU</title>
    <link rel="stylesheet" href="351.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: #f8fafc; color: #111827; padding: 40px 24px 80px; }
        .page-title { font-size: 2rem; font-weight: 800; text-align: center; margin-bottom: 8px; }
        .page-sub { text-align: center; color: #6b7280; font-size: .9rem; margin-bottom: 36px; }
        .alert { max-width: 960px; margin: 0 auto 24px; padding: 12px 18px; border-radius: 8px; font-weight: 600; font-size: .9em; }
        .alert-success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; }
        .alert-error   { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }
        .section { max-width: 960px; margin: 0 auto 52px; }
        .section-heading { font-size: 1.1rem; font-weight: 700; color: #1e2a4a; margin-bottom: 14px; padding-bottom: 8px; border-bottom: 2px solid #e5e7eb; }
        .table-wrap { border-radius: 10px; overflow: hidden; border: 1px solid #e5e7eb; background: #fff; }
        table { width: 100%; border-collapse: collapse; font-size: .9rem; }
        thead th { background: #1e2a4a; color: #fff; padding: 12px 16px; text-align: left; font-weight: 600; font-size: .85rem; }
        tbody td { padding: 13px 16px; border-bottom: 1px solid #f3f4f6; color: #374151; vertical-align: middle; }
        tbody tr:last-child td { border-bottom: none; }
        tbody tr:hover td { background: #f9fafb; }
        .empty td { text-align: center; color: #9ca3af; font-style: italic; padding: 24px; }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 6px; font-size: .75em; font-weight: 700; }
        .badge-student   { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }
        .badge-professor { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
        .badge-alumni    { background: #faf5ff; color: #6d28d9; border: 1px solid #ddd6fe; }
        .badge-admin     { background: #fff1f2; color: #be123c; border: 1px solid #fecdd3; }
        .badge-pending   { background: #fffbeb; color: #92400e; border: 1px solid #fde68a; }
        .badge-approved  { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }
        .badge-denied    { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
        .btn { display: inline-flex; align-items: center; padding: 5px 14px; border-radius: 6px; font-size: .8rem; font-weight: 600; font-family: inherit; cursor: pointer; border: none; text-decoration: none; transition: opacity .15s; }
        .btn:hover { opacity: .85; }
        .btn-red    { background: #ef4444; color: #fff; }
        .btn-green  { background: #22c55e; color: #fff; }
        .btn-gray   { background: #6b7280; color: #fff; }
        .btn-gap { display: flex; gap: 6px; }
    </style>
</head>
<body>

<div id="wrapper">
    <header>
        <h1><a href="admin.php">Admin Panel</a></h1>
    </header>
    <div class="site-logo">
        <img src="ban.png" alt="CNU Banner">
    </div>
    <nav>
        <ul>
            <li><a href="dashboard.php">Portal</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>
</div>

<div class="page-title">Admin Panel</div>
<div class="page-sub">Manage registered users and conference travel requests.</div>

<?php if ($msg_text): ?>
    <div class="alert alert-<?php echo $msg_type; ?>">
        <?php echo htmlspecialchars($msg_text); ?>
    </div>
<?php endif; ?>

<!-- ── USERS ── -->
<div class="section">
    <div class="section-heading">Registered Users (<?php echo count($users); ?>)</div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Registered</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($users)): ?>
                    <tr class="empty"><td colspan="6">No users found</td></tr>
                <?php else: ?>
                    <?php foreach ($users as $u): ?>
                    <tr>
                        <td><?php echo $u["id"]; ?></td>
                        <td><?php echo htmlspecialchars($u["first_name"] . " " . $u["last_name"]); ?></td>
                        <td><?php echo htmlspecialchars($u["email"]); ?></td>
                        <td>
                            <span class="badge badge-<?php echo htmlspecialchars($u["Position"]); ?>">
                                <?php echo htmlspecialchars(ucfirst($u["Position"])); ?>
                            </span>
                        </td>
                        <td>—</td>
                        <td>
                            <?php if ((int)$u["id"] !== (int)$_SESSION["user_id"]): ?>
                                <a href="admin.php?delete_user=<?php echo $u["id"]; ?>"
                                   class="btn btn-red"
                                   onclick="return confirm('Delete <?php echo htmlspecialchars($u["first_name"]); ?>\'s account? This cannot be undone.')">
                                    Delete
                                </a>
                            <?php else: ?>
                                <span style="color:#9ca3af;font-size:.8em;">You</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ── TRIPS ── -->
<div class="section">
    <div class="section-heading">Conference Travel Requests (<?php echo count($trips); ?>)</div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Trip ID</th>
                    <th>Submitted By</th>
                    <th>Destination</th>
                    <th>Departure Date</th>
                    <th>Return Data</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($trips)): ?>
                    <tr class="empty"><td colspan="8">No travel requests submitted yet</td></tr>
                <?php else: ?>
                    <?php foreach ($trips as $t): ?>
                    <tr>
                        <td><?php echo $t["TripID"]; ?></td>
                        <td><?php echo htmlspecialchars($t["first_name"] . " " . $t["last_name"]); ?><br>
                            <small style="color:#6b7280"><?php echo htmlspecialchars($t["email"]); ?></small>
                        </td>
                        <td><?php echo htmlspecialchars($t["Destination"]); ?></td>
                        <td><?php echo htmlspecialchars($t["ArrivalDate"]); ?></td>
                        <td><?php echo htmlspecialchars($t["ReturnDate"]); ?></td>
                        <td>
                            <span class="badge badge-<?php echo strtolower($t["Status"]); ?>">
                                <?php echo htmlspecialchars($t["Status"]); ?>
                            </span>
                        </td>
                        <td>
                            <div class="btn-gap">
                                <?php if ($t["Status"] !== "Approved"): ?>
                                <a href="admin.php?trip_action=Approved&trip_id=<?php echo $t["TripID"]; ?>"
                                   class="btn btn-green">Approve</a>
                                <?php endif; ?>
                                <?php if ($t["Status"] !== "Denied"): ?>
                                <a href="admin.php?trip_action=Denied&trip_id=<?php echo $t["TripID"]; ?>"
                                   class="btn btn-gray">Deny</a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ── RECEIPTS ── -->
<div class="section">
    <div class="section-heading">Reimbursement Requests (<?php echo count($receipts); ?>)</div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Receipt ID</th>
                    <th>Submitted By</th>
                    <th>Trip ID</th>
                    <th>Amount</th>
                    <th>Bank Info</th>
                    <th>Date</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>
                <?php if (empty($receipts)): ?>
                    <tr class="empty">
                        <td colspan="7">No reimbursement requests found</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($receipts as $r): ?>
                        <?php $status = strtolower($r["Status"] ?? "pending"); ?>
                        <tr>
                            <td><?php echo $r["ReceiptID"]; ?></td>

                            <td>
                                <?php echo htmlspecialchars($r["first_name"] . " " . $r["last_name"]); ?><br>
                                <small style="color:#6b7280">
                                    <?php echo htmlspecialchars($r["email"]); ?>
                                </small>
                            </td>

                            <td><?php echo htmlspecialchars($r["TripID"]); ?></td>

                            <td>$<?php echo number_format($r["Amount"], 2); ?></td>

                            <td><?php echo htmlspecialchars($r["BankInfo"]); ?></td>

                            <td><?php echo htmlspecialchars($r["ReceiptDate"]); ?></td>

                            <td>
                                <span class="badge badge-<?php echo $status; ?>">
                                    <?php echo ucfirst($status); ?>
                                </span>
                            </td>

                            <td>
                                <div class="btn-gap">
                                    <?php if ($status !== "approved"): ?>
                                        <a href="admin.php?receipt_action=Approved&receipt_id=<?php echo $r["ReceiptID"]; ?>"
                                           class="btn btn-green">Approve</a>
                                    <?php endif; ?>

                                    <?php if ($status !== "denied"): ?>
                                        <a href="admin.php?receipt_action=Denied&receipt_id=<?php echo $r["ReceiptID"]; ?>"
                                           class="btn btn-gray">Deny</a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
session_start();
require "dbconnect.php";
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}
$user_id = $_SESSION["user_id"];
$stmt = $pdo->prepare("SELECT r.* FROM Receipt r INNER JOIN Trip t ON r.TripID = t.TripID WHERE t.user_id = ? ORDER BY r.ReceiptDate ASC");
$stmt->execute([$user_id]);
$receipts = $stmt->fetchAll();
$today = date("Y-m-d");
$current = [];
$past = [];
foreach ($receipts as $r) {
    if (!empty($r["ReceiptDate"]) && $r["ReceiptDate"] < $today) {
        $past[] = $r;
    } else {
        $current[] = $r;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reimbursement Requests</title>
<link rel="stylesheet" href="trip_create.css">
</head>
<body>
<div id="wrapper">
<header>
<h1>Reimbursement Requests</h1>
</header>
<div class="site-logo">
<img src="../../ban.png" alt="CNU Banner">
</div>
<nav>
<ul>
<li><a href="receipt.php">Submit Reimbursement</a></li>
<li><a href="Trip_Home.php">Back to Travel</a></li>

</ul>
</nav>
<div class="container">
<div class="section-title">Current Reimbursements</div>
<table>
<thead>
<tr>
<th>Trip ID</th>
<th>Amount</th>
<th>Bank Info</th>
<th>Date</th>
<th>Status</th>
</tr>
</thead>
<tbody>
<?php if (empty($current)): ?>
<tr><td colspan="5" class="empty">No current reimbursements</td></tr>
<?php else: ?>
<?php foreach ($current as $r): ?>
<tr>
<td><?= htmlspecialchars($r["TripID"]) ?></td>
<td>$<?= htmlspecialchars(number_format($r["Amount"], 2)) ?></td>
<td><?= htmlspecialchars($r["BankInfo"]) ?></td>
<td><?= htmlspecialchars($r["ReceiptDate"]) ?></td>
<td><span class="pill pending">Pending</span></td>
</tr>
<?php endforeach; ?>
<?php endif; ?>
</tbody>
</table>
<div class="section-title">Past Reimbursements</div>
<table>
<thead>
<tr>
<th>Trip ID</th>
<th>Amount</th>
<th>Bank Info</th>
<th>Date</th>
<th>Status</th>
</tr>
</thead>
<tbody>
<?php if (empty($past)): ?>
<tr><td colspan="5" class="empty">No past reimbursements</td></tr>
<?php else: ?>
<?php foreach ($past as $r): ?>
<tr>
<td><?= htmlspecialchars($r["TripID"]) ?></td>
<td>$<?= htmlspecialchars(number_format($r["Amount"], 2)) ?></td>
<td><?= htmlspecialchars($r["BankInfo"]) ?></td>
<td><?= htmlspecialchars($r["ReceiptDate"]) ?></td>
<td><span class="pill pending">Pending</span></td>
</tr>
<?php endforeach; ?>
<?php endif; ?>
</tbody>
</table>
</div>
</div>
</body>
</html>

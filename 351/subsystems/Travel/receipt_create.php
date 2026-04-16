<?php
session_start();
require "../../includes/dbconnect.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];

$stmt = $pdo->prepare(
    "SELECT r.* FROM Receipt r
     INNER JOIN Trip t ON r.TripID = t.TripID
     WHERE t.user_id = ?
     ORDER BY r.ReceiptDate ASC"
);
$stmt->execute([$user_id]);
$receipts = $stmt->fetchAll();

$pending  = [];
$resolved = [];

foreach ($receipts as $r) {
    $status = strtolower($r["Status"] ?? "pending");
    if ($status === "approved" || $status === "denied") {
        $resolved[] = $r;
    } else {
        $pending[] = $r;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reimbursement Requests</title>
    <link rel="stylesheet" href="../../351.css">
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

        <div class="section-title">Pending Reimbursements</div>

        <table>
            <thead>
                <tr>
                    <th>Receipt ID</th>
                    <th>Trip ID</th>
                    <th>Amount</th>
                    <th>Bank Info</th>
                    <th>Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($pending)): ?>
                    <tr><td colspan="6" class="empty">No pending reimbursements</td></tr>
                <?php else: ?>
                    <?php foreach ($pending as $r): ?>
                        <tr>
                            <td><?= htmlspecialchars($r["ReceiptID"]) ?></td>
                            <td><?= htmlspecialchars($r["TripID"]) ?></td>
                            <td>$<?= htmlspecialchars(number_format($r["Amount"], 2)) ?></td>
                            <td><?= htmlspecialchars($r["BankInfo"]) ?></td>
                            <td><?= htmlspecialchars($r["ReceiptDate"]) ?></td>
                            <td>
                                <?php $status = strtolower($r["Status"] ?? "pending"); ?>
                                <span class="pill <?= $status ?>"><?= ucfirst($status) ?></span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="section-title">Approved / Denied Reimbursements</div>

        <table>
            <thead>
                <tr>
                    <th>Receipt ID</th>
                    <th>Trip ID</th>
                    <th>Amount</th>
                    <th>Bank Info</th>
                    <th>Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($resolved)): ?>
                    <tr><td colspan="6" class="empty">No approved/denied reimbursements</td></tr>
                <?php else: ?>
                    <?php foreach ($resolved as $r): ?>
                        <tr>
                            <td><?= htmlspecialchars($r["ReceiptID"]) ?></td>
                            <td><?= htmlspecialchars($r["TripID"]) ?></td>
                            <td>$<?= htmlspecialchars(number_format($r["Amount"], 2)) ?></td>
                            <td><?= htmlspecialchars($r["BankInfo"]) ?></td>
                            <td><?= htmlspecialchars($r["ReceiptDate"]) ?></td>
                            <td>
                                <?php $status = strtolower($r["Status"] ?? "pending"); ?>
                                <span class="pill <?= $status ?>"><?= ucfirst($status) ?></span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

    </div>
</div>
</body>
</html>

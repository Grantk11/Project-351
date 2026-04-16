<?php
session_start();
require "../../includes/dbconnect.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];

$stmt = $pdo->prepare("SELECT r.* FROM Receipt r INNER JOIN Trip t ON r.TripID = t.TripID WHERE t.user_id = ? ORDER BY r.ReceiptDate ASC");
$stmt->execute([$user_id]);
$receipts = $stmt->fetchAll();

$today = date("Y-m-d");
$pending = [];
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
<style>
    
:root {
    --card:#ffffff;
    --text:#0f172a;
    --muted:#64748b;
    --border:#e5e7eb;
    --nav:#0b1220;
    --blue:#2563eb;
    --blue2:#1d4ed8;
    --shadow:0 10px 30px rgba(0,0,0,.08);
    --radius:14px;
}

body {
    margin:0;
    font-family: Arial, sans-serif;
    color:var(--text);
    background:linear-gradient(180deg,#f6f7fb,#ffffff);
}

nav {
    background:var(--nav);
    padding:16px;
    text-align:center;
    font-size:20px;
    font-weight:bold;
    color:white;
}

.container {
    max-width:750px;
    margin:50px auto;
    background:var(--card);
    padding:30px;
    border-radius:var(--radius);
    box-shadow:var(--shadow);
}

h1 {
    text-align:center;
    margin-bottom:25px;
}

label {
    display:block;
    margin-bottom:6px;
    font-weight:bold;
}

input {
    width:100%;
    padding:10px;
    margin-bottom:16px;
    border:1px solid var(--border);
    border-radius:8px;
    background:#ffffff !important;
    font-size:14px;
    height:auto !important;
    box-sizing:border-box;
}

input[type="number"] {
    appearance:textfield;
}

input[type="date"] {
    background:#ffffff;
}

button {
    width:100%;
    padding:12px;
    background:var(--blue);
    color:white;
    border:none;
    border-radius:10px;
    font-weight:bold;
    cursor:pointer;
}

button:hover {
    background:var(--blue2);
}

.error {
    background:#fee2e2;
    border:1px solid #ef4444;
    color:#991b1b;
    padding:12px;
    border-radius:8px;
    margin-bottom:20px;
    text-align:center;
}

.section-title {
    margin:30px 0 15px;
    font-size:18px;
    font-weight:bold;
    color:var(--muted);
}

table {
    width:100%;
    border-collapse:collapse;
    margin-top:20px;
}

thead th {
    background:#0f172a;
    color:white;
    padding:12px;
    text-align:left;
}

tbody td {
    padding:12px;
    border-bottom:1px solid var(--border);
}

tbody tr:hover {
    background:#f9fafb;
}

.empty {
    text-align:center;
    color:var(--muted);
    font-style:italic;
}

.btn-row {
    display:flex;
    justify-content:center;
    margin-top:20px;
}

.btn {
    background:#6b7280;
    color:white;
    padding:10px 16px;
    border-radius:10px;
    text-decoration:none;
    font-weight:bold;
}

.btn:hover {
    background:#4b5563;
}
</style>
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
                        <tr>
                            <td colspan="6" class="empty">No pending reimbursements</td>
                        </tr>
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

            <div class="section-title">Approved/Denied Reimbursements</div>

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
                        <tr>
                            <td colspan="6" class="empty">No approved/denied reimbursements</td>
                        </tr>
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

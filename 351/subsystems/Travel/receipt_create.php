<?php
session_start();
require "dbconnect.php";
$error = "";
$receipts = [];
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}
$user_id = $_SESSION["user_id"];
$tripID = $_GET['tripID'] ?? $_POST['TripID'] ?? null;
if (!$tripID) {
    die("No trip selected.");
}
$stmt = $pdo->prepare("SELECT * FROM Receipt WHERE TripID = ? ORDER BY ReceiptDate DESC");
$stmt->execute([$tripID]);
$receipts = $stmt->fetchAll();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $tripID   = $_POST["TripID"] ?? "";
    $amount   = $_POST["Amount"] ?? "";
    $bankInfo = trim($_POST["BankInfo"] ?? "");
    $date     = $_POST["Date"] ?? "";
    if (!ctype_digit((string)$tripID)) {
        $error = "Trip ID must be numeric.";
    }
    if (!$error) {
        $check = $pdo->prepare("SELECT TripID FROM Trip WHERE TripID = ?");
        $check->execute([$tripID]);
        if ($check->rowCount() === 0) {
            $error = "Trip ID does not exist.";
        }
    }
    if (!$error && (!is_numeric($amount) || $amount <= 0)) {
        $error = "Amount must be a positive number.";
    }
    if (!$error && (!ctype_digit($bankInfo) || strlen($bankInfo) !== 6)) {
        $error = "Bank info must be a 6-digit number.";
    }
    if (!$error && (!$date || strtotime($date) > time())) {
        $error = "Invalid date.";
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Reimbursement</title>
    <link rel="stylesheet" href="receipt_create.css">
</head>
<body>
<nav>Trip Management Portal</nav>
<div class="container">
    <h1>Submit Reimbursement</h1>

    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <h2 class="section-title">Reimbursements for Trip #<?= htmlspecialchars($tripID) ?></h2>
    <table>
        <thead>
            <tr>
                <th>Trip ID</th>
                <th>Amount</th>
                <th>Bank Info</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($receipts)): ?>
                <tr>
                    <td colspan="4" class="empty">No reimbursements submitted</td>
                </tr>
            <?php else: ?>
                <?php foreach ($receipts as $r): ?>
                    <tr>
                        <td><?= htmlspecialchars($r["TripID"]) ?></td>
                        <td>$<?= htmlspecialchars($r["Amount"]) ?></td>
                        <td><?= htmlspecialchars($r["BankInfo"]) ?></td>
                        <td><?= htmlspecialchars($r["ReceiptDate"]) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="btn-row">
        <a class="btn" href="trip_home.php">Back to Home</a>
    </div>
</div>
</body>
</html>
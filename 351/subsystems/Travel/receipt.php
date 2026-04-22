<?php
/*  Responsible Party: Grant */
session_start();
require "../../includes/dbconnect.php";

$error = "";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];
$tripID  = $_GET['TripID'] ?? $_POST['TripID'] ?? null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $amount   = trim($_POST["Amount"] ?? "");
    $bankInfo = trim($_POST["BankInfo"] ?? "");
    $date     = trim($_POST["Date"] ?? "");

    if (!ctype_digit((string)$tripID)) {
        $error = "Invalid Trip ID.";
    } else {
        $stmt = $pdo->prepare("SELECT TripID FROM Trip WHERE TripID = ?");
        $stmt->execute([$tripID]);
        if ($stmt->rowCount() === 0) {
            $error = "Trip ID does not exist.";
        }
    }

    if (!$error && (!is_numeric($amount) || $amount <= 0)) {
        $error = "Amount must be a positive number.";
    }

    if (!$error && (!ctype_digit($bankInfo) || strlen($bankInfo) !== 6)) {
        $error = "Bank info must be a 6-digit number.";
    }

    if (!$error) {
        if (strtotime($date) === false) {
            $error = "Invalid date format.";
        } elseif (strtotime($date) > time()) {
            $error = "Date cannot be in the future.";
        }
    }

    if (!$error) {
        $stmt = $pdo->prepare(
            "INSERT INTO Receipt (TripID, Amount, BankInfo, ReceiptDate)
             VALUES (?, ?, ?, ?)"
        );
        $stmt->execute([$tripID, $amount, $bankInfo, $date]);
        header("Location: receipt_create.php?tripID=" . urlencode($tripID));
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Reimbursement</title>
    <link rel="stylesheet" href="../../351.css">
    <style>
        .container {
            max-width: 500px;
        }
    </style>
</head>

<body>
<div id="wrapper">

    <header>
        <h1>Submit Reimbursement</h1>
    </header>

    <div class="site-logo">
        <img src="../../ban.png" alt="CNU Banner">
    </div>

    <nav>
        <ul>
            <li><a href="Trip_Home.php">Back to Travel</a></li>
            <li><a href="../../logout.php">Logout</a></li>
        </ul>
    </nav>

    <div class="container">
        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <label for="TripID">Trip ID:</label>
            <input type="number" id="TripID" name="TripID" min="1" required
                   value="<?= htmlspecialchars($_POST['TripID'] ?? $tripID ?? '') ?>">

            <label for="Amount">Amount:</label>
            <input type="number" step="0.01" min="0.01" id="Amount" name="Amount" required
                   value="<?= htmlspecialchars($_POST['Amount'] ?? '') ?>">

            <label for="BankInfo">Bank Info (6 digits):</label>
            <input type="text" id="BankInfo" name="BankInfo" maxlength="6" required
                   value="<?= htmlspecialchars($_POST['BankInfo'] ?? '') ?>">

            <label for="Date">Date:</label>
            <input type="date" id="Date" name="Date" required
                   max="<?= date('Y-m-d') ?>"
                   value="<?= htmlspecialchars($_POST['Date'] ?? '') ?>">

            <button type="submit" class="btn-full">Submit Reimbursement</button>
        </form>
    </div>

</div>
</body>
</html>

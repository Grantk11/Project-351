<?php
session_start();
require "dbconnect.php";
$error = "";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travel</title>
    <link rel="stylesheet" href="trip_home.css">
</head>

<body>
    <div id="wrapper">

        <header>
            <h1><a href="Trip_Home.php">Travel Dashboard</a></h1>
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

        <div class="container">

            <h1>Trip Management Portal</h1>
            <p>Select an option below to manage your travel requests and reimbursements.</p>

            <div class="grid">

                <div class="card">
                    <h3>Create Trip</h3>
                    <p>Submit a new business travel request.</p>
                    <a href="trip.php" class="btn">Open</a>
                </div>

                <div class="card">
                    <h3>View Trips</h3>
                    <p>See upcoming and past trips.</p>
                    <a href="trip_create.php" class="btn">Open</a>
                </div>

                <div class="card">
                    <h3>Reimbursement Requests</h3>
                    <p>View Your Reimbursement Requests</p>
                    <a href="receipt_create.php" class="btn">Open</a>
                </div>

                <div class="card">
                    <h3>Receipt</h3>
                    <p>Upload your receipts for a reimbursement </p>
                    <a href="receipt.php" class="btn">Open</a>
                </div>

            </div>

        </div>

    </div>
</body>
</html>

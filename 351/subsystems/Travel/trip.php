<?php
session_start();
require "dbconnect.php";

$error = "";
$success = "";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $destination = trim($_POST["Destination"] ?? "");
    $arrivalDate = $_POST["ArrivalDate"] ?? "";
    $returnDate  = $_POST["ReturnDate"] ?? "";

    if (!$destination || !$arrivalDate || !$returnDate) {
        $error = "Please fill out all fields.";
    } elseif ((int)date("Y", strtotime($arrivalDate)) < 2026) {
        $error = "Arrival year must be 2026 or later.";
    } elseif (strtotime($returnDate) < strtotime($arrivalDate)) {
        $error = "Return date must be after arrival date.";
    }

    if (!$error) {
        $stmt = $pdo->prepare(
            "INSERT INTO trip (user_id, Destination, ArrivalDate, ReturnDate, Status)
             VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->execute([$user_id, $destination, $arrivalDate, $returnDate, 'Pending']);

        header("Location: trip_create.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Trip</title>
    <link rel="stylesheet" href="trip.css">
</head>

<body>
    <div id="wrapper">

        <div class="site-logo">
            <img src="../../ban.png" alt="CNU Banner">
        </div>

        <nav>
            <ul>
                <li><a href="Trip_Home.php">Back to Travel</a></li>
            </ul>
        </nav>

        <div class="container">
            <h1>Create a Trip</h1>

            <?php if ($error): ?>
                <div class="error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <label for="Destination">Destination:</label><br>
                <input 
                    type="text" 
                    id="Destination" 
                    name="Destination" 
                    maxlength="100" 
                    required
                    value="<?= htmlspecialchars($_POST['Destination'] ?? '') ?>"
                ><br><br>

                <label for="ArrivalDate">Arrival Date:</label><br>
                <input 
                    type="date" 
                    id="ArrivalDate" 
                    name="ArrivalDate" 
                    required
                    value="<?= htmlspecialchars($_POST['ArrivalDate'] ?? '') ?>"
                ><br><br>

                <label for="ReturnDate">Return Date:</label><br>
                <input 
                    type="date" 
                    id="ReturnDate" 
                    name="ReturnDate" 
                    required
                    value="<?= htmlspecialchars($_POST['ReturnDate'] ?? '') ?>"
                ><br><br>

                <button type="submit">Create Trip</button>
            </form>

        </div>
    </div>
</body>
</html>

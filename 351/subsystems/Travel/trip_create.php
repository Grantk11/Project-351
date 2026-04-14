<?php
session_start();
require "dbconnect.php";
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}
$user_id = $_SESSION["user_id"];
$created = $_GET['created'] ?? null;
$stmt = $pdo->prepare("SELECT * FROM trip WHERE user_id = ? ORDER BY ArrivalDate ASC");
$stmt->execute([$user_id]);
$trips = $stmt->fetchAll();
$today = date("Y-m-d");
$upcoming = [];
$past = [];
foreach ($trips as $t) {
    $endDate = !empty($t["ReturnDate"]) ? $t["ReturnDate"] : $t["ArrivalDate"];
    if (!empty($endDate) && $endDate < $today) {
        $past[] = $t;
    } else {
        $upcoming[] = $t;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Trips</title>
<link rel="stylesheet" href="trip_create.css">
</head>
<body>
<div id="wrapper">
<header>
<h1>My Trips</h1>
</header>
<div class="site-logo">
<img src="../../ban.png" alt="CNU Banner">
</div>
<nav>
<ul>
<li><a href="trip.php">Create New Trip</a></li>
</ul>
</nav>
<div class="container">
<?php if ($created): ?>
<div class="success">Trip successfully created!</div>
<?php endif; ?>
<div class="section-title">PendingTrips</div>
<table>
<thead>
<tr>
<th>Trip ID</th>
<th>Destination</th>
<th>Arrival</th>
<th>Return</th>
<th>Status</th>
</tr>
</thead>
<tbody>
<?php if (empty($upcoming)): ?>
<tr><td colspan="5" class="empty">No pending trips</td></tr>
<?php else: ?>
<?php foreach ($upcoming as $trip): ?>
<tr>
<td><?= htmlspecialchars($trip["TripID"]) ?></td>
<td><?= htmlspecialchars($trip["Destination"]) ?></td>
<td><?= htmlspecialchars($trip["ArrivalDate"]) ?></td>
<td><?= htmlspecialchars($trip["ReturnDate"]) ?></td>
<td><span class="pill <?= strtolower($trip["Status"]) ?>"><?= htmlspecialchars($trip["Status"]) ?></span></td>
<?php endforeach; ?>
<?php endif; ?>
</tbody>
</table>
<div class="section-title">Approved/Denied Trips</div>
<table>
<thead>
<tr>
<th>Trip ID</th>
<th>Destination</th>
<th>Arrival</th>
<th>Return</th>
<th>Status</th>
</tr>
</thead>
<tbody>
<?php if (empty($past)): ?>
<tr><td colspan="5" class="empty">No approved/denied trips</td></tr>
<?php else: ?>
<?php foreach ($past as $trip): ?>
<tr>
<td><?= htmlspecialchars($trip["TripID"]) ?></td>
<td><?= htmlspecialchars($trip["Destination"]) ?></td>
<td><?= htmlspecialchars($trip["ArrivalDate"]) ?></td>
<td><?= htmlspecialchars($trip["ReturnDate"]) ?></td>
<td><span class="pill <?= strtolower($trip["Status"]) ?>"><?= htmlspecialchars($trip["Status"]) ?></span></td>

</tr>
<?php endforeach; ?>
<?php endif; ?>
</tbody>
</table>
</div>
</div>
</body>
</html>

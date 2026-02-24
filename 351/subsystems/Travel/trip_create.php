<?php
session_start();

$error = "";

if (!isset($_SESSION["trips"])) {
    $_SESSION["trips"] = [];
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $employeeID  = trim($_POST["EmploID"] ?? "");
    $destination = trim($_POST["EmployeeDestination"] ?? "");
    $arrivalDate = $_POST["Arrival_Date"] ?? "";
    $returnDate  = $_POST["Return_Date"] ?? "";

    if (!ctype_digit($employeeID) || strlen($employeeID) !== 8) {
        $error = "Not a valid ID (must be exactly 8 digits).";
    }

    if (!$error && !empty($arrivalDate)) {
        $year = (int)date("Y", strtotime($arrivalDate));
        if ($year < 2026) {
            $error = "Arrival year must be 2026 or later.";
        }
    }

    if (!$error && $arrivalDate && $returnDate) {
        if (strtotime($returnDate) < strtotime($arrivalDate)) {
            $error = "Return date must be after arrival date.";
        }
    }

    if (!$error) {
        $_SESSION["trips"][] = [
            "TripID"      => rand(10000000, 99999999),
            "Destination" => $destination,
            "Arrival"     => $arrivalDate,
            "Return"      => $returnDate,
            "Status"      => "Pending"
        ];

        header("Location: trip_create.php");
        exit();
    }
}

$today = date("Y-m-d");
$upcoming = [];
$past = [];

foreach ($_SESSION["trips"] as $t) {
    if ($t["Return"] < $today) {
        $past[] = $t;
    } else {
        $upcoming[] = $t;
    }
}
?>
<!doctype html>
<html lang="en">

<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

?>

<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>351</title>
    <link rel="stylesheet" href="../../351.css">
</head>
<body>
    <div id="wrapper">
        <header>
            <h1><a href="../../dashboard.php">351 System Portal</a></h1>
        </header>

        <div class="site-logo">Logo Here?</div>




<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Trip Status</title>

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
    max-width:950px;
    margin:40px auto;
    background:var(--card);
    padding:30px;
    border-radius:var(--radius);
    box-shadow:var(--shadow);
}

h1 {
    text-align:center;
    margin-bottom:30px;
}

.section-title {
    margin:40px 0 15px;
    font-size:18px;
    font-weight:bold;
    color:var(--muted);
}

table {
    width:100%;
    border-collapse:collapse;
    margin-bottom:25px;
}

thead th {
    background:#0f172a;
    color:white;
    padding:14px;
    text-align:left;
}

tbody td {
    padding:14px;
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

.pill {
    padding:6px 12px;
    border-radius:999px;
    font-size:12px;
    font-weight:bold;
}

.pending {
    background:#fffbeb;
    color:#92400e;
}

.approved {
    background:#ecfdf5;
    color:#065f46;
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

.btn-row {
    display:flex;
    justify-content:center;
    gap:14px;
    margin-top:30px;
}

.btn {
    background:var(--blue);
    color:white;
    padding:12px 18px;
    border-radius:10px;
    text-decoration:none;
    font-weight:bold;
}

.btn:hover {
    background:var(--blue2);
}

.secondary {
    background:#6b7280;
}

.secondary:hover {
    background:#4b5563;
}

</style>
</head>

<body>



<div class="container">

<h1>Your Trips</h1>

<?php if ($error): ?>
<div class="error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="section-title">Upcoming Trips</div>

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
<tr>
<td colspan="5" class="empty">No upcoming trips</td>
</tr>
<?php else: ?>
<?php foreach ($upcoming as $trip): ?>
<tr>
<td><?= htmlspecialchars($trip["TripID"]) ?></td>
<td><?= htmlspecialchars($trip["Destination"]) ?></td>
<td><?= htmlspecialchars($trip["Arrival"]) ?></td>
<td><?= htmlspecialchars($trip["Return"]) ?></td>
<td><span class="pill pending"><?= $trip["Status"] ?></span></td>
</tr>
<?php endforeach; ?>
<?php endif; ?>
</tbody>
</table>


<div class="section-title">Past Trips</div>

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
<tr>
<td colspan="5" class="empty">No past trips</td>
</tr>
<?php else: ?>
<?php foreach ($past as $trip): ?>

<tr>
<td><?= htmlspecialchars($trip["TripID"]) ?></td>
<td><?= htmlspecialchars($trip["Destination"]) ?></td>
<td><?= htmlspecialchars($trip["Arrival"]) ?></td>
<td><?= htmlspecialchars($trip["Return"]) ?></td>
<td><span class="pill approved">Approved</span></td>
</tr>

<?php endforeach; ?>
<?php endif; ?>
</tbody>
</table>


<div class="btn-row">
<a class="btn" href="trip.php">Create Another Trip</a>
<a class="btn secondary" href="trip_home.php">Back to Home Page</a>


</div>

</div>
</body>
</html>



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
<title>Home | Trip Management Portal</title>

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
    font-size:22px;
    font-weight:bold;
    color:white;
}

.container {
    max-width:900px;
    margin:60px auto;
    background:var(--card);
    padding:40px;
    border-radius:var(--radius);
    box-shadow:var(--shadow);
    text-align:center;
}

h1 {
    margin-bottom:10px;
}

p {
    color:var(--muted);
    margin-bottom:35px;
}

.grid {
    display:grid;
    grid-template-columns: repeat(auto-fit,minmax(220px,1fr));
    gap:20px;
}

.card {
    border:1px solid var(--border);
    border-radius:var(--radius);
    padding:30px 20px;
    transition:.2s;
}

.card:hover {
    transform:translateY(-5px);
    box-shadow:0 12px 25px rgba(0,0,0,.08);
}

.card h3 {
    margin-bottom:10px;
}

.btn {
    display:inline-block;
    margin-top:15px;
    background:var(--blue);
    color:white;
    padding:10px 18px;
    border-radius:10px;
    text-decoration:none;
    font-weight:bold;
}

.btn:hover {
    background:var(--blue2);
}
</style>
</head>

<body>

<nav>Trip Management Portal</nav>

<div class="container">

<h1>Welcome</h1>
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
<h3>Reimbursement</h3>
<p>Submit expenses for completed trips.</p>
<a href="reimbursement.php" class="btn">Open</a>
</div>

<div class="card">
<h3>Account</h3>
<p>Manage profile or employee settings.</p>
<a href="#" class="btn">Open</a>
</div>

</div>

</div>

</body>
</html>

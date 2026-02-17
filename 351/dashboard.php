<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>351 System Dashboard</title>
    <link rel="stylesheet" href="351.css">
    <style>
        body { font-family: Arial, sans-serif; background:#f4f4f4; margin:0; }
        header { background:#222; color:white; padding:15px; }
        .container { padding:40px; }
        .cards { display:flex; gap:20px; flex-wrap:wrap; }
        .card {
            background:white;
            padding:30px;
            border-radius:8px;
            box-shadow:0 4px 10px rgba(0,0,0,0.1);
            flex:1;
            min-width:250px;
            text-align:center;
        }
        .card a {
            font-weight:bold;
            color:#333;
            display:block;
            margin-top:40px;
        }
        .logout { float:right; color:white; text-decoration:none; }
    </style>
</head>
<body>

<header>
    <h2 style="display:inline;">351 Information System</h2>
    <a class="logout" href="logout.php">Logout</a>
</header>

<div class="container">
    <h1>Dashboard</h1>
    <p>Welcome to your system control panel.</p>

    <div class="cards">

        <div class="card">
            <h3>Advising Subsystem</h3>
            <p>View Academic Advising Details</p>
            <a href="subsystems/Advising/advising_index.php">CNU Advising</a>
        </div>

        <div class="card">
            <h3>Alumni Connection</h3>
            <p>Connect with CNU Alum for Professional Development</p>
            <a href="subsystems/alumni/alumni_index.php">CNU Connection</a>
        </div>

        <div class="card">
            <h3>Conference Travel</h3>
            <p>View Conference Trips and Upload Travel Reciepts</p>
            <a href="subsystems/travel/travel_index.php">#TravelCNU</a>
        </div>

    </div>
</div>

		<img src="cnu_sails.png" alt="CNU Sails">
</body>
</html>

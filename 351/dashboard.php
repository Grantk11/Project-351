<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

echo "<marquee>Welcome, " . 
     htmlspecialchars($_SESSION["first_name"]) . 
     "</marquee>";
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
	<a class="logout" href="my_profile.php">My Profile</a><br>
    <a class="logout" href="logout.php">Logout</a><br>
</header>

<div class="container">
    <h1>Dashboard</h1>
    <p>Welcome to your CNU Dashboard, interact with the image below to enter a subsystem.</p>
	
<div class="dash-logo">
<img src="sails_map.jpg" usemap="#image-map">

<map name="image-map">
    <area target="" alt="Advising" title="Advising" href="subsystems/Advising/advising_index.php" coords="165,449,211,531,404,267,333,230" shape="poly">
    <area target="" alt="Alumni" title="Alumni" href="subsystems/alumni/alumni_index.php" coords="366,445,416,515,566,298,497,253,429,352,406,392" shape="poly">
    <area target="" alt="Travel" title="Travel" href="subsystems/travel/travel_index.php" coords="482,540,709,199,758,228,739,320,771,351,684,516,616,529,541,576" shape="poly">
</map>
</div>

<p>Alternatively, click these links</p>
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

	
</body>
</html>

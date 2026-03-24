<?php
session_start();
require_once __DIR__ . "/includes/auth_check.php";
require_login(); // Must be logged in; any role is fine for the dashboard itself
 
$role = $_SESSION["role"] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — CNU Portal</title>
    <link rel="stylesheet" href="351.css">
</head>
<body>
<div id="wrapper">
 
    <header>
        <h1><a href="dashboard.php">CNU Portal</a></h1>
    </header>
 
    <div class="site-logo">
        <img src="ban.png" alt="CNU Banner">
    </div>
 
    <nav>
        <ul>
            <li><a href="my_profile.php">My Profile</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>
 
    <main>
 
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION["first_name"]); ?></h2>
        <p><em>Logged in as: <?php echo htmlspecialchars(ucfirst($role)); ?></em></p>
 
        <?php if (isset($_GET['error']) && $_GET['error'] === 'access_denied'): ?>
            <p style="color:red;">You don't have permission to access that page.</p>
        <?php endif; ?>
 
        <p>Interact with the campus map to enter a subsystem.</p>
 
        <div class="dash-logo">
            <img src="sails_map.jpg" usemap="#image-map" class="map-img">
 
            <map name="image-map">
 
                <?php if (has_role(['student', 'professor'])): ?>
                <area alt="Advising" href="subsystems/Advising/advising_index.php"
                      coords="165,449,211,531,404,267,333,230" shape="poly">
                <?php endif; ?>
 
                <?php
                // Alumni system: all roles can enter, but land on different pages
                if ($role === 'student') {
                    $alumni_link = "subsystems/alumni/alumni_events.php";
                } else {
                    $alumni_link = "subsystems/alumni/alumni_index.php";
                }
                ?>
                <area alt="Alumni" href="<?php echo $alumni_link; ?>"
                      coords="366,445,416,515,566,298,497,253,429,352,406,392" shape="poly">
 
                <area alt="Travel" href="subsystems/travel/trip_home.php"
                      coords="482,540,709,199,758,228,739,320,771,351,684,516,616,529,541,576"
                      shape="poly">
 
            </map>
        </div>
 
        <h3>Subsystem Links</h3>
 
        <ul class="subsystem-links">
 
            <?php if (has_role(['student', 'professor'])): ?>
            <li><a href="subsystems/Advising/advising_index.php">Course Advising System</a></li>
            <?php endif; ?>
 
            <?php if ($role === 'student'): ?>
            <li><a href="subsystems/alumni/alumni_events.php">Alumni Events</a></li>
            <?php else: ?>
            <li><a href="subsystems/alumni/alumni_index.php">Alumni Connection System</a></li>
            <?php endif; ?>
 
            <li><a href="subsystems/travel/trip_home.php">Conference Travel System</a></li>
 
        </ul>
 
    </main>
 
    <footer>
        <br>sam.kirkhorn.23@cnu.edu
        <br>grant.kochan.23@cnu.edu
        <br>alijah.levenberry.23@cnu.edu
        <br>colin.anderson.22@cnu.edu
    </footer>
 
</div>
</body>
</html>
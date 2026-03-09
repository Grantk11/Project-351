<?php
session_start();

if (isset($_SESSION["user_id"])) {
    header("Location: dashboard.php");
    exit;
}
?>

<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>351</title>
    <link rel="stylesheet" href="351.css">
</head>
<body>
    <div id="wrapper">
        <header>
            <h1><a href="index.php">CNU Home</a></h1>
        </header>

        <div class="site-logo">
		<img src="ban.png" alt="CNU Banner">
		</div>


        <nav>
            <ul>
                <li><a href="login.php">Log In</a></li>
				<li><a href="register.php">Register</a></li>
             
            </ul>
        </nav>

        <main>
            <h2>Welcome to our website</h2><br>
            <p><b>You must log in to access the following subsystems</b></p>
            
				<ul>
                <li>Alumni Connection System</li>
                <li>Course Advising System</li>
                <li>Travel Reimbursement System</li>
            </ul>
<?php
           
                ?>
</div>
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



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
            <h1><a href="index.php">Home Page</a></h1>
        </header>

        <div class="site-logo">
		<img src="ban.png" alt="CNU Banner">
		</div>


        <nav>
            <ul>
                <a href="login.php">Log In</a><br>
				<a href="register.php">Register</a>
             
            </ul>
        </nav>

        <main>
            <h2>Welcome to our website</h2>
            <p>This is our project</p>
            
                <b>Subsystems include...</b>
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



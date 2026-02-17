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
		
<h1>Please Input your Email and Password</h1>
<form method="POST" action="register.php">
    <input type="email" name="email" required>
    <input type="password" name="password" required>
    <button type="submit">Register</button>
</form>
</html>
<?php
session_start();
require "includes/dbconnect.php"; // MUST define $pdo

// Make sure form was submitted
if (!isset($_POST['email'], $_POST['password'])) {
    die("Please Try Again");
}

$email = $_POST['email'];
$password = $_POST['password'];

// Hash password
$passwordHash = password_hash($password, PASSWORD_DEFAULT);

// Verification token
$token = bin2hex(random_bytes(32));

// Insert user
$stmt = $pdo->prepare(
    "INSERT INTO users (email, password, verify_token) VALUES (?, ?, ?)"
);
$stmt->execute([$email, $passwordHash, $token]);

// Verification link
$verifyLink = "http://localhost/351/verify.php?token=$token";

// Send email
mail(
    $email,
    "Verify your account",
    "Click this link to verify your account:\n$verifyLink"
);

// Redirect
header("Location: login.php");
exit();

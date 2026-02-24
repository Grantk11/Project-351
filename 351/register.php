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
		
<h2>Create Account</h2>

<?php if (!empty($error)): ?>
    <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
<?php endif; ?>

<form method="POST">
    <input type="text" name="first_name" placeholder="First Name" required>
    <input type="text" name="last_name" placeholder="Last Name" required><br>
    <input type="email" name="email" placeholder="Email" required><br>
    <input type="password" name="password" placeholder="Password" required><br>
    <button type="submit">Register</button>
</form>
</html>
<?php
session_start();
require "includes/dbconnect.php";

$error = "";

// Only run registration logic if form was submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (!isset($_POST['email'], $_POST['password'], $_POST['first_name'], $_POST['last_name'])) {
        $error = "Please fill out all fields.";
    } else {

        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $firstName = trim($_POST['first_name']);
        $lastName = trim($_POST['last_name']);

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $token = bin2hex(random_bytes(32));

        $stmt = $pdo->prepare(
            "INSERT INTO users (email, password, first_name, last_name, verify_token)
             VALUES (?, ?, ?, ?, ?)"
        );

        $stmt->execute([$email, $passwordHash, $firstName, $lastName, $token]);

        $verifyLink = "http://localhost/351/verify.php?token=$token";

        mail(
            $email,
            "Verify your account",
            "Click this link to verify your account:\n$verifyLink"
        );

        header("Location: login.php");
        exit();
    }
}
?>

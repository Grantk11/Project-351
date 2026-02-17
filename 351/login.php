<?php
session_start();
require __DIR__ . "/includes/dbconnect.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = $_POST["email"] ?? "";
    $password = $_POST["password"] ?? "";

    $stmt = $pdo->prepare(
        "SELECT id, email, password, is_verified FROM users WHERE email = ?"
    );
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);


    if ($user && password_verify($password, $user["password"])) {

        if ((int)$user["is_verified"] !== 1) {
            die("Please verify your email first.");
        }

        session_regenerate_id(true);
        $_SESSION["user_id"] = $user["id"];

        // ✅ Success message + redirect
        echo "<!DOCTYPE html>
        <html>
        <head>
            <meta charset='utf-8'>
            <meta http-equiv='refresh' content='2;url=dashboard.php'>
            <title>Login Successful</title>
        </head>
        <body>
            <h2>Login successful!</h2>
            <p>Redirecting to your dashboard...</p>
        </body>
        </html>";
        exit;
    }

    // ❗ If we reach here, login failed
    $error = "Invalid email or password";
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

<h2>Login here</h2>

<?php if ($error): ?>
    <p style="color:red;"><?php echo $error; ?></p>
<?php endif; ?>

<form method="POST">
    <input type="email" name="email" required>
    <input type="password" name="password" required>
    <button type="submit">Login</button>
</form>

</body>
</html>

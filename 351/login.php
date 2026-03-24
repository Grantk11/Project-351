<?php
session_start();
require __DIR__ . "/includes/dbconnect.php";
 
$error = "";
 
// Redirect already-logged-in users
if (isset($_SESSION["user_id"])) {
    header("Location: dashboard.php");
    exit;
}
 
if ($_SERVER["REQUEST_METHOD"] === "POST") {
 
    $email    = trim($_POST["email"]    ?? "");
    $password = $_POST["password"] ?? "";
 
    $stmt = $pdo->prepare(
        "SELECT id, email, password, first_name, last_name, Position
         FROM users
         WHERE email = ?"
    );
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
 
    if ($user && password_verify($password, $user["password"])) {
 
        session_regenerate_id(true);
 
        $_SESSION["user_id"]    = $user["id"];
        $_SESSION["email"]      = $user["email"];
        $_SESSION["first_name"] = $user["first_name"];
        $_SESSION["last_name"]  = $user["last_name"];
        $_SESSION["role"]       = $user["Position"]; // 'student' | 'professor' | 'alumni'
 
        header("Location: dashboard.php");
        exit;
    }
 
    // If we reach here, credentials were wrong
    $error = "Invalid email or password.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — CNU Portal</title>
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
 
    <main>
        <h2>Log In</h2>
 
        <?php if ($error): ?>
            <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
 
        <?php if (isset($_GET['registered'])): ?>
            <p style="color:green;">Account created! Please log in.</p>
        <?php endif; ?>
 
        <form method="POST">
            <input type="email"    name="email"    placeholder="Email"    required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <button type="submit">Login</button>
        </form>
 
        <p><a href="forgot_password.php">Forgot your password?</a></p>
        <p>No account yet? <a href="register.php">Register here</a>.</p>
    </main>
 
</div>
</body>
</html>
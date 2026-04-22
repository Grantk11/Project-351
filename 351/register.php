<?php
session_start();
require "includes/dbconnect.php";
/*  Responsible Party: Colin */
 
$error = "";
 
// Redirect already-logged-in users
if (isset($_SESSION["user_id"])) {
    header("Location: dashboard.php");
    exit;
}
 
if ($_SERVER["REQUEST_METHOD"] === "POST") {
 
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name  = trim($_POST['last_name']  ?? '');
    $email      = trim($_POST['email']      ?? '');
    $password   = $_POST['password']        ?? '';
    $role       = $_POST['role']            ?? '';
 
    $allowed_roles = ['student', 'professor', 'alumni'];
 
    if (!$first_name || !$last_name || !$email || !$password || !$role) {
        $error = "Please fill out all fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif (!in_array($role, $allowed_roles, true)) {
        $error = "Please select a valid role.";
    } else {
        // Check for duplicate email
        $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $check->execute([$email]);
        if ($check->fetch()) {
            $error = "An account with that email already exists.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
 
            $stmt = $pdo->prepare(
                "INSERT INTO users (email, first_name, last_name, password, Position)
                 VALUES (?, ?, ?, ?, ?)"
            );
            $stmt->execute([$email, $first_name, $last_name, $hash, $role]);
 
            header("Location: login.php?registered=1");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register — CNU Portal</title>
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
        <h2>Create Account</h2>
 
        <?php if ($error): ?>
            <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
 
        <form method="POST">
            <input type="text"     name="first_name" placeholder="First Name" required><br>
            <input type="text"     name="last_name"  placeholder="Last Name"  required><br>
            <input type="email"    name="email"      placeholder="Email"      required><br>
            <input type="password" name="password"   placeholder="Password"   required><br>
 
            <label for="role">I am a:</label>
            <select name="role" id="role" required>
                <option value="">-- Select your role --</option>
                <option value="student"   <?php echo (($_POST['role'] ?? '') === 'student'   ? 'selected' : ''); ?>>Student</option>
                <option value="professor" <?php echo (($_POST['role'] ?? '') === 'professor' ? 'selected' : ''); ?>>Professor</option>
                <option value="alumni"    <?php echo (($_POST['role'] ?? '') === 'alumni'    ? 'selected' : ''); ?>>Alumni</option>
            </select><br>
 
            <button type="submit">Register</button>
        </form>
 
        <p>Already have an account? <a href="login.php">Log in here</a>.</p>
    </main>
 
</div>
</body>
</html>

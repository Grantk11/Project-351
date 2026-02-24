<?php
session_start();

// Protect page (must be logged in)
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Profile</title>
</head>
<body>

<h1>My Profile</h1>

<p><strong>First Name:</strong> <?php echo htmlspecialchars($_SESSION['first_name']); ?></p>
<p><strong>Last Name:</strong> <?php echo htmlspecialchars($_SESSION['last_name']); ?></p>
<p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['email']); ?></p>

<hr>

<h2>Events I'm Interested In</h2>

<?php
// For now placeholder
echo "<p>No events yet.</p>";
?>

<br><br><h3><a href="dashboard.php">Back to Dashboard</a></h3>
</body>
</html>
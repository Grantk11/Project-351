<?php
$host = "localhost";
$db   = "sec_system";
$user = "admin_351";
$pass = "351admin";
$charset = "utf8mb4";

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    // If we reach here, connection is successful!
    echo "Connection successful!";
} catch (PDOException $e) {
    // This will tell us EXACTLY what is wrong (Access denied, Unknown database, etc.)
    die("Detailed Error: " . $e->getMessage());
}
?>

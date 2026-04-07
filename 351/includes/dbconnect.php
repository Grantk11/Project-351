<?php
$host = "ec2-3-148-84-102.us-east-2.compute.amazonaws.com";
$db   = "sec_system";       // change if needed
$user = "root";       // your MySQL user
$pass = "351admin";       // your MySQL password
$charset = "utf8mb4";

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("Database connection failed");
}
?>

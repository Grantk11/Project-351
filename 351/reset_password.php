<?php
require "includes/db.php";

$token = $_GET["token"] ?? null;

$stmt = $pdo->prepare(
    "SELECT id FROM users WHERE reset_token = ? AND reset_expires > NOW()"
);
$stmt->execute([$token]);
$user = $stmt->fetch();

if (!$user) {
    die("Invalid or expired reset link.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $hash = password_hash($_POST["password"], PASSWORD_DEFAULT);

    $stmt = $pdo->prepare(
        "UPDATE users 
         SET password = ?, reset_token = NULL, reset_expires = NULL 
         WHERE id = ?"
    );
    $stmt->execute([$hash, $user["id"]]);

    echo "Password reset successful. You may now log in.";
}
?>

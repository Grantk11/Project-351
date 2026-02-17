<?php
require "includes/db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST["email"];
    $token = bin2hex(random_bytes(32));
    $expires = date("Y-m-d H:i:s", time() + 3600);

    $stmt = $pdo->prepare(
        "UPDATE users SET reset_token = ?, reset_expires = ? WHERE email = ?"
    );
    $stmt->execute([$token, $expires, $email]);

    $link = "http://yourdomain.com/reset_password.php?token=$token";

    mail(
        $email,
        "Password Reset",
        "Click this link to reset your password:\n$link\n\nThis link expires in 1 hour."
    );

    echo "If that email exists, a reset link has been sent.";
}
?>

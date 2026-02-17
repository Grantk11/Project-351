<html>

<?php
require 'db.php';

$token = $_GET['token'];

$stmt = $pdo->prepare(
  "UPDATE users SET is_verified = 1, verify_token = NULL WHERE verify_token = ?"
);
$stmt->execute([$token]);

if ($stmt->rowCount()) {
    echo "Email verified! You can now log in.";
} else {
    echo "Invalid or expired link.";
}

?>
</html>
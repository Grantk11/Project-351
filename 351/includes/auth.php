<?php
session_start();
/*  Responsible Party: Colin
THIS MODULE IS CURRENTLY UNUSED */

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>

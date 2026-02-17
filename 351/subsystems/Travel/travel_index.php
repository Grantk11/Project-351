<html>
<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

?>

<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>351</title>
    <link rel="stylesheet" href="../../351.css">
</head>
<body>
    <div id="wrapper">
        <header>
            <h1><a href="../../dashboard.php">351 System Portal</a></h1>
        </header>

        <div class="site-logo">Logo Here?</div>





</html>
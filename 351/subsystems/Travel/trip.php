<?php
session_start();
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

  $employeeID  = $_POST["EmployeeID"] ?? "";
  $destination = $_POST["EmployeeDestination"] ?? "";
  $arrivalDate = $_POST["Arrival_Date"] ?? "";
  $returnDate  = $_POST["Return_Date"] ?? "";

  
  if (!ctype_digit((string)$employeeID) || strlen((string)$employeeID) !== 8) {
    $error = "Not a valid ID (must be exactly 8 digits).";
  }


  if (!$error && !empty($arrivalDate)) {
    $year = (int)date("Y", strtotime($arrivalDate));
    if ($year < 2026) {
      $error = "Arrival year must be 2026 or later.";
    }
  }


  if (!$error && !empty($arrivalDate) && !empty($returnDate)) {
    if (strtotime($returnDate) < strtotime($arrivalDate)) {
      $error = "Return date must be after arrival date.";
    }
  }


}
?>
<!doctype html>
<html lang="en">

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



<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Create Trip</title>

  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f4f6f8;
      margin: 0;
      padding: 0;
    }

    nav {
      background-color: #1f2933;
      padding: 12px;
      text-align: center;
    }

    nav a {
      color: #ffffff;
      text-decoration: none;
      margin: 0 12px;
      font-weight: bold;
    }

    nav a:hover { text-decoration: underline; }

    .container {
      max-width: 500px;
      background-color: #ffffff;
      margin: 40px auto;
      padding: 25px;
      border-radius: 8px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    h1 {
      text-align: center;
      margin-bottom: 25px;
      color: #1f2933;
    }

    .error {
      background: #fee2e2;
      border: 1px solid #ef4444;
      color: #991b1b;
      padding: 10px;
      border-radius: 6px;
      margin-bottom: 16px;
      font-weight: bold;
    }

    label {
      display: block;
      margin-bottom: 6px;
      font-weight: bold;
      color: #333;
    }

    input {
      width: 100%;
      padding: 8px;
      margin-bottom: 18px;
      border: 1px solid #ccc;
      border-radius: 4px;
      font-size: 14px;
      box-sizing: border-box;
    }

    input:focus {
      border-color: #2563eb;
      outline: none;
    }

    button {
      width: 100%;
      padding: 10px;
      background-color: #2563eb;
      color: #ffffff;
      border: none;
      border-radius: 5px;
      font-size: 16px;
      cursor: pointer;
    }

    button:hover { background-color: #1d4ed8; }

    .hero {
      text-align: center;
      margin-top: 18px;
    }
  </style>
</head>

<body>

  <div class="hero">
    <img src="travel.png" alt="travel" width="500">
  </div>

  <div class="container">
    <h1>Create a Trip</h1>


    <form action="trip_create.php" method="post">
      <label for="EmployeeID">Employee ID</label>
      <input type="text" id="EmployeeID" name="EmployeeID" maxlength="8" required>

      <label for="EmployeeDestination">Destination</label>
      <input type="text" id="EmployeeDestination" name="EmployeeDestination" maxlength="15" required>

      <label for="Arrival_Date">Arrival Date</label>
      <input type="date" id="Arrival_Date" name="Arrival_Date" required>

      <label for="Return_Date">Return Date</label>
      <input type="date" id="Return_Date" name="Return_Date" required>

      <button type="submit">Create Trip</button>
      
      
    </form>
  </div>

</body>
</html>

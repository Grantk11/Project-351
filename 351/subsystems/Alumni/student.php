<!DOCTYPE html>
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
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Student View</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: 'Inter', sans-serif; background: #ffffff; color: #111827; padding: 60px 24px 80px; }
  .page-title { font-size: 2rem; font-weight: 800; text-align: center; color: #111827; margin-bottom: 40px; }
  .section { margin-bottom: 48px; }
  .section-heading { font-size: .95rem; font-weight: 700; color: #4a6fa5; margin-bottom: 12px; }
  .centered { max-width: 780px; margin: 0 auto; }
  .who-box { max-width: 780px; margin: 0 auto 32px; display: flex; align-items: center; gap: 12px; flex-wrap: wrap; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 14px 20px; font-size: .9em; }
  .who-box label { font-weight: 600; color: #6b7280; }
  .who-box input { padding: 8px 12px; border: 1.5px solid #d1d5db; border-radius: 6px; font-size: .9em; width: 220px; font-family: inherit; }
  .who-box input:focus { outline: none; border-color: #3b5bdb; }
  .table-wrap { max-width: 780px; margin: 0 auto; border-radius: 8px; overflow: hidden; border: 1px solid #e5e7eb; }
  table { width: 100%; border-collapse: collapse; font-size: .93rem; }
  thead th { background: #1e2a4a; color: #fff; padding: 12px 18px; text-align: left; font-weight: 600; font-size: .88rem; }
  tbody td { padding: 13px 18px; border-bottom: 1px solid #f3f4f6; color: #374151; vertical-align: middle; }
  tbody tr:last-child td { border-bottom: none; }
  tbody tr:hover td { background: #f9fafb; }
  .td-act { display: flex; gap: 8px; align-items: center; }
  .empty-row td { text-align: center; color: #9ca3af; font-style: italic; padding: 22px; }
  .btn { display: inline-flex; align-items: center; justify-content: center; padding: 10px 24px; border-radius: 8px; font-size: .9rem; font-weight: 600; font-family: inherit; cursor: pointer; border: none; text-decoration: none; transition: opacity .15s; }
  .btn:hover { opacity: .88; }
  .btn-blue  { background: #3b5bdb; color: #fff; }
  .btn-gray  { background: #6b7280; color: #fff; }
  .btn-red   { background: #ef4444; color: #fff; }
  .btn-sm { padding: 5px 14px; font-size: .8rem; border-radius: 6px; }
  .badge { display: inline-block; padding: 3px 10px; border-radius: 6px; font-size: .78em; font-weight: 700; }
  .badge-interested { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }
  .badge-blue       { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
  .badge-available  { background: #fff7ed; color: #c2410c; border: 1px solid #fed7aa; }
  .bottom-actions { display: flex; justify-content: center; gap: 14px; margin-top: 12px; }
</style>
</head>
<body>

<div class="page-title">Your Dashboard</div>

<div class="who-box">
  <label>Your Name:</label>
  <input type="text" placeholder="Enter your name to track interests…">
  <button class="btn btn-blue btn-sm">Set</button>
</div>

<!-- UPCOMING EVENTS -->
<div class="section">
  <div class="section-heading centered">Upcoming Events</div>
  <div class="table-wrap">
    <table>
      <thead><tr><th>Event ID</th><th>Title</th><th>Date</th><th>Location</th><th>Posted By</th><th>Status</th></tr></thead>
      <tbody>
        <tr class="empty-row"><td colspan="6">No upcoming events</td></tr>
      </tbody>
    </table>
  </div>
</div>

<!-- JOB BOARD -->
<div class="section">
  <div class="section-heading centered">Job Board</div>
  <div class="table-wrap">
    <table>
      <thead><tr><th>Job ID</th><th>Title</th><th>Company</th><th>Location</th><th>Type</th></tr></thead>
      <tbody>
        <tr class="empty-row"><td colspan="5">No job postings yet</td></tr>
      </tbody>
    </table>
  </div>
</div>

<!-- AVAILABLE MENTORS -->
<div class="section">
  <div class="section-heading centered">Available Mentors</div>
  <div class="table-wrap">
    <table>
      <thead><tr><th>Mentor</th><th>Email</th><th>Industry</th><th>Status</th></tr></thead>
      <tbody>
        <tr class="empty-row"><td colspan="4">No mentors available right now</td></tr>
      </tbody>
    </table>
  </div>
</div>

<div class="bottom-actions">
  <a href="preview_alumni.html" class="btn btn-blue">Alumni Portal</a>
  <a href="#" class="btn btn-gray">Back to Home Page</a>
</div>

</body>

</html>

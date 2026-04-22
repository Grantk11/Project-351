<?php
/*  Responsible Party: Sam */
session_start();
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../includes/dbconnect.php';
require_login();
$role = $_SESSION['role'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alumni System — CNU Portal</title>
    <link rel="stylesheet" href="../../351.css">
    <style>
        .portal-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 24px;
            margin-top: 30px;
        }
        .portal-card {
            background: #f5f6fa;
            border-radius: 10px;
            padding: 30px 24px;
            text-align: center;
            text-decoration: none;
            color: #2f3640;
            border: 2px solid #edeff2;
            transition: all 0.3s ease;
        }
        .portal-card:hover {
            border-color: #00a8ff;
            box-shadow: 0 4px 16px rgba(0,168,255,0.15);
            transform: translateY(-3px);
        }
        .portal-card .icon { font-size: 2.5rem; margin-bottom: 12px; }
        .portal-card h3 { margin: 0 0 8px; font-size: 1.1rem; font-weight: 700; }
        .portal-card p { margin: 0; font-size: 0.88rem; color: #57606f; }
        .role-badge {
            display: inline-block;
            background: #00a8ff;
            color: #fff;
            font-size: 0.78rem;
            font-weight: 700;
            padding: 3px 12px;
            border-radius: 20px;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
    </style>
</head>
<body>
<div id="wrapper">

    <header>
        <h1><a href="alumni_index.php">Alumni Connection System</a></h1>
    </header>

    <div class="site-logo">
        <img src="../../ban.png" alt="CNU Banner">
    </div>

    <nav>
        <ul>
            <li><a href="../../dashboard.php">Back to Portal</a></li>
            <li><a href="../../logout.php">Logout</a></li>
        </ul>
    </nav>

    <main>
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION['first_name']); ?></h2>
        <span class="role-badge"><?php echo htmlspecialchars(ucfirst($role)); ?></span>

        <?php if ($role === 'student'): ?>
            <p>Browse upcoming events, job postings, and mentorship opportunities shared by alumni and faculty.</p>
        <?php else: ?>
            <p>Manage events, job postings, and mentorship opportunities for the CNU community.</p>
        <?php endif; ?>

        <div class="portal-grid">
            <a href="events.php" class="portal-card">
                <div class="icon">📅</div>
                <h3>Upcoming Events</h3>
                <p><?php echo $role === 'student' ? 'Browse campus and alumni events' : 'Post and manage events'; ?></p>
            </a>
            <a href="jobs.php" class="portal-card">
                <div class="icon">💼</div>
                <h3>Job Board</h3>
                <p><?php echo $role === 'student' ? 'Explore job and internship listings' : 'Post and manage job listings'; ?></p>
            </a>
            <a href="mentors.php" class="portal-card">
                <div class="icon">🤝</div>
                <h3>Mentorship</h3>
                <p><?php echo $role === 'student' ? 'Find a mentor in your field' : 'Add and manage mentors'; ?></p>
            </a>
        </div>
    </main>

    <footer>
        <br>sam.kirkhorn.23@cnu.edu
        <br>grant.kochan.23@cnu.edu
        <br>alijah.levenberry.23@cnu.edu
        <br>colin.anderson.22@cnu.edu
    </footer>

</div>
</body>
</html>

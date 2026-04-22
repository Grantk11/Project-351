<?php
session_start();
/*  Responsible Party: Colin */

// Protect page (must be logged in)
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Database connection
$host    = "localhost";
$db      = "sec_system";
$db_user = "admin_351";
$db_pass = "351admin";
$charset = "utf8mb4";
$dsn     = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
try {
    $pdo = new PDO($dsn, $db_user, $db_pass, $options);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

$user_id = $_SESSION['user_id'];
$role    = $_SESSION['role'] ?? '';

// ── Alumni: Events RSVPed to (students only) ────────────────────────────────
$rsvped_events = [];
if ($role === 'student') {
    $stmt = $pdo->prepare(
        "SELECT e.title, e.event_date, e.location, e.posted_by
           FROM event_rsvps r
           JOIN events e ON e.id = r.event_id
          WHERE r.user_id = ?
       ORDER BY e.event_date ASC"
    );
    $stmt->execute([$user_id]);
    $rsvped_events = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// ── Alumni: Jobs expressed interest in (students only) ──────────────────────
$applied_jobs = [];
if ($role === 'student') {
    $stmt = $pdo->prepare(
        "SELECT j.title, j.company, j.location, j.job_type, j.posted_by
           FROM job_applications a
           JOIN jobs j ON j.id = a.job_id
          WHERE a.user_id = ?
       ORDER BY j.created_at DESC"
    );
    $stmt->execute([$user_id]);
    $applied_jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// ── Travel: Upcoming trips (all roles) ──────────────────────────────────────
$upcoming_trips = [];
$stmt = $pdo->prepare(
    "SELECT TripID, Destination, ArrivalDate, ReturnDate, Status
       FROM Trip
      WHERE user_id = ?
        AND ReturnDate >= CURDATE()
   ORDER BY ArrivalDate ASC"
);
$stmt->execute([$user_id]);
$upcoming_trips = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ── Advising: Booked appointments (students) ─────────────────────────────────
$advising_appointments = [];
if ($role === 'student') {
    $stmt = $pdo->prepare(
        "SELECT s.slot_date, s.slot_time, s.location, s.notes,
                CONCAT(u.first_name, ' ', u.last_name) AS faculty_name
           FROM advising_slots s
           JOIN users u ON u.id = s.faculty_id
          WHERE s.student_id = ? AND s.is_booked = 1
       ORDER BY s.slot_date ASC, s.slot_time ASC"
    );
    $stmt->execute([$user_id]);
    $advising_appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// ── Advising: Slots created by professor ─────────────────────────────────────
$advising_slots = [];
if ($role === 'professor') {
    $stmt = $pdo->prepare(
        "SELECT s.slot_date, s.slot_time, s.location, s.notes,
                s.is_booked,
                CONCAT(u.first_name, ' ', u.last_name) AS student_name
           FROM advising_slots s
           LEFT JOIN users u ON u.id = s.student_id
          WHERE s.faculty_id = ?
       ORDER BY s.slot_date ASC, s.slot_time ASC"
    );
    $stmt->execute([$user_id]);
    $advising_slots = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile — CNU Portal</title>
    <link rel="stylesheet" href="351.css">
    <style>
        /* ── Design tokens ── */
        :root {
            --navy:    #1a2340;
            --blue:    #00a8ff;
            --blue2:   #0088cc;
            --green:   #22c55e;
            --amber:   #f59e0b;
            --red:     #ef4444;
            --slate:   #57606f;
            --bg:      #f5f6fa;
            --card:    #ffffff;
            --border:  #e8eaf0;
            --radius:  10px;
            --shadow:  0 2px 14px rgba(26,35,64,.08);
            --transition: .2s ease;
        }

        *, *::before, *::after { box-sizing: border-box; }

        /* ── Profile header card ── */
        .profile-header {
            display: flex;
            align-items: center;
            gap: 22px;
            background: var(--navy);
            color: #fff;
            border-radius: var(--radius);
            padding: 28px 32px;
            margin-bottom: 32px;
        }
        .avatar {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: var(--blue);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            font-weight: 800;
            color: #fff;
            flex-shrink: 0;
            letter-spacing: -1px;
        }
        .profile-info h2 {
            margin: 0 0 4px;
            font-size: 1.4rem;
            font-weight: 800;
            letter-spacing: -.3px;
        }
        .profile-info .email {
            font-size: .88rem;
            opacity: .75;
            margin-bottom: 8px;
        }
        .role-badge {
            display: inline-block;
            background: rgba(255,255,255,.15);
            border: 1px solid rgba(255,255,255,.25);
            color: #fff;
            font-size: .72rem;
            font-weight: 700;
            padding: 3px 12px;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: .8px;
        }

        /* ── Section wrapper ── */
        .section { margin-bottom: 36px; }
        .section-heading {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: .82rem;
            font-weight: 800;
            color: var(--navy);
            text-transform: uppercase;
            letter-spacing: .9px;
            margin-bottom: 14px;
            padding-bottom: 8px;
            border-bottom: 2px solid var(--border);
        }
        .section-heading .icon {
            font-size: 1.1rem;
        }
        .section-heading .count-badge {
            margin-left: auto;
            background: var(--blue);
            color: #fff;
            font-size: .7rem;
            font-weight: 700;
            padding: 2px 9px;
            border-radius: 20px;
        }

        /* ── Data table ── */
        .table-wrap {
            border-radius: var(--radius);
            overflow: hidden;
            border: 1px solid var(--border);
            box-shadow: var(--shadow);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: .88rem;
        }
        thead th {
            background: var(--navy);
            color: #fff;
            padding: 11px 16px;
            text-align: left;
            font-weight: 600;
            font-size: .8rem;
            letter-spacing: .3px;
        }
        tbody td {
            padding: 12px 16px;
            border-bottom: 1px solid var(--border);
            color: var(--slate);
            vertical-align: middle;
        }
        tbody tr:last-child td { border-bottom: none; }
        tbody tr:hover td { background: #f8f9fc; }
        .empty-row td {
            text-align: center;
            color: #a4b0be;
            font-style: italic;
            padding: 22px;
        }

        /* ── Pill badges ── */
        .pill {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: .76rem;
            font-weight: 700;
        }
        .pill-blue   { background: #e8f5ff; color: #0070b8; border: 1px solid #b8dff7; }
        .pill-green  { background: #ecfdf5; color: #166534; border: 1px solid #bbf7d0; }
        .pill-amber  { background: #fffbeb; color: #92400e; border: 1px solid #fde68a; }
        .pill-red    { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
        .pill-slate  { background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; }

        /* ── Divider between sections ── */
        .section-divider {
            border: none;
            border-top: 1px solid var(--border);
            margin: 32px 0;
        }
    </style>
</head>
<body>
<div id="wrapper">

    <header>
        <h1><a href="dashboard.php">CNU Portal</a></h1>
    </header>

    <div class="site-logo">
        <img src="ban.png" alt="CNU Banner">
    </div>

    <nav>
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <main>

        <!-- ── Profile Header ── -->
        <div class="profile-header">
            <div class="avatar">
                <?php echo strtoupper(substr($_SESSION['first_name'], 0, 1) . substr($_SESSION['last_name'], 0, 1)); ?>
            </div>
            <div class="profile-info">
                <h2><?php echo htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']); ?></h2>
                <div class="email"><?php echo htmlspecialchars($_SESSION['email']); ?></div>
                <span class="role-badge"><?php echo htmlspecialchars(ucfirst($role)); ?></span>
            </div>
        </div>


        <?php if ($role === 'student'): ?>
        <!-- ══════════════════════════════════════════════════
             STUDENT VIEW
        ══════════════════════════════════════════════════ -->

        <!-- ── Alumni Events RSVPed ── -->
        <div class="section">
            <div class="section-heading">
                <span class="icon">📅</span> Events I'm Attending
                <span class="count-badge"><?php echo count($rsvped_events); ?></span>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Event</th>
                            <th>Date</th>
                            <th>Location</th>
                            <th>Posted By</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($rsvped_events)): ?>
                            <tr class="empty-row"><td colspan="4">You haven't RSVPed to any events yet.</td></tr>
                        <?php else: foreach ($rsvped_events as $e): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($e['title']); ?></strong></td>
                            <td><?php echo htmlspecialchars($e['event_date']); ?></td>
                            <td><?php echo htmlspecialchars($e['location']); ?></td>
                            <td><?php echo htmlspecialchars($e['posted_by']); ?></td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <hr class="section-divider">

        <!-- ── Alumni Jobs Applied ── -->
        <div class="section">
            <div class="section-heading">
                <span class="icon">💼</span> Jobs I've Applied To
                <span class="count-badge"><?php echo count($applied_jobs); ?></span>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Company</th>
                            <th>Location</th>
                            <th>Type</th>
                            <th>Posted By</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($applied_jobs)): ?>
                            <tr class="empty-row"><td colspan="5">You haven't expressed interest in any jobs yet.</td></tr>
                        <?php else: foreach ($applied_jobs as $j): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($j['title']); ?></strong></td>
                            <td><?php echo htmlspecialchars($j['company']); ?></td>
                            <td><?php echo htmlspecialchars($j['location']); ?></td>
                            <td><span class="pill pill-blue"><?php echo htmlspecialchars($j['job_type']); ?></span></td>
                            <td><?php echo htmlspecialchars($j['posted_by']); ?></td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <hr class="section-divider">

        <!-- ── Upcoming Trips ── -->
        <div class="section">
            <div class="section-heading">
                <span class="icon">✈️</span> My Upcoming Trips
                <span class="count-badge"><?php echo count($upcoming_trips); ?></span>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Trip ID</th>
                            <th>Destination</th>
                            <th>Arrival</th>
                            <th>Return</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($upcoming_trips)): ?>
                            <tr class="empty-row"><td colspan="5">No upcoming trips.</td></tr>
                        <?php else: foreach ($upcoming_trips as $t): ?>
                        <tr>
                            <td>#<?php echo htmlspecialchars($t['TripID']); ?></td>
                            <td><strong><?php echo htmlspecialchars($t['Destination']); ?></strong></td>
                            <td><?php echo htmlspecialchars($t['ArrivalDate']); ?></td>
                            <td><?php echo htmlspecialchars($t['ReturnDate']); ?></td>
                            <td>
                                <?php
                                $status = strtolower($t['Status']);
                                $pillClass = match($status) {
                                    'approved' => 'pill-green',
                                    'pending'  => 'pill-amber',
                                    'denied'   => 'pill-red',
                                    default    => 'pill-slate',
                                };
                                ?>
                                <span class="pill <?php echo $pillClass; ?>"><?php echo htmlspecialchars($t['Status']); ?></span>
                            </td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <hr class="section-divider">

        <!-- ── Advising Appointments ── -->
        <div class="section">
            <div class="section-heading">
                <span class="icon">🎓</span> My Advising Appointments
                <span class="count-badge"><?php echo count($advising_appointments); ?></span>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Professor</th>
                            <th>Location</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($advising_appointments)): ?>
                            <tr class="empty-row"><td colspan="5">No advising appointments booked.</td></tr>
                        <?php else: foreach ($advising_appointments as $a):
                            $isPast = strtotime($a['slot_date'] . ' ' . $a['slot_time']) < time();
                        ?>
                        <tr>
                            <td>
                                <?php echo htmlspecialchars(date('D, M j, Y', strtotime($a['slot_date']))); ?>
                                <?php if ($isPast): ?>
                                    <span class="pill pill-slate" style="margin-left:4px;">Past</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars(date('g:i A', strtotime($a['slot_time']))); ?></td>
                            <td><?php echo htmlspecialchars($a['faculty_name']); ?></td>
                            <td><?php echo htmlspecialchars($a['location']); ?></td>
                            <td><?php echo htmlspecialchars($a['notes'] ?: '—'); ?></td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>


        <?php elseif ($role === 'professor'): ?>
        <!-- ══════════════════════════════════════════════════
             PROFESSOR VIEW
        ══════════════════════════════════════════════════ -->

        <!-- ── Upcoming Trips ── -->
        <div class="section">
            <div class="section-heading">
                <span class="icon">✈️</span> My Upcoming Trips
                <span class="count-badge"><?php echo count($upcoming_trips); ?></span>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Trip ID</th>
                            <th>Destination</th>
                            <th>Arrival</th>
                            <th>Return</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($upcoming_trips)): ?>
                            <tr class="empty-row"><td colspan="5">No upcoming trips.</td></tr>
                        <?php else: foreach ($upcoming_trips as $t): ?>
                        <tr>
                            <td>#<?php echo htmlspecialchars($t['TripID']); ?></td>
                            <td><strong><?php echo htmlspecialchars($t['Destination']); ?></strong></td>
                            <td><?php echo htmlspecialchars($t['ArrivalDate']); ?></td>
                            <td><?php echo htmlspecialchars($t['ReturnDate']); ?></td>
                            <td>
                                <?php
                                $status = strtolower($t['Status']);
                                $pillClass = match($status) {
                                    'approved' => 'pill-green',
                                    'pending'  => 'pill-amber',
                                    'denied'   => 'pill-red',
                                    default    => 'pill-slate',
                                };
                                ?>
                                <span class="pill <?php echo $pillClass; ?>"><?php echo htmlspecialchars($t['Status']); ?></span>
                            </td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <hr class="section-divider">

        <!-- ── Advising Slots Created ── -->
        <div class="section">
            <div class="section-heading">
                <span class="icon">🎓</span> My Advising Slots
                <span class="count-badge"><?php echo count($advising_slots); ?></span>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Location</th>
                            <th>Notes</th>
                            <th>Status</th>
                            <th>Booked By</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($advising_slots)): ?>
                            <tr class="empty-row"><td colspan="6">No advising slots created yet.</td></tr>
                        <?php else: foreach ($advising_slots as $s): ?>
                        <tr>
                            <td><?php echo htmlspecialchars(date('D, M j, Y', strtotime($s['slot_date']))); ?></td>
                            <td><?php echo htmlspecialchars(date('g:i A', strtotime($s['slot_time']))); ?></td>
                            <td><?php echo htmlspecialchars($s['location']); ?></td>
                            <td><?php echo htmlspecialchars($s['notes'] ?: '—'); ?></td>
                            <td>
                                <?php if ($s['is_booked']): ?>
                                    <span class="pill pill-amber">Booked</span>
                                <?php else: ?>
                                    <span class="pill pill-green">Open</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($s['student_name'] ?: '—'); ?></td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>


        <?php else: ?>
        <!-- ══════════════════════════════════════════════════
             ALUMNI / OTHER ROLES — trips only
        ══════════════════════════════════════════════════ -->

        <div class="section">
            <div class="section-heading">
                <span class="icon">✈️</span> My Upcoming Trips
                <span class="count-badge"><?php echo count($upcoming_trips); ?></span>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Trip ID</th>
                            <th>Destination</th>
                            <th>Arrival</th>
                            <th>Return</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($upcoming_trips)): ?>
                            <tr class="empty-row"><td colspan="5">No upcoming trips.</td></tr>
                        <?php else: foreach ($upcoming_trips as $t): ?>
                        <tr>
                            <td>#<?php echo htmlspecialchars($t['TripID']); ?></td>
                            <td><strong><?php echo htmlspecialchars($t['Destination']); ?></strong></td>
                            <td><?php echo htmlspecialchars($t['ArrivalDate']); ?></td>
                            <td><?php echo htmlspecialchars($t['ReturnDate']); ?></td>
                            <td>
                                <?php
                                $status = strtolower($t['Status']);
                                $pillClass = match($status) {
                                    'approved' => 'pill-green',
                                    'pending'  => 'pill-amber',
                                    'denied'   => 'pill-red',
                                    default    => 'pill-slate',
                                };
                                ?>
                                <span class="pill <?php echo $pillClass; ?>"><?php echo htmlspecialchars($t['Status']); ?></span>
                            </td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php endif; ?>

        <br>
        <h3><a href="dashboard.php">← Back to Dashboard</a></h3>

    </main>

    <footer>
        <br>sam.kirkhorn.23@cnu.edu<br>grant.kochan.23@cnu.edu
        <br>alijah.levenberry.23@cnu.edu<br>colin.anderson.22@cnu.edu
    </footer>

</div>
</body>
</html>

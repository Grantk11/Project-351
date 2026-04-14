<?php
session_start();
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../includes/dbconnect.php';
require_role(['professor']);

$professor_id = (int)$_SESSION['user_id'];
$success = '';

// Save advisee selections
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selected = isset($_POST['advisees']) ? array_map('intval', $_POST['advisees']) : [];

    // Remove all current advisees for this professor then re-insert selections
    $pdo->prepare("DELETE FROM advisee_list WHERE professor_id = ?")->execute([$professor_id]);

    if (!empty($selected)) {
        $insert = $pdo->prepare("INSERT INTO advisee_list (professor_id, student_id) VALUES (?, ?)");
        foreach ($selected as $sid) {
            $insert->execute([$professor_id, $sid]);
        }
    }
    $success = "Advisee list saved.";
}

// Fetch all students
$allStudents = $pdo->query(
    "SELECT id, first_name, last_name, email FROM users WHERE Position = 'student' ORDER BY last_name, first_name"
)->fetchAll(PDO::FETCH_ASSOC);

// Fetch current advisee IDs for this professor
$advStmt = $pdo->prepare("SELECT student_id FROM advisee_list WHERE professor_id = ?");
$advStmt->execute([$professor_id]);
$currentAdvisees = array_column($advStmt->fetchAll(PDO::FETCH_ASSOC), 'student_id');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Advisees</title>
    <link rel="stylesheet" href="../../351.css">
    <style>
        .module-header { margin-bottom:1.5rem; }
        .module-header h2 { margin-bottom:.25rem; }
        .module-header p  { color:#555; margin:0; }

        .alert { padding:.75rem 1rem; border-radius:4px; margin-bottom:1rem; }
        .alert-success { background:#d4edda; color:#155724; border:1px solid #c3e6cb; }

        .card {
            background:#fff;
            border:1px solid #ddd;
            border-radius:6px;
            padding:1.25rem 1.5rem;
            margin-bottom:2rem;
        }
        .card-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            gap: 1rem;
            flex-wrap: wrap;
        }
        .card-toolbar h3 { margin:0; }

        /* Search box */
        .search-box {
            padding: .4rem .7rem;
            border: 1px solid #bbb;
            border-radius: 4px;
            font-size: .9rem;
            width: 220px;
        }

        table { width:100%; border-collapse:collapse; font-size:.92rem; }
        th, td { padding:.55rem .75rem; text-align:left; border-bottom:1px solid #ddd; }
        th { background:#004a8f; color:#fff; }
        tr:hover td { background:#f0f4ff; }

        /* Checkbox styling */
        input[type="checkbox"] { width:17px; height:17px; cursor:pointer; accent-color:#004a8f; }

        .btn {
            display:inline-block;
            padding:.5rem 1.2rem;
            border:none;
            border-radius:4px;
            cursor:pointer;
            font-size:.95rem;
            font-weight:600;
            text-decoration:none;
        }
        .btn-primary   { background:#004a8f; color:#fff; }
        .btn-primary:hover   { background:#003366; }
        .btn-secondary { background:#e2e8f0; color:#333; }
        .btn-secondary:hover { background:#cbd5e0; }

        .form-actions { display:flex; gap:.75rem; margin-top:1.25rem; }

        .select-links { font-size:.85rem; }
        .select-links a { color:#004a8f; cursor:pointer; text-decoration:underline; margin-right:.75rem; }

        .count-label { font-size:.85rem; color:#555; }

        tr.hidden-row { display:none; }
    </style>
</head>
<body>
<div id="wrapper">

    <header>
        <h1><a href="../../dashboard.php">CNU Portal</a></h1>
    </header>

    <div class="site-logo">
        <img src="../../ban.png" alt="CNU Banner">
    </div>

    <nav>
        <ul>
            <li><a href="messaging_index.php">Messaging</a></li>
            <li><a href="advising_index.php">Advising Home</a></li>
            <li><a href="../../logout.php">Logout</a></li>
        </ul>
    </nav>

    <main style="max-width:860px;margin:2rem auto;padding:0 1rem;">

        <div class="module-header">
            <h2>Manage Advisee List</h2>
            <p>Check the students you advise. Save when done — this list is used when sending group messages.</p>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="POST" action="manage_advisees.php">
            <div class="card">
                <div class="card-toolbar">
                    <h3>
                        Registered Students
                        <span class="count-label" id="advisee-count">
                            (<?= count($currentAdvisees) ?> selected)
                        </span>
                    </h3>
                    <input type="text" class="search-box" id="student-search"
                           placeholder="Search by name or email..." oninput="filterStudents()">
                </div>

                <div class="select-links">
                    <a onclick="toggleAll(true)">Select All</a>
                    <a onclick="toggleAll(false)">Deselect All</a>
                </div>
                <br>

                <?php if (empty($allStudents)): ?>
                    <p style="color:#777;font-style:italic;">No registered students found.</p>
                <?php else: ?>
                    <table id="student-table">
                        <thead>
                            <tr>
                                <th style="width:40px;">Advisee</th>
                                <th>Name</th>
                                <th>Email</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($allStudents as $s): ?>
                            <tr data-name="<?= htmlspecialchars(strtolower($s['first_name'] . ' ' . $s['last_name'])) ?>"
                                data-email="<?= htmlspecialchars(strtolower($s['email'])) ?>">
                                <td>
                                    <input type="checkbox" name="advisees[]"
                                           value="<?= (int)$s['id'] ?>"
                                           class="advisee-check"
                                           onchange="updateCount()"
                                           <?= in_array((int)$s['id'], $currentAdvisees) ? 'checked' : '' ?>>
                                </td>
                                <td><?= htmlspecialchars($s['last_name'] . ', ' . $s['first_name']) ?></td>
                                <td><?= htmlspecialchars($s['email']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Save Advisee List</button>
                <a href="messaging_index.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>

    </main>

    <footer>
        <br>sam.kirkhorn.23@cnu.edu
        <br>grant.kochan.23@cnu.edu
        <br>alijah.levenberry.23@cnu.edu
        <br>colin.anderson.22@cnu.edu
    </footer>

</div>

<script>
function updateCount() {
    const checked = document.querySelectorAll('.advisee-check:checked').length;
    document.getElementById('advisee-count').textContent = '(' + checked + ' selected)';
}

function toggleAll(state) {
    document.querySelectorAll('.advisee-check').forEach(cb => {
        const row = cb.closest('tr');
        if (!row.classList.contains('hidden-row')) cb.checked = state;
    });
    updateCount();
}

function filterStudents() {
    const query = document.getElementById('student-search').value.toLowerCase();
    document.querySelectorAll('#student-table tbody tr').forEach(row => {
        const name  = row.dataset.name  || '';
        const email = row.dataset.email || '';
        row.classList.toggle('hidden-row', !name.includes(query) && !email.includes(query));
    });
}
</script>

</body>
</html>

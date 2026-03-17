<?php
session_start();
require "../../DB_Connect.php";

$student_id = isset($_GET['student_id']) ? intval($_GET['student_id']) : 0;

$stmt = mysqli_prepare($conn, "
    SELECT MeetingID, MeetingName, MeetingLocation, MeetingDate
    FROM meeting
    WHERE StudentID = ?
    ORDER BY MeetingDate ASC
");
mysqli_stmt_bind_param($stmt, "i", $student_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Meetings</title>
    <link rel="stylesheet" href="../../351.css">
</head>
<body>

<div style="display: block; margin-bottom: 10px;">
    <a href="../../dashboard.php">Back to Dashboard</a>
</div>

<div id="wrapper">

    <header>
        <h1>Advising Meetings</h1>
    </header>

    <main>

        <a href="add_meeting.php?student_id=<?= htmlspecialchars($student_id) ?>">+ Schedule New Meeting</a>

        <br><br>

        <?php if (mysqli_num_rows($result) === 0): ?>
            <p>No meetings scheduled for this student.</p>
        <?php else: ?>
            <table border="1" cellpadding="8" cellspacing="0">
                <thead>
                    <tr>
                        <th>Meeting Name</th>
                        <th>Location</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['MeetingName']) ?></td>
                        <td><?= htmlspecialchars($row['MeetingLocation']) ?></td>
                        <td><?= htmlspecialchars($row['MeetingDate']) ?></td>
                        <td>
                            <a href="delete_meeting.php?meeting_id=<?= $row['MeetingID'] ?>&student_id=<?= $student_id ?>"
                               onclick="return confirm('Delete this meeting?')">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        <?php endif; ?>

    </main>

</div>

</body>
</html>

<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Fetch all teachers
$sql = "SELECT * FROM teachers ORDER BY id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Teachers</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <style>
        .action-btn {
            margin-right: 5px;
        }
    </style>
</head>
<body class="w3-light-grey">

<div class="w3-container w3-card-4 w3-white w3-margin w3-padding" style="max-width:900px; margin:auto; margin-top:40px;">
    <h2>Manage Teachers</h2>

    <a href="add_teacher.php" class="w3-button w3-green w3-margin-bottom">➕ Add New Teacher</a>
    <a href="admin_dashboard.php" class="w3-button w3-light-grey w3-margin-bottom" style="float:right;">Back to Dashboard</a>

    <?php if ($result->num_rows > 0): ?>
    <table class="w3-table-all w3-hoverable">
        <thead>
            <tr class="w3-blue">
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($teacher = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($teacher['id']) ?></td>
                <td><?= htmlspecialchars($teacher['name']) ?></td>
                <td><?= htmlspecialchars($teacher['email']) ?></td>
                <td>
                    <a href="edit_teacher.php?id=<?= $teacher['id'] ?>" class="w3-button w3-blue w3-small action-btn">Edit</a>
                    <a href="delete_teacher.php?id=<?= $teacher['id'] ?>" class="w3-button w3-red w3-small" onclick="return confirm('Are you sure to delete this teacher?');">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
    <?php else: ?>
        <p class="w3-text-red">No teachers found.</p>
    <?php endif; ?>
</div>

</body>
</html>

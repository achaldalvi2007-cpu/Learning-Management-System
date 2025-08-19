<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Get success or error messages from URL params
$message = isset($_GET['message']) ? $_GET['message'] : '';
$error = isset($_GET['error']) ? $_GET['error'] : '';

// Prepare UNION ALL query to get all users
$sql = "
    SELECT id, name, email, role FROM users WHERE role='admin'
    UNION ALL
    SELECT id, name, email, 'teacher' AS role FROM teachers
    UNION ALL
    SELECT id, name, email, 'student' AS role FROM students
    ORDER BY role, name
";

$result = $conn->query($sql);

if (!$result) {
    die("Query failed: " . $conn->error);
}

$all_users = [];
while ($row = $result->fetch_assoc()) {
    $all_users[] = $row;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>All Users - Reset Password</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <style>
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
        form { margin: 0; }
        .message { margin: 15px 0; padding: 12px; border-radius: 5px; }
        .success { background-color: #d4edda; color: #155724; }
        .error { background-color: #f8d7da; color: #721c24; }
    </style>
</head>
<body class="w3-container" style="max-width:900px; margin:auto; margin-top:50px;">

    <h2>All Users List - Reset Password</h2>

    <?php if ($message): ?>
        <div class="message success"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="message error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <table class="w3-table w3-striped w3-bordered">
        <thead>
            <tr>
                <th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Reset Password</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($all_users as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user['id']) ?></td>
                    <td><?= htmlspecialchars($user['name']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td><?= htmlspecialchars($user['role']) ?></td>
                    <td>
                        <form method="POST" action="reset_password_action.php" style="display:flex; gap: 5px; flex-wrap: nowrap;">
                            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                            <input type="hidden" name="role" value="<?= $user['role'] ?>">
                            <input type="password" name="new_password" placeholder="New password" required style="flex:1;">
                            <button class="w3-button w3-green" type="submit">Reset</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <a href="admin_dashboard.php" class="w3-button w3-light-grey w3-margin-top">Back to Dashboard</a>
</body>
</html>

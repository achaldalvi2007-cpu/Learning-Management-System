<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $message = "Passwords do not match!";
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM teachers WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $message = "Email already exists!";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO teachers (name, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $name, $email, $hashed_password);

            if ($stmt->execute()) {
                $message = "Teacher added successfully!";
            } else {
                $message = "Error adding teacher!";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Teacher</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
</head>
<body class="w3-light-grey">

<div class="w3-container w3-card-4 w3-white w3-margin w3-padding" style="max-width:600px; margin:auto; margin-top:40px;">
    <h2>Add New Teacher</h2>

    <?php if ($message): ?>
        <p class="w3-text-green"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form method="POST" novalidate>
        <label>Name:</label>
        <input class="w3-input w3-border" type="text" name="name" required>

        <label>Email:</label>
        <input class="w3-input w3-border" type="email" name="email" required>

        <label>Password:</label>
        <input class="w3-input w3-border" type="password" name="password" required minlength="6">

        <label>Confirm Password:</label>
        <input class="w3-input w3-border" type="password" name="confirm_password" required minlength="6">

        <br>
        <button class="w3-button w3-green" type="submit">Add Teacher</button>
        <a href="manage_teachers.php" class="w3-button w3-gray">Back</a>
    </form>
</div>

</body>
</html>

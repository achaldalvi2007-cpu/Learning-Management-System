<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$id = intval($_GET['id']);
$message = '';

// Get current teacher info
$stmt = $conn->prepare("SELECT id, name, email FROM teachers WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$teacher = $result->fetch_assoc();

if (!$teacher) {
    die("Teacher not found.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);

    // Check if email already exists for other teachers
    $stmt = $conn->prepare("SELECT id FROM teachers WHERE email = ? AND id != ?");
    $stmt->bind_param("si", $email, $id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $message = "Email already used by another teacher!";
    } else {
        $stmt = $conn->prepare("UPDATE teachers SET name = ?, email = ? WHERE id = ?");
        $stmt->bind_param("ssi", $name, $email, $id);

        if ($stmt->execute()) {
            $message = "Teacher updated successfully!";
            // Refresh data
            $teacher['name'] = $name;
            $teacher['email'] = $email;
        } else {
            $message = "Error updating teacher!";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Teacher</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
</head>
<body class="w3-light-grey">

<div class="w3-container w3-card-4 w3-white w3-margin w3-padding" style="max-width:600px; margin:auto; margin-top:40px;">
    <h2>Edit Teacher</h2>

    <?php if ($message): ?>
        <p class="w3-text-green"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Name:</label>
        <input class="w3-input w3-border" type="text" name="name" value="<?= htmlspecialchars($teacher['name']) ?>" required>

        <label>Email:</label>
        <input class="w3-input w3-border" type="email" name="email" value="<?= htmlspecialchars($teacher['email']) ?>" required>

        <br>
        <button class="w3-button w3-blue" type="submit">Update Teacher</button>
        <a href="manage_teachers.php" class="w3-button w3-gray">Back</a>
    </form>
</div>

</body>
</html>

<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$id = intval($_GET['id']);
$message = '';

// Fetch current student info safely with prepared statement
$stmt = $conn->prepare("SELECT id, name, email, fees_paid, total_fees FROM students WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

if (!$student) {
    die("Student not found.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);

    $update_stmt = $conn->prepare("UPDATE students SET name=?, email=? WHERE id=?");
    $update_stmt->bind_param("ssddi", $name, $email, $id);

    if ($update_stmt->execute()) {
        $message = "Student updated successfully!";
        // Refresh student data
        $stmt->execute();
        $result = $stmt->get_result();
        $student = $result->fetch_assoc();
    } else {
        $message = "Error updating student.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Student</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
</head>
<body class="w3-light-grey">

<div class="w3-container w3-card-4 w3-white w3-margin w3-padding" style="max-width:600px; margin:auto;">
    <h2>Edit Student</h2>

    <?php if (!empty($message)): ?>
        <p class="w3-text-green"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <!-- Debugging: print current student data -->
    <!-- <pre><?php // print_r($student); ?></pre> -->

    <form method="POST">
        <label>Name:</label>
        <input
          class="w3-input w3-border"
          type="text"
          name="name"
          value="<?= htmlspecialchars($student['name'] ?? '') ?>"
          required
        >

        <label>Email:</label>
        <input
          class="w3-input w3-border"
          type="email"
          name="email"
          value="<?= htmlspecialchars($student['email'] ?? '') ?>"
          required
        >

        <br>
        <button class="w3-button w3-blue" type="submit">Update</button>
        <a href="manage_students.php" class="w3-button w3-gray">Back</a>
    </form>
</div>

</body>
</html>

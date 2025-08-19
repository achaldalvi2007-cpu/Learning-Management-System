<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$message = '';

$streams = ['PCM', 'PCB', 'PCMB'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $fees_paid = floatval($_POST['fees_paid']);
    $total_fees = floatval($_POST['total_fees']);
    $stream = $_POST['stream'] ?? '';

    if (!in_array($stream, $streams)) {
        $message = "Please select a valid stream.";
    } else {
        $stmt = $conn->prepare("INSERT INTO students (name, email, password, fees_paid, total_fees, stream) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssdds", $name, $email, $password, $fees_paid, $total_fees, $stream);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Student added successfully!";
            header("Location: manage_students.php");
            exit();
        } else {
            $message = "Error: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Student</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
</head>
<body class="w3-light-grey">

<div class="w3-container w3-card-4 w3-white w3-margin w3-padding" style="max-width:600px; margin:auto;">
    <h2>Add New Student</h2>

    <?php if (!empty($message)): ?>
        <p class="w3-text-red"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Name:</label>
        <input class="w3-input w3-border" type="text" name="name" required>

        <label>Email:</label>
        <input class="w3-input w3-border" type="email" name="email" required>

        <label>Password:</label>
        <input class="w3-input w3-border" type="password" name="password" required>

        <label>Stream:</label>
        <select class="w3-select w3-border" name="stream" required>
            <option value="" disabled selected>Select Stream</option>
            <?php foreach ($streams as $s): ?>
                <option value="<?= htmlspecialchars($s) ?>"><?= htmlspecialchars($s) ?></option>
            <?php endforeach; ?>
        </select>

        <label>Fees Paid (₹):</label>
        <input class="w3-input w3-border" type="number" name="fees_paid" min="0" step="0.01" required>

        <label>Total Fees (₹):</label>
        <input class="w3-input w3-border" type="number" name="total_fees" min="0" step="0.01" required>

        <br>
        <button class="w3-button w3-blue" type="submit">Add Student</button>
        <a href="manage_students.php" class="w3-button w3-gray">Cancel</a>
    </form>
</div>

</body>
</html>

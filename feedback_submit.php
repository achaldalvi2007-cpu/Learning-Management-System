<?php
session_start();
include 'connection.php';

$success = '';
$error = '';

// Detect user type and load info
if (isset($_SESSION['student_id'])) {
    $name = $_SESSION['student_name'];
    $email = $_SESSION['student_email'];
    $role = 'student';
    $back_url = 'student_dashboard.php';
} elseif (isset($_SESSION['teacher_id'])) {
    $name = $_SESSION['teacher_name'];
    $email = $_SESSION['teacher_email'];
    $role = 'teacher';
    $back_url = 'teacher_dashboard.php';
} else {
    header("Location: index.php");
    exit();
}

// Handle feedback form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = trim($_POST['message'] ?? '');

    if (empty($message)) {
        $error = "Please enter your feedback message.";
    } else {
        $stmt = $conn->prepare("INSERT INTO feedback (name, email, role, message) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $role, $message);

        if ($stmt->execute()) {
            $success = "✅ Thank you! Your feedback has been submitted.";
        } else {
            $error = "❌ Error submitting feedback. Please try again.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Submit Feedback</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
</head>
<body class="w3-light-grey">

<div class="w3-container w3-card-4 w3-white w3-margin" style="max-width:600px; margin:40px auto;">

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
        <h2 style="margin: 0;">Submit Feedback</h2>
        <a href="<?= $back_url ?>" class="w3-button w3-light-grey">⬅ Back to Dashboard</a>
    </div>

    <?php if ($success): ?>
        <div class="w3-panel w3-green w3-padding"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="w3-panel w3-red w3-padding"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" class="w3-container" novalidate>
        <label>Name</label>
        <input class="w3-input w3-border" type="text" value="<?= htmlspecialchars($name) ?>" disabled>

        <label>Email</label>
        <input class="w3-input w3-border" type="email" value="<?= htmlspecialchars($email) ?>" disabled>

        <label>Role</label>
        <input class="w3-input w3-border" type="text" value="<?= htmlspecialchars(ucfirst($role)) ?>" disabled>

        <label>Message</label>
        <textarea class="w3-input w3-border" name="message" rows="5" required></textarea>

        <button type="submit" class="w3-button w3-green w3-margin-top">Submit Feedback</button>
    </form>

</div>

</body>
</html>

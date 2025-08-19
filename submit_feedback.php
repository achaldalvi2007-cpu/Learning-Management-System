<?php
include 'connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $message = $_POST['message'];

    $stmt = $conn->prepare("INSERT INTO feedback (name, email, role, message) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $role, $message);

    if ($stmt->execute()) {
        echo "Feedback submitted successfully!";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!-- Feedback Form -->
<form method="POST" class="w3-container w3-card w3-white w3-padding w3-margin" style="max-width:600px; margin:auto;">
    <h2>Submit Feedback</h2>
    <label>Name</label>
    <input class="w3-input" name="name" required>
    <label>Email</label>
    <input class="w3-input" name="email" required type="email">
    <label>Role</label>
    <select class="w3-select" name="role" required>
        <option value="" disabled selected>Choose role</option>
        <option value="student">Student</option>
        <option value="teacher">Teacher</option>
    </select>
    <label>Message</label>
    <textarea class="w3-input" name="message" required></textarea><br>
    <button class="w3-button w3-green" type="submit">Submit</button>
</form>

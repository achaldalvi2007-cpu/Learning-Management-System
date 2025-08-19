<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Fetch all students
$sql = "SELECT * FROM students ORDER BY id DESC";
$result = $conn->query($sql);

// Handle flash message
$message = '';
if (isset($_SESSION['success_message'])) {
    $message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Students</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <style>
        .success-box {
            background-color: #d4edda;
            color: #155724;
            padding: 10px 20px;
            margin-bottom: 15px;
            border-left: 6px solid #28a745;
            font-weight: bold;
        }
    </style>
</head>
<body class="w3-container w3-light-grey">

<div class="w3-card-4 w3-white w3-margin-top w3-padding" style="max-width: 1100px; margin: 40px auto;">
    <h2>👨‍🎓 Manage Students</h2>

<?php if (!empty($message)) : ?>
    <div class="success-box" id="flash-message"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<script>
  // Auto-hide after 3 seconds
  setTimeout(() => {
    const msg = document.getElementById("flash-message");
    if (msg) msg.style.display = "none";
  }, 3000); // 3000ms = 3 seconds
</script>


    <a href="add_student.php" class="w3-button w3-green w3-margin-bottom">➕ Add New Student</a>
    <a href="admin_dashboard.php" class="w3-button w3-light-grey w3-margin-bottom w3-right">🏠 Back to Dashboard</a>

    <?php if ($result->num_rows > 0) { ?>
        <table class="w3-table-all w3-hoverable w3-small w3-centered">
            <thead>
                <tr class="w3-blue">
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Fees Paid (₹)</th>
                    <th>Total Fees (₹)</th>
                    <th>Balance (₹)</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($student = $result->fetch_assoc()) { 
                    $fees_paid = $student['Fees_paid'];
                    $total_fees = $student['Total_fees'];
                    $balance = $total_fees - $fees_paid;
                ?>
                    <tr>
                        <td><?= $student['id'] ?></td>
                        <td><?= htmlspecialchars($student['name']) ?></td>
                        <td><?= htmlspecialchars($student['email']) ?></td>
                        <td>₹<?= number_format($fees_paid, 2) ?></td>
                        <td>₹<?= number_format($total_fees, 2) ?></td>
                        <td style="color: <?= $balance > 0 ? 'red' : 'green' ?>;">
                            ₹<?= number_format($balance, 2) ?>
                        </td>
                        <td>
                            <a href="edit_student.php?id=<?= $student['id'] ?>" class="w3-button w3-blue w3-small">✏️ Edit</a>
                            <a href="delete_student.php?id=<?= $student['id'] ?>" class="w3-button w3-red w3-small"
                               onclick="return confirm('Are you sure you want to delete this student?');">🗑️ Delete</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } else { ?>
        <p class="w3-text-red">No students found.</p>
    <?php } ?>
</div>

</body>
</html>

<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Fetch all students with fee info
$sql = "SELECT id, name, email, fees_paid, total_fees FROM students ORDER BY name ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Manage Fees</title>
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
  <style>
    .header-section {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
    }
  </style>
</head>
<body class="w3-container w3-light-grey" style="max-width:1000px; margin:auto; margin-top:40px;">

<div class="w3-card-4 w3-white w3-padding">
  <div class="header-section">
    <h2>Manage Fees</h2>
    <div>
      <a href="admin_dashboard.php" class="w3-button w3-light-grey w3-margin-right">Back to Dashboard</a>
      <!-- You can add other buttons here if needed, like Add Fee -->
    </div>
  </div>

  <?php if ($result->num_rows > 0) { ?>
    <table class="w3-table-all w3-hoverable w3-striped">
      <thead>
        <tr class="w3-blue">
          <th>Name</th>
          <th>Email</th>
          <th>Fees Paid (₹)</th>
          <th>Total Fees (₹)</th>
          <th>Balance (₹)</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($student = $result->fetch_assoc()) {
          $balance = $student['total_fees'] - $student['fees_paid'];
        ?>
        <tr>
          <td><?= htmlspecialchars($student['name']) ?></td>
          <td><?= htmlspecialchars($student['email']) ?></td>
          <td>₹<?= number_format($student['fees_paid'], 2) ?></td>
          <td>₹<?= number_format($student['total_fees'], 2) ?></td>
          <td style="color: <?= $balance > 0 ? 'red' : 'green' ?>;">
            ₹<?= number_format($balance, 2) ?>
            <?= $balance > 0 ? '<span class="w3-text-red">(Due)</span>' : '<span class="w3-text-green">(Paid)</span>' ?>
          </td>
          <td>
            <a href="edit_fees.php?id=<?= $student['id'] ?>" class="w3-button w3-indigo w3-small">Edit</a>
          </td>
        </tr>
        <?php } ?>
      </tbody>
    </table>
  <?php } else { ?>
    <p>No students found.</p>
  <?php } ?>
</div>

</body>
</html>

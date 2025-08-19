<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
include 'connection.php';

// For testing only: Bypass login (remove in production)
if (!isset($_SESSION['admin_id'])) {
    $_SESSION['admin_id'] = 1;
}

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    die("Invalid student ID");
}

$message = '';

// Fetch student data
$stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

if (!$student) {
    die("Student not found");
}

// Handle nulls and set default values
$total_fees = isset($student['Total_fees']) ? $student['Total_fees'] : 0;
$fees_paid = isset($student['Fees_paid']) ? $student['Fees_paid'] : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $total_fees_input = floatval($_POST['total_fees']);
    $additional_payment = floatval($_POST['additional_payment']);

    if ($additional_payment < 0) {
        $message = "Additional payment cannot be negative.";
    } elseif (($fees_paid + $additional_payment) > $total_fees_input) {
        $message = "Total paid fees cannot exceed total fees.";
    } else {
        $new_fees_paid = $fees_paid + $additional_payment;

        $update_stmt = $conn->prepare("UPDATE students SET total_fees = ?, fees_paid = ? WHERE id = ?");
        $update_stmt->bind_param("dii", $total_fees_input, $new_fees_paid, $id);

        if ($update_stmt->execute()) {
            $message = "Fees updated successfully!";

            // Update displayed values after update
            $total_fees = $total_fees_input;
            $fees_paid = $new_fees_paid;
        } else {
            $message = "Error updating fees: " . $update_stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Fees - <?php echo htmlspecialchars($student['name']); ?></title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <style>
        .readonly-input { background-color: #f1f1f1; }
        .message { margin-bottom: 15px; font-weight: bold; }
    </style>
</head>
<body class="w3-light-grey">

<div class="w3-container w3-card-4 w3-white w3-margin w3-padding" style="max-width:600px; margin:auto;">
    <h2 class="w3-text-blue">Edit Fees for <strong><?php echo htmlspecialchars($student['name']); ?></strong></h2>

    <?php if ($message): ?>
        <div class="w3-panel <?php echo (strpos($message, 'successfully') !== false) ? 'w3-green' : 'w3-red'; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <label>Total Fees (₹):</label>
        <input type="number" step="0.01" name="total_fees" required value="<?php echo htmlspecialchars($total_fees); ?>" class="w3-input w3-border"/>

        <label>Fees Already Paid (₹):</label>
        <input type="number" readonly value="<?php echo htmlspecialchars($fees_paid); ?>" class="w3-input w3-border readonly-input"/>

        <label>Additional Payment (₹):</label>
        <input type="number" step="0.01" min="0" name="additional_payment" required value="0" class="w3-input w3-border"/>

        <br><br>
        <button type="submit" class="w3-button w3-blue">Update Fees</button>
        <a href="manage_fees.php" class="w3-button w3-gray">Back</a>
    </form>
</div>

</body>
</html>

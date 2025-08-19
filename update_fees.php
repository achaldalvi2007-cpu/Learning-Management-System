<?php
include 'connection.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Get current fees
    $sql = "SELECT name, fees_paid, total_fees FROM students WHERE id = $id";
    $result = $conn->query($sql);
    $student = $result->fetch_assoc();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fees_paid = $_POST['fees_paid'];
    $total_fees = $_POST['total_fees'];

    $update_sql = "UPDATE students SET fees_paid = '$fees_paid', total_fees = '$total_fees' WHERE id = $id";
    if ($conn->query($update_sql) === TRUE) {
        header("Location: teacher_dashboard.php");
        exit();
    } else {
        echo "Error updating record.";
    }
}
?>

<!DOCTYPE html>
<html>
<head><title>Update Fees</title></head>
<body>
<h2>Update Fees for <?php echo $student['name']; ?></h2>
<form method="POST">
    Fees Paid: <input type="number" name="fees_paid" value="<?php echo $student['fees_paid']; ?>" required><br><br>
    Total Fees: <input type="number" name="total_fees" value="<?php echo $student['total_fees']; ?>" required><br><br>
    <button type="submit">Update</button>
</form>
<a href="teacher_dashboard.php">Cancel</a>
</body>
</html>

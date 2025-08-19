<?php
session_start();
include 'connection.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare statement to select admin from users table
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND role = 'admin'");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        // Verify hashed password
        if (password_verify($password, $row['password'])) {
            $_SESSION['admin_id'] = $row['id'];
            $_SESSION['admin_name'] = $row['name'];
            $_SESSION['role'] = 'admin';
            header("Location: admin_dashboard.php");
            exit();
        } else {
            $error = "Wrong password!";
        }
    } else {
        $error = "Email not found or not an admin!";
    }
}
?>

<!DOCTYPE html>
<html>
<style>
  .forgot-password {
      display: block;
      margin-top: 10px;
      text-align: center;
      font-size: 0.95rem;
      font-weight: 600;
      color: #007BFF;
      text-decoration: none;
      cursor: pointer;
      transition: color 0.3s ease;
    }

    .forgot-password:hover {
      color: #0056b3;
      text-decoration: underline;
    }

</style>
<head>
    <title>Admin Login</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
</head>
<body class="w3-container w3-light-grey" style="max-width:400px; margin:auto; margin-top:50px;">

    <div class="w3-card-4 w3-white w3-padding">
        <h2 class="w3-center">Admin Login</h2>

        <?php if ($error) echo "<p class='w3-text-red'>$error</p>"; ?>

        <form method="POST" action="">
            <label>Email:</label>
            <input class="w3-input w3-border w3-margin-bottom" type="email" name="email" required>

            <label>Password:</label>
            <input class="w3-input w3-border w3-margin-bottom" type="password" name="password" required>

            <button class="w3-button w3-blue w3-block" type="submit">Login</button>
        </form>
    <a href="admin_forgot_password.php?role=admin" class="forgot-password">Forgot Password?</a>

        <a href="index.php" class="w3-button w3-light-grey w3-block w3-margin-top">Back to Home</a>
    </div>

</body>
</html>

<?php
session_start();
include 'connection.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
require 'PHPMailer/Exception.php';


$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    // Check if admin exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND role = 'admin'");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        $token = bin2hex(random_bytes(50));
        $update = $conn->prepare("UPDATE users SET reset_token = ? WHERE email = ? AND role = 'admin'");
        $update->bind_param("ss", $token, $email);
        $update->execute();

        // Send email
        $reset_link = "http://localhost/coaching_project/admin_reset_password.php?token=$token";

        $mail = new PHPMailer;
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'achaldalvi2007@gmail.com';     
        $mail->Password = 'edxdwprbbetcyefc';       
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('achaldalvi2007@gmail.com', 'Coaching Admin');
        $mail->addAddress($email);
        $mail->Subject = 'Reset Your Admin Password';
        $mail->Body = "Click this link to reset your password:\n\n$reset_link";

        if ($mail->send()) {
            $message = "✅ Reset link sent to your email.";
        } else {
            $message = "❌ Mail Error: " . $mail->ErrorInfo;
        }
    } else {
        $message = "❌ No admin found with this email.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Forgot Admin Password</title>
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
</head>
<body class="w3-container w3-light-grey">
  <div class="w3-card w3-white w3-padding w3-margin-top" style="max-width:500px; margin:auto;">
    <h2>🔑 Forgot Admin Password</h2>
    <?php if ($message): ?>
      <p class="w3-text-blue"><?php echo $message; ?></p>
    <?php endif; ?>

    <form method="post">
      <label>Enter Registered Email</label>
      <input class="w3-input w3-border w3-margin-top" type="email" name="email" required>
      <button class="w3-button w3-blue w3-margin-top">Send Reset Link</button>
    </form>
  </div>
</body>
</html>

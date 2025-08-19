<?php
session_start();
include 'connection.php';

$token = $_GET['token'] ?? '';
$message = '';

// If form submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_password = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    if ($new_password !== $confirm) {
        $message = "❌ Passwords do not match.";
    } elseif (strlen($new_password) < 6) {
        $message = "❌ Password must be at least 6 characters.";
    } else {
        $hashed = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET password=?, reset_token=NULL WHERE reset_token=? AND role='admin'");
        $stmt->bind_param("ss", $hashed, $token);
        if ($stmt->execute() && $stmt->affected_rows === 1) {
            $message = "✅ Password reset successful. <a href='admin_login.php'>Login now</a>";
        } else {
            $message = "❌ Invalid or expired token.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Reset Admin Password</title>
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
</head>
<body class="w3-container w3-light-grey">
  <div class="w3-card w3-white w3-padding w3-margin-top" style="max-width:500px; margin:auto;">
    <h2>🔐 Reset Your Password</h2>
    <?php if ($message): ?>
      <p class="w3-text-blue"><?php echo $message; ?></p>
    <?php endif; ?>
    <form method="post">
      <label>New Password</label>
      <input class="w3-input w3-border w3-margin-top" type="password" name="new_password" required>

      <label class="w3-margin-top">Confirm Password</label>
      <input class="w3-input w3-border" type="password" name="confirm_password" required>

      <button class="w3-button w3-green w3-margin-top">Reset Password</button>
    </form>
  </div>
</body>
</html>

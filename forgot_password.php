<?php
session_start();
include 'connection.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'src/Exception.php';
require 'src/PHPMailer.php';
require 'src/SMTP.php';

$message = $error = "";

// Default role if not provided
$role = isset($_GET['role']) && in_array($_GET['role'], ['student', 'teacher']) ? $_GET['role'] : 'student';
$backToLoginUrl = $role === 'teacher' ? 'teacher_login.php' : 'student_login.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);

    // Check in both tables
    $stmt = $conn->prepare("
        SELECT id, 'student' AS role FROM students WHERE email = ?
        UNION
        SELECT id, 'teacher' AS role FROM teachers WHERE email = ?
    ");
    $stmt->bind_param("ss", $email, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $user_id = $row['id'];
        $role = $row['role'];

        // Generate secure token
        $token = bin2hex(random_bytes(16));
        $expiry = gmdate("Y-m-d H:i:s", strtotime("+1 hour")); // Use UTC

        // Insert or update token in DB
        $stmt2 = $conn->prepare("
            INSERT INTO password_resets (user_id, role, token, expiry)
            VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE token = VALUES(token), expiry = VALUES(expiry)
        ");
        $stmt2->bind_param("isss", $user_id, $role, $token, $expiry);
        $stmt2->execute();

        // Reset link
        $reset_link = "http://localhost/coaching_project/reset_password.php?token=$token";

        // Send email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'achaldalvi2007@gmail.com';         
            $mail->Password = 'edxdwprbbetcyefc';                
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('achaldalvi2007@gmail.com', 'Achal Dalvi');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request';
            $mail->Body = "Hello,<br><br>Click the button below to reset your password. This link is valid for 1 hour.<br><br>
            <a href='$reset_link' style='padding:10px 20px;background:#0078D7;color:white;text-decoration:none;border-radius:5px;'>Reset Password</a><br><br>
            Or copy and paste this link in your browser:<br>$reset_link";

            $mail->send();
            $message = "A reset link has been sent to your email.";
        } catch (Exception $e) {
            $error = "Mail error: " . $mail->ErrorInfo;
        }
    } else {
        $error = "Email not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Forgot Password</title>
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css" />
<style>
    body {
        background: #f0f4f8;
        font-family: Arial, sans-serif;
        padding: 30px;
    }
    .container {
        max-width: 400px;
        margin: auto;
        background: white;
        padding: 25px 30px;
        border-radius: 10px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }
    h2 {
        text-align: center;
        color: #222;
        margin-bottom: 20px;
        font-weight: 700;
    }
    label {
        font-weight: 600;
        color: #555;
    }
    input[type="email"] {
        width: 100%;
        padding: 12px;
        margin-top: 8px;
        margin-bottom: 18px;
        border-radius: 6px;
        border: 1.5px solid #ccc;
        font-size: 1rem;
        transition: border-color 0.3s ease;
    }
    input[type="email"]:focus {
        outline: none;
        border-color: #0078D7;
    }
    button {
        background-color: #0078D7;
        color: white;
        padding: 14px;
        width: 100%;
        border: none;
        border-radius: 7px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }
    button:hover {
        background-color: #005aab;
    }
    .message {
        padding: 12px;
        margin-bottom: 18px;
        font-weight: 600;
        text-align: center;
        border-radius: 6px;
    }
    .success {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }
    .error {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
    .back-login {
        margin-top: 15px;
        text-align: center;
    }
    .back-login a {
        text-decoration: none;
        color: #0078D7;
        font-weight: 600;
        transition: color 0.3s ease;
    }
    .back-login a:hover {
        color: #005aab;
        text-decoration: underline;
    }
</style>
</head>
<body>

<div class="container w3-card-4">
    <h2>Forgot Password</h2>

    <?php if ($message): ?>
        <div class="message success"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="message error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
        <label for="email">Enter your email address:</label>
        <input type="email" id="email" name="email" placeholder="your.email@example.com" required />
        <button type="submit">Send Reset Link</button>
    </form>

    <div class="back-login">
        <a href="<?= htmlspecialchars($backToLoginUrl) ?>">← Back to Login</a>
    </div>
</div>

</body>
</html>

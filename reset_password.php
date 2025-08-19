<?php
session_start();
include 'connection.php';

// Ensure consistent timezone handling (use UTC for token check)
date_default_timezone_set("Asia/Kolkata");

// Get token from URL
$token = $_GET['token'] ?? '';
$message = $error = "";
$show_form = false;

if (empty($token)) {
    $error = "Invalid or missing token.";
} else {
    // Validate token and expiry
    $stmt = $conn->prepare("SELECT * FROM password_resets WHERE token = ? AND expiry > UTC_TIMESTAMP()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $user_id = $row['user_id'];
        $role = $row['role'];
        $show_form = true;

        // Handle form submission
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $new_password = $_POST['new_password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';

            if ($new_password !== $confirm_password) {
                $error = "Passwords do not match.";
            } elseif (strlen($new_password) < 6) {
                $error = "Password must be at least 6 characters.";
            } else {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

                // Update password in the correct table
                if ($role === 'student') {
                    $update = $conn->prepare("UPDATE students SET password = ? WHERE id = ?");
                } else {
                    $update = $conn->prepare("UPDATE teachers SET password = ? WHERE id = ?");
                }

                $update->bind_param("si", $hashed_password, $user_id);
                if ($update->execute()) {
                    // Delete token after successful reset
                    $delete = $conn->prepare("DELETE FROM password_resets WHERE token = ?");
                    $delete->bind_param("s", $token);
                    $delete->execute();

                    $message = "Password reset successful. Redirecting to login...";
                    $show_form = false;

                    // Redirect to correct login page
                    $login_url = ($role === 'teacher') ? 'teacher_login.php' : 'student_login.php';
                    header("refresh:3;url=$login_url");
                    exit();
                } else {
                    $error = "Failed to update password. Please try again.";
                }
            }
        }
    } else {
        $error = "Invalid or expired token.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #eef2f5;
            padding-top: 60px;
            text-align: center;
        }
        .form-container {
            background: #fff;
            display: inline-block;
            padding: 30px 40px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        h2 {
            margin-bottom: 20px;
        }
        input[type="password"] {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 16px;
        }
        button {
            padding: 12px 30px;
            background: #0078D7;
            color: white;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            background: #005aab;
        }
        .error {
            color: red;
            margin-bottom: 10px;
            font-weight: 600;
        }
        .message {
            color: green;
            margin-bottom: 10px;
            font-weight: 600;
        }
        a {
            color: #0078D7;
            text-decoration: none;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Reset Password</h2>

    <?php if ($message): ?>
        <div class="message"><?= $message ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>

    <?php if ($show_form): ?>
    <form method="POST">
        <input type="password" name="new_password" placeholder="New Password" required><br>
        <input type="password" name="confirm_password" placeholder="Confirm Password" required><br>
        <button type="submit">Reset Password</button>
    </form>
    <?php else: ?>
        <p><a href="forgot_password.php">← Go back to Forgot Password</a></p>
    <?php endif; ?>
</div>

</body>
</html>

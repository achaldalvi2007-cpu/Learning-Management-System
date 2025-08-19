<?php
session_start();
include 'connection.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $email = $_POST['email'];
  $password = $_POST['password'];

  // Prepared statement to prevent SQL Injection
  $stmt = $conn->prepare("SELECT * FROM students WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows == 1) {
    $row = $result->fetch_assoc();

    // Verify hashed password
if (password_verify($password, $row['password'])) {
  $_SESSION['student_id'] = $row['id'];
  $_SESSION['student_name'] = $row['name'];
  $_SESSION['student_email'] = $row['email']; 
  $_SESSION['role'] = 'student';
  header("Location: student_dashboard.php");
  exit();
}
else {
      $error = "Wrong password!";
    }
  } else {
    $error = "Email not found!";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Student Login</title>
  <style>
    /* Basic Reset */
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
      background: #f0f4f8;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .login-container {
      background: white;
      padding: 30px 40px;
      border-radius: 10px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.1);
      width: 100%;
      max-width: 400px;
    }

    .login-container h2 {
      margin-bottom: 20px;
      color: #333;
      text-align: center;
    }

    form {
      display: flex;
      flex-direction: column;
    }

    label {
      margin-bottom: 6px;
      font-weight: 600;
      color: #555;
    }

    input[type="email"],
    input[type="password"] {
      padding: 12px 15px;
      margin-bottom: 20px;
      border: 1.5px solid #ccc;
      border-radius: 6px;
      font-size: 1rem;
      transition: border-color 0.3s ease;
    }

    input[type="email"]:focus,
    input[type="password"]:focus {
      border-color: #007BFF;
      outline: none;
    }

    button {
      background-color: #007BFF;
      border: none;
      padding: 14px;
      color: white;
      font-weight: 700;
      font-size: 1.1rem;
      border-radius: 6px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    button:hover {
      background-color: #0056b3;
    }

    .error-message {
      background-color: #ffe1e1;
      color: #cc0000;
      padding: 12px;
      margin-bottom: 20px;
      border-radius: 6px;
      font-weight: 600;
      text-align: center;
      box-shadow: 0 1px 5px rgba(204,0,0,0.2);
    }

    .back-link {
      display: block;
      margin-top: 15px;
      text-align: center;
      color: #007BFF;
      text-decoration: none;
      font-weight: 600;
      font-size: 0.9rem;
    }

    .back-link:hover {
      text-decoration: underline;
    }

    /* New styling for forgot password */
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
</head>
<body>
  <div class="login-container">
    <h2>Student Login</h2>
    <?php if ($error): ?>
      <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST" action="">
      <label for="email">Email:</label>
      <input type="email" id="email" name="email" required placeholder="Enter your email" />

      <label for="password">Password:</label>
      <input type="password" id="password" name="password" required placeholder="Enter your password" />

      <button type="submit">Login</button>
    </form>

    <!-- Forgot password link -->
    <a href="forgot_password.php?role=student" class="forgot-password">Forgot Password?</a>

    <a class="back-link" href="index.php">← Back to Home</a>
  </div>
</body>
</html>

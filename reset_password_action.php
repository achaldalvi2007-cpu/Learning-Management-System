<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = intval($_POST['user_id']);
    $role = $_POST['role'];
    $new_password = $_POST['new_password'];

    if (empty($new_password)) {
        header("Location: admin_user_list.php?error=Password+cannot+be+empty");
        exit();
    }

    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    if ($role === 'admin') {
        $stmt = $conn->prepare("UPDATE users SET password=? WHERE id=? AND role='admin'");
    } elseif ($role === 'teacher') {
        $stmt = $conn->prepare("UPDATE teachers SET password=? WHERE id=?");
    } elseif ($role === 'student') {
        $stmt = $conn->prepare("UPDATE students SET password=? WHERE id=?");
    } else {
        header("Location: admin_user_list.php?error=Invalid+user+role");
        exit();
    }

    if (!$stmt) {
        header("Location: admin_user_list.php?error=Database+error");
        exit();
    }

    $stmt->bind_param("si", $hashed_password, $user_id);

    if ($stmt->execute()) {
        header("Location: admin_user_list.php?message=Password+reset+successfully");
    } else {
        header("Location: admin_user_list.php?error=Failed+to+reset+password");
    }
    exit();
} else {
    header("Location: admin_user_list.php");
    exit();
}

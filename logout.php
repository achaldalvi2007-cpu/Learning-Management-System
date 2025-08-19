<?php
session_start();

if (isset($_SESSION['role'])) {
    $role = $_SESSION['role'];
    session_destroy();

    if ($role == 'student') {
        header("Location: student_login.php");
    } elseif ($role == 'teacher') {
        header("Location: teacher_login.php");
    } else {
        header("Location: index.php");
    }
} else {
    header("Location: index.php");
}

exit();
?>

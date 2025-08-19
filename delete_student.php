<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

if (isset($_GET['id'])) {
    $student_id = intval($_GET['id']);
    $conn->query("DELETE FROM students WHERE id = $student_id");
}

header("Location: manage_students.php");
exit();
?>

<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: manage_teachers.php");
    exit();
}

$id = intval($_GET['id']);

// Optional: Prevent deleting yourself if admins can be teachers too
// if ($id == $_SESSION['admin_id']) {
//     die("You cannot delete yourself.");
// }

// First, optionally delete any related data (if needed)
// e.g., notes uploaded by teacher - either delete or reassign

// For now, just delete teacher
$stmt = $conn->prepare("DELETE FROM teachers WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    $_SESSION['message'] = "Teacher deleted successfully!";
} else {
    $_SESSION['message'] = "Error deleting teacher.";
}

header("Location: manage_teachers.php");
exit();
?>

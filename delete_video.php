<?php
session_start();
include 'connection.php';

// Check if teacher is logged in
if (!isset($_SESSION['teacher_id'])) {
    header("Location: teacher_login.php");
    exit();
}

$teacher_id = $_SESSION['teacher_id'];

// Check if video ID is provided via GET
if (!isset($_GET['id'])) {
    // No video ID, redirect back to dashboard
    header("Location: teacher_dashboard.php");
    exit();
}

$video_id = intval($_GET['id']);

// Verify the video belongs to the logged-in teacher
$stmt = $conn->prepare("SELECT id FROM videos WHERE id = ? AND teacher_id = ?");
$stmt->bind_param("ii", $video_id, $teacher_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Video not found or does not belong to this teacher
    $stmt->close();
    header("Location: teacher_dashboard.php");
    exit();
}
$stmt->close();

// Delete the video record from database
$stmt_del = $conn->prepare("DELETE FROM videos WHERE id = ? AND teacher_id = ?");
$stmt_del->bind_param("ii", $video_id, $teacher_id);

if ($stmt_del->execute()) {
    $stmt_del->close();
    // Redirect back with success message
    header("Location: teacher_dashboard.php?msg=video_deleted");
    exit();
} else {
    $stmt_del->close();
    // Redirect back with error message
    header("Location: teacher_dashboard.php?msg=error_deleting_video");
    exit();
}

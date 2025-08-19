<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['teacher_id'])) {
  header("Location: teacher_login.php");
  exit();
}

if (isset($_GET['id'])) {
  $note_id = $_GET['id'];

  // First delete file
  $result = $conn->query("SELECT file_path FROM notes WHERE id = $note_id");
  if ($result && $row = $result->fetch_assoc()) {
    if (file_exists($row['file_path'])) {
      unlink($row['file_path']);
    }
  }

  // Then delete from DB
  $conn->query("DELETE FROM notes WHERE id = $note_id");
}

header("Location: teacher_dashboard.php");
exit();
?>

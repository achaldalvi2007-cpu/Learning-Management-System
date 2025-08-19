<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['teacher_id'])) {
    header("Location: teacher_login.php");
    exit();
}

$teacher_id = $_SESSION['teacher_id'];
$teacher_name = $_SESSION['teacher_name'];

// Fetch notes uploaded by this teacher
$notes_sql = "SELECT * FROM notes WHERE teacher_id = ? ORDER BY upload_date DESC";
$stmt = $conn->prepare($notes_sql);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$notes_result = $stmt->get_result();

// Fetch videos uploaded by this teacher
$videos_sql = "SELECT * FROM videos WHERE teacher_id = ? ORDER BY upload_date DESC";
$stmt2 = $conn->prepare($videos_sql);
$stmt2->bind_param("i", $teacher_id);
$stmt2->execute();
$videos_result = $stmt2->get_result();
?>

<!DOCTYPE html>
<html>
<head>
  <title>Teacher Dashboard</title>
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
  <style>
    .w3-card {
      margin-bottom: 20px;
    }
    .item {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 16px;
      border-bottom: 1px solid #ccc;
    }
    .item-info {
      display: flex;
      flex-direction: column;
    }
    .item-buttons a {
      margin-left: 8px;
    }
    .bottom-buttons {
      margin-top: 30px;
      display: flex;
      justify-content: space-between;
      flex-wrap: wrap;
    }
    .top-buttons {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
      margin-bottom: 20px;
    }
  </style>
</head>
<body class="w3-light-grey">

<div class="w3-content" style="max-width:800px; margin-top:40px;">
  <div class="w3-card-4 w3-white w3-padding">

    <h2 class="w3-center">👨‍🏫 Welcome, <?php echo htmlspecialchars($teacher_name); ?></h2>

    <!-- Top Upload Buttons -->
    <div class="top-buttons">
      <a href="upload_note.php" class="w3-button w3-green">➕ Upload New Note</a>
      <a href="upload_video_link.php" class="w3-button w3-green">🎥 Upload New Video/Link</a>
    </div>

    <hr>

    <!-- Notes Section -->
    <h3>📄 Your Uploaded Notes</h3>
    <?php if ($notes_result->num_rows > 0) { ?>
      <ul class="w3-ul w3-hoverable" style="list-style-type:none; padding:0; margin:0;">
        <?php while ($note = $notes_result->fetch_assoc()) { ?>
          <li class="item">
            <div class="item-info">
              <span class="w3-large"><?php echo htmlspecialchars($note['title']); ?></span>
              <small>Uploaded on: <?php echo htmlspecialchars($note['upload_date']); ?></small>
            </div>
            <div class="item-buttons">
              <a href="<?php echo htmlspecialchars($note['file_path']); ?>" class="w3-button w3-blue w3-small" download>⬇️ Download</a>
              <a href="delete_note.php?id=<?php echo $note['id']; ?>" class="w3-button w3-red w3-small" onclick="return confirm('Are you sure you want to delete this note?');">🗑️ Delete</a>
            </div>
          </li>
        <?php } ?>
      </ul>
    <?php } else { ?>
      <p class="w3-text-red">❗ You haven't uploaded any notes yet.</p>
    <?php } ?>

    <hr>

    <!-- Videos Section -->
    <h3>🎬 Your Uploaded Videos/Links</h3>
    <?php if ($videos_result->num_rows > 0) { ?>
      <ul class="w3-ul w3-hoverable" style="list-style-type:none; padding:0; margin:0;">
        <?php while ($video = $videos_result->fetch_assoc()) { ?>
          <li class="item">
            <div class="item-info">
              <span class="w3-large"><?php echo htmlspecialchars($video['title']); ?></span>
              <small>Uploaded on: <?php echo htmlspecialchars($video['upload_date']); ?></small>
            </div>
            <div class="item-buttons">
              <a href="delete_video.php?id=<?php echo $video['id']; ?>" class="w3-button w3-red w3-small" onclick="return confirm('Are you sure you want to delete this video?');">🗑️ Delete</a>
            </div>
          </li>
        <?php } ?>
      </ul>
    <?php } else { ?>
      <p class="w3-text-red">❗ You haven't uploaded any videos or links yet.</p>
    <?php } ?>

    <!-- Feedback and Logout Buttons -->
    <div class="bottom-buttons">
      <a href="feedback_submit.php" class="w3-button w3-blue">📝 Give Feedback</a>
      <a href="logout.php" class="w3-button w3-red">Logout</a>
    </div>

  </div>
</div>

</body>
</html>

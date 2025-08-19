<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Fetch all notes with teacher names
$notes_sql = "SELECT notes.id, notes.title, notes.upload_date, notes.file_path, teachers.name AS teacher_name 
              FROM notes 
              JOIN teachers ON notes.teacher_id = teachers.id 
              ORDER BY notes.upload_date DESC";
$notes_result = $conn->query($notes_sql);

// Fetch all videos with teacher names
$videos_sql = "SELECT videos.id, videos.title, videos.upload_date, videos.video_url, teachers.name AS teacher_name 
               FROM videos 
               JOIN teachers ON videos.teacher_id = teachers.id 
               ORDER BY videos.upload_date DESC";
$videos_result = $conn->query($videos_sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>All Notes & Videos - Admin</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <style>
      .header-section {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
      }
      .section-title {
        margin-top: 30px;
      }
    </style>
</head>
<body class="w3-container w3-light-grey" style="max-width:900px; margin:auto; margin-top:40px;">

<div class="w3-card-4 w3-white w3-padding">
    <div class="header-section">
        <h2>All Uploaded Notes & Videos</h2>
        <a href="admin_dashboard.php" class="w3-button w3-light-grey w3-margin-left">Back to Dashboard</a>
    </div>

    <!-- Notes Section -->
    <h3 class="section-title">📄 Uploaded Notes</h3>
    <?php if ($notes_result->num_rows > 0) { ?>
        <table class="w3-table-all w3-hoverable">
            <thead>
                <tr class="w3-blue">
                    <th>Title</th>
                    <th>Uploaded By</th>
                    <th>Upload Date</th>
                    <th>Download</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($note = $notes_result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($note['title']); ?></td>
                    <td><?php echo htmlspecialchars($note['teacher_name']); ?></td>
                    <td><?php echo htmlspecialchars($note['upload_date']); ?></td>
                    <td>
                        <a href="<?php echo htmlspecialchars($note['file_path']); ?>" class="w3-button w3-green" download>Download</a>
                    </td>
                    <td>
                        <a href="admin_delete_note.php?id=<?php echo $note['id']; ?>" class="w3-button w3-red" onclick="return confirm('Delete this note?');">Delete</a>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    <?php } else { ?>
        <p class="w3-text-red">No notes uploaded yet.</p>
    <?php } ?>

    <!-- Videos Section -->
    <h3 class="section-title">🎥 Uploaded Videos/Links</h3>
    <?php if ($videos_result->num_rows > 0) { ?>
        <table class="w3-table-all w3-hoverable">
            <thead>
                <tr class="w3-blue">
                    <th>Title</th>
                    <th>Uploaded By</th>
                    <th>Upload Date</th>
                    <th>Watch</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($video = $videos_result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($video['title']); ?></td>
                    <td><?php echo htmlspecialchars($video['teacher_name']); ?></td>
                    <td><?php echo htmlspecialchars($video['upload_date']); ?></td>
                    <td>
                        <a href="<?php echo htmlspecialchars($video['video_url']); ?>" class="w3-button w3-blue" target="_blank" rel="noopener noreferrer">Watch Video</a>
                    </td>
                    <td>
                        <a href="admin_delete_video.php?id=<?php echo $video['id']; ?>" class="w3-button w3-red" onclick="return confirm('Delete this video?');">Delete</a>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    <?php } else { ?>
        <p class="w3-text-red">No videos uploaded yet.</p>
    <?php } ?>

</div>

</body>
</html>

<?php
session_start();
include 'connection.php';

// Check if teacher is logged in
if (!isset($_SESSION['teacher_id'])) {
    header("Location: teacher_login.php");
    exit();
}

$error = '';
$success = '';

$subjects = ['Physics', 'Chemistry', 'Maths', 'Biology']; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $video_url = trim($_POST['video_url']);
    $subject = $_POST['subject'] ?? '';
    $teacher_id = $_SESSION['teacher_id'];

    // Basic validation
    if (empty($title) || empty($video_url) || empty($subject)) {
        $error = "Please fill in all fields.";
    } elseif (!in_array($subject, $subjects)) {
        $error = "Please select a valid subject.";
    } elseif (!filter_var($video_url, FILTER_VALIDATE_URL)) {
        $error = "Invalid video URL format.";
    } else {
        // Insert into videos table
        $stmt = $conn->prepare("INSERT INTO videos (title, video_url, teacher_id, subject, upload_date) VALUES (?, ?, ?, ?, CURDATE())");
        $stmt->bind_param("ssis", $title, $video_url, $teacher_id, $subject);

        if ($stmt->execute()) {
            $success = "Video link uploaded successfully!";
        } else {
            $error = "Database error: Could not save video.";
        }

        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Upload Video Link</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
</head>
<body class="w3-container w3-light-grey">

<div class="w3-card-4 w3-white w3-margin-top w3-padding" style="max-width:600px; margin:auto;">
    <h2>Upload Lecture Video / YouTube Link</h2>

    <?php if ($error): ?>
        <div class="w3-panel w3-red"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="w3-panel w3-green"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <form method="POST" class="w3-container">
        <label>Video Title</label>
        <input class="w3-input w3-border" type="text" name="title" required>

        <label>Subject</label>
        <select name="subject" class="w3-select w3-border" required>
            <option value="" disabled selected>Select Subject</option>
            <?php foreach ($subjects as $sub): ?>
                <option value="<?php echo htmlspecialchars($sub); ?>"><?php echo htmlspecialchars($sub); ?></option>
            <?php endforeach; ?>
        </select>

        <label>YouTube / Lecture Video Link</label>
        <input class="w3-input w3-border" type="url" name="video_url" placeholder="https://youtu.be/..." required>

        <br>
        <button class="w3-button w3-blue w3-margin-top" type="submit">Upload Video</button>
    </form>

    <br>
    <a href="teacher_dashboard.php" class="w3-button w3-gray">Back to Dashboard</a>
</div>

</body>
</html>

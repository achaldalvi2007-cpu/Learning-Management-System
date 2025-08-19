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
    $subject = $_POST['subject'] ?? 'General';
    $teacher_id = $_SESSION['teacher_id'];
    $file = $_FILES['pdf_file'];

    if (empty($title)) {
        $error = "Please enter note title.";
    } elseif (!in_array($subject, $subjects)) {
        $error = "Please select a valid subject.";
    } elseif ($file['error'] !== 0) {
        $error = "Error uploading file.";
    } elseif ($file['type'] !== 'application/pdf') {
        $error = "Only PDF files are allowed.";
    } else {
        $upload_dir = 'files/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $filename = uniqid() . '_' . basename($file['name']);
        $target_path = $upload_dir . $filename;

        if (move_uploaded_file($file['tmp_name'], $target_path)) {
            $stmt = $conn->prepare("INSERT INTO notes (title, file_path, teacher_id, subject, upload_date) VALUES (?, ?, ?, ?, CURDATE())");
            $stmt->bind_param("ssis", $title, $target_path, $teacher_id, $subject);

            if ($stmt->execute()) {
                $success = "Note uploaded successfully!";
            } else {
                $error = "Database error. Try again.";
                unlink($target_path);
            }

            $stmt->close();
        } else {
            $error = "Failed to save uploaded file.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Upload Note</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
</head>
<body class="w3-container w3-light-grey">

<div class="w3-card-4 w3-white w3-margin-top w3-padding" style="max-width:600px; margin:auto;">
    <h2>📄 Upload New Note</h2>

    <?php if ($error): ?>
        <div class="w3-panel w3-red"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="w3-panel w3-green"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="w3-container">
        <label>Note Title</label>
        <input class="w3-input w3-border" type="text" name="title" required>

        <label>Subject</label>
        <select name="subject" class="w3-select w3-border" required>
            <option value="" disabled selected>Select Subject</option>
            <?php foreach ($subjects as $sub): ?>
                <option value="<?php echo htmlspecialchars($sub); ?>"><?php echo htmlspecialchars($sub); ?></option>
            <?php endforeach; ?>
        </select>

        <label>Upload PDF File</label>
        <input class="w3-input w3-border" type="file" name="pdf_file" accept="application/pdf" required>

        <br>
        <button class="w3-button w3-blue w3-margin-top" type="submit">Upload</button>
    </form>

    <br>
    <a href="teacher_dashboard.php" class="w3-button w3-gray">⬅️ Back to Dashboard</a>
</div>

</body>
</html>

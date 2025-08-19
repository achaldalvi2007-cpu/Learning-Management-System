<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: student_login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

function getEmbedURL($url) {
    $parsed_url = parse_url($url);
    if (!isset($parsed_url['host'])) return $url;
    if (strpos($parsed_url['host'], 'youtube.com') !== false) {
        parse_str($parsed_url['query'] ?? '', $params);
        if (isset($params['v'])) {
            return "https://www.youtube.com/embed/" . $params['v'] . "?rel=0";
        }
    } elseif (strpos($parsed_url['host'], 'youtu.be') !== false) {
        $video_id = ltrim($parsed_url['path'], '/');
        return "https://www.youtube.com/embed/" . $video_id . "?rel=0";
    }
    return $url;
}

// Fetch student info
$student_query = $conn->query("SELECT fees_paid, total_fees FROM students WHERE id='$student_id'");
$student = $student_query->fetch_assoc();

// Subjects
$subjects = ['Physics', 'Chemistry', 'Maths'];

// Fetch Notes
$notes_result = $conn->query("SELECT n.*, t.name AS teacher_name FROM notes n JOIN teachers t ON n.teacher_id = t.id ORDER BY subject, upload_date DESC");
$notes_by_subject = [];
while ($row = $notes_result->fetch_assoc()) {
    $notes_by_subject[$row['subject']][] = $row;
}

// Fetch Videos
$videos_result = $conn->query("SELECT v.*, t.name AS teacher_name FROM videos v JOIN teachers t ON v.teacher_id = t.id ORDER BY subject, upload_date DESC");
$videos_by_subject = [];
while ($row = $videos_result->fetch_assoc()) {
    $videos_by_subject[$row['subject']][] = $row;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Student Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <style>
        .tab-button {
            cursor: pointer;
        }
        .tab-content {
            display: none;
        }
        .video-popup {
            position: fixed;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            background: #fff;
            border: 2px solid #444;
            padding: 10px;
            z-index: 9999;
            max-width: 90vw;
            max-height: 80vh;
            width: 560px;
            height: 315px;
            display: none;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.5);
        }
        .video-popup iframe {
            width: 100%;
            height: 100%;
            border-radius: 5px;
        }
        .close-btn {
            position: absolute;
            top: 5px;
            right: 10px;
            background: #e74c3c;
            color: white;
            border: none;
            font-size: 16px;
            border-radius: 50%;
            padding: 4px 9px;
            cursor: pointer;
        }
    </style>
</head>
<body class="w3-light-grey">

<div class="w3-container w3-card-4 w3-white w3-margin-top w3-padding" style="max-width: 1000px; margin: auto;">
    <h2>Welcome, <?= htmlspecialchars($_SESSION['student_name']) ?></h2>

    <h3>📊 Fees Status</h3>
    <p><strong>Paid:</strong> ₹<?= htmlspecialchars($student['fees_paid']) ?></p>
    <p><strong>Total:</strong> ₹<?= htmlspecialchars($student['total_fees']) ?></p>

    <h3>📚 Subject Tabs</h3>
    <div class="w3-bar w3-border w3-margin-bottom">
        <?php foreach ($subjects as $subject): ?>
            <button class="w3-bar-item w3-button tab-button" onclick="showTab('<?= $subject ?>')"><?= $subject ?></button>
        <?php endforeach; ?>
    </div>

    <?php foreach ($subjects as $subject): ?>
        <div id="<?= $subject ?>" class="tab-content">
            <!-- Notes -->
            <h4 class="w3-blue w3-padding">📘 <?= $subject ?> Notes</h4>
            <?php if (!empty($notes_by_subject[$subject])): ?>
                <div class="w3-row-padding">
                    <?php foreach ($notes_by_subject[$subject] as $note): ?>
                        <div class="w3-third w3-margin-top">
                            <div class="w3-card w3-padding w3-white">
                                <strong><?= htmlspecialchars($note['title']) ?></strong><br />
                                <small>By <?= htmlspecialchars($note['teacher_name']) ?></small><br />
                                <small><?= htmlspecialchars($note['upload_date']) ?></small><br /><br />
                                <a href="<?= htmlspecialchars($note['file_path']) ?>" class="w3-button w3-green w3-small" download>📥 Download</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="w3-text-red">No notes available for <?= $subject ?></p>
            <?php endif; ?>

            <!-- Videos -->
            <h4 class="w3-orange w3-padding">🎬 <?= $subject ?> Videos</h4>
            <?php if (!empty($videos_by_subject[$subject])): ?>
                <div class="w3-row-padding">
                    <?php foreach ($videos_by_subject[$subject] as $video): 
                        $embed_url = getEmbedURL($video['video_url']);
                    ?>
                        <div class="w3-third w3-margin-top">
                            <div class="w3-card w3-padding w3-white">
                                <strong><?= htmlspecialchars($video['title']) ?></strong><br />
                                <small>By <?= htmlspecialchars($video['teacher_name']) ?></small><br />
                                <small><?= htmlspecialchars($video['upload_date']) ?></small><br /><br />
                                <button class="w3-button w3-blue w3-small" onclick="openVideoPopup('<?= $embed_url ?>')">▶️ Watch</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="w3-text-red">No videos available for <?= $subject ?></p>
            <?php endif; ?>
            <hr />
        </div>
    <?php endforeach; ?>

    <div class="w3-margin-top w3-bar">
        <a href="feedback_submit.php" class="w3-bar-item w3-button w3-blue">📝 Feedback</a>
        <a href="logout.php" class="w3-bar-item w3-button w3-red w3-right">Logout</a>
    </div>
</div>

<!-- Video Popup -->
<div id="video-popup" class="video-popup">
    <button class="close-btn" onclick="closeVideoPopup()">×</button>
    <iframe id="video-iframe" src="" frameborder="0" allowfullscreen></iframe>
</div>

<script>
function showTab(subject) {
    const tabs = document.querySelectorAll('.tab-content');
    tabs.forEach(tab => tab.style.display = 'none');
    document.getElementById(subject).style.display = 'block';
}

// Auto show first tab
document.addEventListener("DOMContentLoaded", () => {
    const firstTab = document.querySelector(".tab-content");
    if (firstTab) firstTab.style.display = "block";
});

function openVideoPopup(url) {
    document.getElementById('video-iframe').src = url;
    document.getElementById('video-popup').style.display = 'block';
}
function closeVideoPopup() {
    document.getElementById('video-popup').style.display = 'none';
    document.getElementById('video-iframe').src = '';
}
</script>

</body>
</html>

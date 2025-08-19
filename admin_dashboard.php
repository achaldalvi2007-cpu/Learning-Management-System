<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: #f1f1f1;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        }
        .dashboard-container {
            max-width: 1000px;
            margin: 50px auto;
        }
        .welcome {
            text-align: center;
            margin-bottom: 40px;
        }
        .welcome h2 {
            font-weight: 700;
            color: #333;
        }
        .cards {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            flex-wrap: wrap;
        }
        .card {
            background: #fff;
            padding: 30px 20px;
            border-radius: 10px;
            flex: 1 1 260px;
            box-shadow: 0 6px 12px rgba(0,0,0,0.1);
            transition: box-shadow 0.3s ease;
            cursor: pointer;
            text-align: center;
            color: #333;
            text-decoration: none;
        }
        .card:hover {
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
            color: #00796b;
        }
        .card i {
            font-size: 48px;
            margin-bottom: 15px;
            color: #00796b;
        }
        .card h3 {
            margin: 0;
            font-weight: 600;
            font-size: 22px;
        }
        .logout-btn {
            display: block;
            max-width: 120px;
            margin: 40px auto 0 auto;
            background: #e53935;
            color: white !important;
            padding: 12px;
            border-radius: 25px;
            text-align: center;
            font-weight: 600;
            text-decoration: none;
            box-shadow: 0 4px 8px rgba(229, 57, 53, 0.4);
            transition: background 0.3s ease;
        }
        .logout-btn:hover {
            background: #b71c1c;
            box-shadow: 0 6px 14px rgba(183, 28, 28, 0.6);
        }
.card:hover i {
  transform: scale(1.2);
  transition: 0.3s;
}

    </style>
</head>
<body>

<div class="dashboard-container w3-container">

    <div class="welcome">
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?> <span style="color:#00796b;">(Admin)</span></h2>
        <p>Manage your coaching class system below.</p>
    </div>

    <div class="cards">

        <a href="manage_students.php" class="card" title="Add, edit or remove students">
            <i class="fas fa-user-graduate"></i>
            <h3>Manage Students</h3>
        </a>

        <a href="manage_teachers.php" class="card" title="Add, update or delete teacher details">
            <i class="fas fa-chalkboard-teacher"></i>
            <h3>Manage Teachers</h3>
        </a>

        <a href="manage_fees.php" class="card" title="View, update or track student fee payments">
            <i class="fas fa-money-bill-wave"></i>
            <h3>Manage Fees</h3>
        </a>

        <a href="admin_view_notes.php" class="card" title="Access and review uploaded notes and videos">
            <i class="fas fa-file-alt"></i>
            <h3>View Notes/Lecture Videos</h3>
        </a>

        <a href="admin_user_list.php" class="card" title="Reset passwords for teachers and students">
            <i class="fas fa-key"></i>
            <h3>Reset Password</h3>
        </a>

<a href="feedbacks.php" class="card" title="Read suggestions and feedback from students and teachers">
    <i class="fas fa-comment-dots"></i>
    <h3>Feedback System</h3>
</a>


    </div>

    <a href="logout.php" class="logout-btn w3-button">Logout</a>

</div>

<!-- FontAwesome JS (optional for icons) -->
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

</body>
</html>

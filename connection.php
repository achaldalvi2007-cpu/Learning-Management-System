<?php
$servername = "localhost";
$username = "root";
$password = "Admin@123";
$dbname = "coaching_class"; 

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

<?php
$host = "localhost";       // Your database host
$db_user = "root";         // Your database username
$db_password = "";         // Your database password
$db_name = "integris"; // Your database name

// Create a database connection
$conn = new mysqli($host, $db_user, $db_password, $db_name);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Database connection failed: " . $conn->connect_error]));
}
?>

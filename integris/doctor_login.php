<?php
header('Content-Type: application/json');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "config.php";

$user = $_POST['username'] ?? '';
$pass = $_POST['password'] ?? '';

if (empty($user) || empty($pass)) {
    echo json_encode(["status" => "fail", "message" => "All fields are required."]);
    exit;
}

// Use prepared statement for security
$query = "SELECT * FROM doctor_register WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $user);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();

    // Use password_verify for hashed password comparison
    if (password_verify($pass, $row['password'])) {
        echo json_encode([
            "status" => "success",
            "message" => "Login successful.",
            "data" => [
                "doctor_id" => $row['id'],  // Optional: Send additional doctor data
                "username" => $row['username'],
                "speciality" => $row['speciality'] ?? ''
            ]
        ]);
    } else {
        echo json_encode(["status" => "fail", "message" => "Invalid username or password."]);
    }
} else {
    echo json_encode(["status" => "fail", "message" => "Invalid username or password."]);
}

$stmt->close();
$conn->close();
?>

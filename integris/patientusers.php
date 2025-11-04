<?php
// Enable Error Reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'config.php';

$conn = new mysqli($servername, $username, $password, $dbname);

// Check Connection
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Connection failed: " . $conn->connect_error]));
}

// Get data from POST (form-data)
$user = $_POST['username'] ?? '';
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';
$pass = $_POST['password'] ?? '';

// Validate input fields
if (empty($user) || empty($email) || empty($phone) || empty($pass)) {
    echo json_encode(["status" => "fail", "message" => "All fields are required."]);
    exit;
}

// Check if email already exists
$checkQuery = "SELECT * FROM patient_register WHERE email = ?";
$stmt = $conn->prepare($checkQuery);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(["status" => "fail", "message" => "Email already registered."]);
} else {
    // Insert new user
    $insertQuery = "INSERT INTO patient_register (username, email, phone, password, created_at) VALUES (?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("ssss", $user, $email, $phone, $pass);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Registration successful."]);
    } else {
        echo json_encode(["status" => "fail", "message" => "Registration failed."]);
    }
}

// Close connections
$stmt->close();
$conn->close();
?>

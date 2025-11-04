<?php
header("Content-Type: application/json");

// ✅ Enable error reporting (for development)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ✅ Ensure it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid request method. Please use POST."
    ]);
    exit();
}

// ✅ Include database connection (this gives us $mysqli)
require_once 'config.php';

// ✅ Get 'phone' from POST
$phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';

if (empty($phone)) {
    echo json_encode([
        "status" => "error",
        "message" => "Missing 'phone' parameter."
    ]);
    exit();
}

// ✅ Sanitize phone input
$phone = $conn->real_escape_string($phone);

// ✅ Query to fetch patient by phone
$sql = "SELECT * FROM patients WHERE phone = '$phone'";
$result = $conn->query($sql);

$patients = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $patients[] = $row;
    }

    echo json_encode([
        "status" => "success",
        "data" => $patients
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "No patient found with that phone number."
    ]);
}

// ✅ Close connection
$conn->close();
?>

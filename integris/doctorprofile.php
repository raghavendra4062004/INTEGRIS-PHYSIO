<?php
header('Content-Type: application/json');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "config.php";

// Enforce POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["status" => "fail", "message" => "Invalid request method."]);
    exit;
}

// Receive data from form
$doctorID = $_POST['username'] ?? '';
$name = $_POST['name'] ?? '';
$speciality = $_POST['speciality'] ?? '';
$gender = $_POST['gender'] ?? '';
$contact = $_POST['contact'] ?? '';
$doctorNote = $_POST['doctorNote'] ?? '';

// Validate required fields
if (empty($doctorID) || empty($name) || empty($speciality) || empty($gender) || empty($contact)) {
    echo json_encode(["status" => "fail", "message" => "All fields are required."]);
    exit;
}

// Check if doctorID already exists
$query = "SELECT * FROM doctor_profile WHERE doctorID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $doctorID);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    echo json_encode(["status" => "fail", "message" => "Doctor ID already exists."]);
} else {
    // Save to doctor_profile table
    $insertProfile = "INSERT INTO doctor_profile (doctorID, name, speciality, gender, contact, doctorNote) VALUES (?, ?, ?, ?, ?, ?)";
    $stmtProfile = $conn->prepare($insertProfile);
    $stmtProfile->bind_param("ssssss", $doctorID, $name, $speciality, $gender, $contact, $doctorNote);

    if ($stmtProfile->execute()) {
        echo json_encode(["status" => "success", "message" => "Profile saved successfully."]);
    } else {
        echo json_encode(["status" => "fail", "message" => "Failed to save profile."]);
    }

    $stmtProfile->close();
}

$stmt->close();
$conn->close();
?>

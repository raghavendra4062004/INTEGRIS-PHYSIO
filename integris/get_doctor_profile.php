<?php
header('Content-Type: application/json');

include 'config.php';


$mysqli = new mysqli($host, $user, $pass, $db);
if ($mysqli->connect_errno) {
    echo json_encode(["status" => "fail", "message" => "Database connection failed"]);
    exit;
}

// Read doctorID from POST request
$doctorID = $_POST['doctorID'] ?? '';

if (empty($doctorID)) {
    echo json_encode(["status" => "fail", "message" => "Doctor ID is required"]);
    exit;
}

$stmt = $mysqli->prepare("SELECT * FROM doctor_profile WHERE doctorID = ?");
$stmt->bind_param("s", $doctorID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $doctor = $result->fetch_assoc();
    echo json_encode([
        "status" => "success",
        "data" => $doctor
    ]);
} else {
    echo json_encode([
        "status" => "fail",
        "message" => "No doctor found with this ID"
    ]);
}

$stmt->close();
$mysqli->close();

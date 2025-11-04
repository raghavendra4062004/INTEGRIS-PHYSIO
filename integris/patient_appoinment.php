<?php
header('Content-Type: application/json');

include 'config.php';
// ✅ Connect to database
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    echo json_encode(["status" => "fail", "message" => "Database connection failed"]);
    exit;
}

// ✅ Retrieve POST values
$name = trim($_POST['name'] ?? '');
$age = trim($_POST['age'] ?? '');
$complaint = trim($_POST['complaint'] ?? '');
$selected_date = trim($_POST['selectedDate'] ?? '');
$selected_doctor = trim($_POST['selectedDoctor'] ?? '');
$created_at = date("Y-m-d H:i:s");

// ✅ Validate input
if ($name === '' || $age === '' || $complaint === '' || $selected_date === '' || $selected_doctor === '') {
    echo json_encode(["status" => "fail", "message" => "All fields are required"]);
    exit;
}

// ✅ Insert into database
$stmt = $conn->prepare("INSERT INTO patient_appointment (name, age, complaint, selected_date, selected_doctor, created_at) VALUES (?, ?, ?, ?, ?, ?)");
if ($stmt) {
    $stmt->bind_param("sissss", $name, $age, $complaint, $selected_date, $selected_doctor, $created_at);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Appointment booked successfully"]);
    } else {
        echo json_encode(["status" => "fail", "message" => "Failed to book appointment"]);
    }

    $stmt->close();
} else {
    echo json_encode(["status" => "fail", "message" => "Failed to prepare statement"]);
}

$conn->close();

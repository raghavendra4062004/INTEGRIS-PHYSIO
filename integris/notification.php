<?php
header("Content-Type: application/json");

// ✅ Enable error reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ✅ Database connection
require_once 'config.php';  // Make sure $conn is set correctly here

// ✅ Query to fetch all appointments
$sql = "SELECT name, age, complaint, selected_date, selected_doctor FROM patient_appointment ORDER BY selected_date ASC";
$result = mysqli_query($conn, $sql);

$appointments = [];

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $appointments[] = [
            'name' => $row['name'],
            'age' => $row['age'],
            'complaint' => $row['complaint'],
            'selectedDate' => $row['selected_date'],
            'selectedDoctor' => $row['selected_doctor'],
        ];
    }

    echo json_encode([
        "status" => "success",
        "data" => $appointments
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "No appointments found."
    ]);
}

mysqli_close($conn);
?>

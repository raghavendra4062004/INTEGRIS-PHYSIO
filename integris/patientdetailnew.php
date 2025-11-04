<?php
header("Content-Type: application/json");
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'config.php';

$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$age = isset($_POST['age']) ? trim($_POST['age']) : '';
$dob = isset($_POST['dob']) ? trim($_POST['dob']) : '';
$gender = isset($_POST['gender']) ? trim($_POST['gender']) : '';
$phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
$address = isset($_POST['address']) ? trim($_POST['address']) : '';

$missingFields = [];
if (empty($name)) $missingFields[] = 'name';
if (empty($age)) $missingFields[] = 'age';
if (empty($dob)) $missingFields[] = 'dob';
if (empty($gender)) $missingFields[] = 'gender';
if (empty($phone)) $missingFields[] = 'phone';
if (empty($address)) $missingFields[] = 'address';

if (!empty($missingFields)) {
    echo json_encode([
        "status" => "error",
        "message" => "Missing fields: " . implode(', ', $missingFields)
    ]);
    exit();
}

$name = $conn->real_escape_string($name);
$age = $conn->real_escape_string($age);
$dob = $conn->real_escape_string($dob);
$gender = $conn->real_escape_string($gender);
$phone = $conn->real_escape_string($phone);
$address = $conn->real_escape_string($address);

$sql = "INSERT INTO patients (name, age, dob, gender, phone, address) 
        VALUES ('$name', '$age', '$dob', '$gender', '$phone', '$address')";

if ($conn->query($sql) === TRUE) {
    echo json_encode([
        "status" => "success",
        "message" => "Patient saved successfully"
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "SQL Error: " . $conn->error
    ]);
}

$conn->close();
?>

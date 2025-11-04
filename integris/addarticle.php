<?php
header("Content-Type: application/json");

// Enable error reporting (for development only)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Allow only POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["status" => "error", "message" => "Only POST requests are allowed."]);
    exit();
}

// Include DB connection
include 'config.php';

// Get POST values
$doctorName     = isset($_POST['doctorName']) ? trim($_POST['doctorName']) : '';
$speciality     = isset($_POST['speciality']) ? trim($_POST['speciality']) : '';
$articleTitle   = isset($_POST['articleTitle']) ? trim($_POST['articleTitle']) : '';
$articleContent = isset($_POST['articleContent']) ? trim($_POST['articleContent']) : '';
$imagePath      = null; // To be updated if image is uploaded

// Debug log
$debug = [
    "doctorName" => $doctorName,
    "speciality" => $speciality,
    "articleTitle" => $articleTitle,
    "articleContent" => $articleContent
];

// Validate required fields
if (empty($doctorName) || empty($speciality) || empty($articleTitle) || empty($articleContent)) {
    echo json_encode([
        "status" => "error",
        "message" => "All fields are required.",
        "received" => $debug
    ]);
    exit();
}

// Handle optional image upload
if (isset($_FILES['articleImage']) && $_FILES['articleImage']['error'] === UPLOAD_ERR_OK) {
    $imageTmpPath = $_FILES['articleImage']['tmp_name'];
    $imageName    = basename($_FILES['articleImage']['name']);
    $uploadDir    = 'uploads/articles/';

    // Create folder if not exists
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $imagePath = $uploadDir . time() . '_' . $imageName;

    if (!move_uploaded_file($imageTmpPath, $imagePath)) {
        echo json_encode([
            "status" => "error",
            "message" => "Failed to upload image."
        ]);
        exit();
    }
}

// Insert into database
$sql = "INSERT INTO articles (doctor_name, speciality, title, content, image_path, created_at)
        VALUES (?, ?, ?, ?, ?, NOW())";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sssss", $doctorName, $speciality, $articleTitle, $articleContent, $imagePath);

if ($stmt->execute()) {
    echo json_encode([
        "status" => "success",
        "message" => "Article saved successfully.",
        "image" => $imagePath
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Database error: " . $stmt->error
    ]);
}

$stmt->close();
$conn->close();
?>
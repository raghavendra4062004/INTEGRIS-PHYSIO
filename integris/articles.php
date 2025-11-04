<?php
header("Content-Type: application/json");

// ✅ Include DB config
require_once 'config.php';

// ✅ Query the articles table
$query = "SELECT id, doctor_name, speciality, title, content, created_at, image_path FROM articles ORDER BY created_at DESC";
$result = $conn->query($query); // ✅ Use $conn (not $mysqli)

if (!$result) {
    echo json_encode([
        "status" => "error",
        "message" => "Query failed: " . $conn->error
    ]);
    exit;
}

// ✅ Collect articles into array
$articles = [];
while ($row = $result->fetch_assoc()) {
    $articles[] = $row;
}

// ✅ Return as JSON
echo json_encode(["articles" => $articles]);
?>

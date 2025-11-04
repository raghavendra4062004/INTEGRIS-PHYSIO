<?php
// Show PHP errors (for debugging)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Include DB connection
include 'config.php';

// Fetch all articles from your "articles" table
$sql = "SELECT id, doctor_name, speciality, title, content, created_at, image_path FROM articles";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $articles = [];

    while ($row = $result->fetch_assoc()) {
        $articles[] = [
            "id"          => $row['id'],
            "doctor_name" => $row['doctor_name'],
            "speciality"  => $row['speciality'],
            "title"       => $row['title'],
            "content"     => $row['content'],
            "created_at"  => $row['created_at'],
            "image_path"  => $row['image_path']
        ];
    }

    echo json_encode([
        "success" => true,
        "data" => $articles
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "No articles found"
    ]);
}

$conn->close();
?>

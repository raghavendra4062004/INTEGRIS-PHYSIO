<?php
header('Content-Type: application/json');

// ✅ MySQL DB credentials
include 'config.php';
// ✅ Connect to MySQL database
$mysqli = new mysqli($host, $user, $pass, $db);
if ($mysqli->connect_errno) {
    echo json_encode(["status" => "fail", "message" => "Database connection failed."]);
    exit;
}

// ✅ Define paths
$uploadDir = __DIR__ . '/uploads/';
$metadataFile = __DIR__ . '/videos.json';

// ✅ Ensure upload directory exists
if (!file_exists($uploadDir)) {
    if (!mkdir($uploadDir, 0777, true)) {
        echo json_encode(["status" => "fail", "message" => "Failed to create upload directory."]);
        exit;
    }
}

// ✅ Load existing metadata from JSON file
$videos = [];
if (file_exists($metadataFile)) {
    $json = file_get_contents($metadataFile);
    $videos = json_decode($json, true) ?? [];
}

// ✅ Handle only POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['videoTitle'] ?? '');
    $description = trim($_POST['videoDescription'] ?? '');
    $videoFile = $_FILES['videoFile'] ?? null;

    // ✅ Validate form fields and uploaded file
    if ($title === '' || $description === '' || !$videoFile || $videoFile['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(["status" => "fail", "message" => "Please fill all fields and upload a valid video."]);
        exit;
    }

    // ✅ Validate supported video types
    $allowedTypes = ['video/mp4', 'video/quicktime', 'video/x-msvideo', 'video/x-matroska'];
    if (!in_array($videoFile['type'], $allowedTypes)) {
        echo json_encode(["status" => "fail", "message" => "Unsupported video format. Use MP4, MOV, AVI, or MKV."]);
        exit;
    }

    // ✅ Generate unique filename and save uploaded video
    $fileName = uniqid('video_') . '_' . basename($videoFile['name']);
    $filePath = $uploadDir . $fileName;

    if (move_uploaded_file($videoFile['tmp_name'], $filePath)) {
        // ✅ Prepare video metadata
        $videoData = [
            'id' => uniqid(),
            'title' => htmlspecialchars($title),
            'description' => htmlspecialchars($description),
            'filename' => $fileName,
        ];

        // ✅ Save metadata to JSON file
        $videos[] = $videoData;
        file_put_contents($metadataFile, json_encode($videos, JSON_PRETTY_PRINT));

        // ✅ Insert video record into database (table: doctor_video)
        $stmt = $mysqli->prepare("INSERT INTO doctor_video (title, description, filename) VALUES (?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("sss", $videoData['title'], $videoData['description'], $videoData['filename']);
            $stmt->execute();
            $stmt->close();
        }

        // ✅ Return success response
        echo json_encode([
            "status" => "success",
            "message" => "Video uploaded successfully!",
            "video" => $videoData
        ]);
    } else {
        // ❌ Failed to save file
        echo json_encode(["status" => "fail", "message" => "Failed to save the uploaded video."]);
    }
} else {
    // ❌ Invalid request method
    echo json_encode(["status" => "fail", "message" => "Invalid request method."]);
}

// ✅ END of PHP script
?>

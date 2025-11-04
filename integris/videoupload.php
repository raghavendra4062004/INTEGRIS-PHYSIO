<?php
header('Content-Type: application/json');

include 'config.php';

// ✅ Connect to DB
$mysqli = new mysqli($host, $user, $pass, $db);
if ($mysqli->connect_errno) {
    echo json_encode(["status" => "fail", "message" => "Connection failed: " . $mysqli->connect_error]);
    exit;
}

// ✅ Query doctor_video table
$query = "SELECT id, title, description, filename, uploaded_at FROM doctor_video ORDER BY id DESC";
$result = $mysqli->query($query);

if (!$result) {
    echo json_encode(["status" => "fail", "message" => "Query error: " . $mysqli->error]);
    exit;
}

$videos = [];
while ($row = $result->fetch_assoc()) {
    $row['video_url'] = "http://14.139.187.229:8081/mca/integris/uploads/" . $row['filename'];
    $videos[] = $row;
}

// ✅ Output JSON
echo json_encode([
    "status" => "success",
    "videos" => $videos
]);

$mysqli->close();
?>

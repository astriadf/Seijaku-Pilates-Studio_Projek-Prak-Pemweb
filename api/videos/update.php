<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: PUT, POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

session_start();
require_once '../../config/database.php';
require_once '../../classes/video.php';

$db = new Database();
$conn = $db->getConnection();

if ($conn === null) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

$video = new Video($conn);

$data = json_decode(file_get_contents('php://input'), true);

if (empty($data['video_id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Video ID is required']);
    exit;
}

try {
    $result = $video->update($data['video_id'], [
        'title' => $data['title'],
        'description' => $data['description'] ?? '',
        'youtube_url' => $data['video_url'] ?? $data['youtube_url'] ?? '',
        'duration_minutes' => $data['duration_minutes'] ?? 0,
        'level' => $data['level'] ?? 'beginner',
        'category' => $data['category'] ?? 'Mat Pilates',
        'is_featured' => $data['is_featured'] ?? 0
    ]);
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Video updated successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to update video']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to update video: ' . $e->getMessage()]);
}

<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: DELETE, POST');
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
$id = $data['video_id'] ?? $_GET['id'] ?? null;

if (empty($id)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Video ID is required']);
    exit;
}

try {
    $result = $video->delete($id);
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Video deleted successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to delete video']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to delete video: ' . $e->getMessage()]);
}

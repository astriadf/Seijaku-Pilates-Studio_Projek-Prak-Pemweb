<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/static_data.php';

$database = new Database();
$db = $database->getConnection();
if ($db !== null) {
    require_once __DIR__ . '/../../classes/video.php';
    $video = new Video($db);

$level = $_GET['level'] ?? null;
    $category = $_GET['category'] ?? null;
    $featured = isset($_GET['featured']) ? true : null;
    $videos = $video->getAll($level, $category, $featured);
} else {
    $videos = StaticData::getVideos();
    
    $level = $_GET['level'] ?? null;
    $category = $_GET['category'] ?? null;
    $featured = isset($_GET['featured']) ? true : null;
    
    if ($level) {
        $videos = array_filter($videos, function($v) use ($level) {
            return $v['level'] === $level || $v['level'] === 'all';
        });
    }
    if ($category) {
        $videos = array_filter($videos, function($v) use ($category) {
            return $v['category'] === $category;
        });
    }
    if ($featured) {
        $videos = array_filter($videos, function($v) {
            return $v['is_featured'] == 1;
        });
    }
    $videos = array_values($videos);
}

echo json_encode([
    'success' => true,
    'data' => $videos
]);

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
    require_once __DIR__ . '/../../classes/review.php';
    $review = new Review($db);
    $featured = isset($_GET['featured']) ? true : null;
    $reviews = $review->getApproved($featured);
} else {
    $reviews = StaticData::getReviews();
    $featured = isset($_GET['featured']) ? true : null;
    if ($featured) {
        $reviews = array_filter($reviews, function($r) {
            return $r['is_featured'] == 1;
        });
        $reviews = array_values($reviews);
    }
}

echo json_encode([
    'success' => true,
    'data' => $reviews
]);

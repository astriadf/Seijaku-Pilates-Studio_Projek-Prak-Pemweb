<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../classes/review.php';
require_once __DIR__ . '/../../utils/jwt.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (empty($data['rating']) || empty($data['review_text'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Rate and review are required']);
    exit;
}

if ($data['rating'] < 1 || $data['rating'] > 5) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Rate must be between 1 and 5']);
    exit;
}

$database = new Database();
$db = $database->getConnection();
$review = new Review($db);

$user = JWT::getAuthUser();
$userId = $user ? $user['user_id'] : null;
$guestName = !$user && !empty($data['guest_name']) ? $data['guest_name'] : null;

if (!$userId && !$guestName) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Name is required for guest reviews']);
    exit;
}

$result = $review->create([
    'user_id' => $userId,
    'guest_name' => $guestName,
    'rating' => $data['rating'],
    'review_text' => $data['review_text']
]);

if ($result) {
    echo json_encode(['success' => true, 'message' => 'Review send successfully']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to send review']);
}

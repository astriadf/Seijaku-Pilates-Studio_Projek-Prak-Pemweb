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
require_once __DIR__ . '/../../classes/booking.php';
require_once __DIR__ . '/../../utils/jwt.php';

$user = JWT::requireAuth(['member']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (empty($data['schedule_id']) || empty($data['date'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Schedule ID and date are required']);
    exit;
}

$database = new Database();
$db = $database->getConnection();
$booking = new Booking($db);

$result = $booking->create($user['user_id'], $data['schedule_id'], $data['date'], $data['notes'] ?? '');

if ($result['success']) {
    echo json_encode($result);
} else {
    http_response_code(400);
    echo json_encode($result);
}

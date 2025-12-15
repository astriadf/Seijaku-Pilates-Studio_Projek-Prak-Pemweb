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
require_once __DIR__ . '/../../classes/notification.php';
require_once __DIR__ . '/../../utils/jwt.php';

$authUser = JWT::requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$database = new Database();
$db = $database->getConnection();
$notification = new Notification($db);

if (!empty($data['notification_id'])) {
    $result = $notification->markAsRead($data['notification_id'], $authUser['user_id']);
} else {
    $result = $notification->markAllAsRead($authUser['user_id']);
}

echo json_encode([
    'success' => $result,
    'message' => $result ? 'Notifications marked as read' : 'Failed to mark notifications as read'
]);

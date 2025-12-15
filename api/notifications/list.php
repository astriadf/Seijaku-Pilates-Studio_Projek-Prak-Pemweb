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
require_once __DIR__ . '/../../classes/notification.php';
require_once __DIR__ . '/../../utils/jwt.php';

$authUser = JWT::requireAuth();

$database = new Database();
$db = $database->getConnection();
$notification = new Notification($db);

$notifications = $notification->getUserNotifications($authUser['user_id']);

echo json_encode([
    'success' => true,
    'data' => $notifications
]);

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
require_once __DIR__ . '/../../classes/booking.php';
require_once __DIR__ . '/../../utils/jwt.php';

$user = JWT::requireAuth(['member']);

$database = new Database();
$db = $database->getConnection();
$booking = new Booking($db);

$status = $_GET['status'] ?? null;
$allBookings = $booking->getUserBookings($user['user_id'], null);
$upcoming = [];
$pending = [];
$history = [];

foreach ($allBookings as $b) {
    $classDate = strtotime($b['class_date']);
    $today = strtotime(date('Y-m-d'));
    
    if ($b['booking_status'] === 'pending') {
        $pending[] = $b;
    } elseif ($b['booking_status'] === 'approved' && $classDate >= $today) {
        $upcoming[] = $b;
    } else {
        $history[] = $b;
    }
}

$result = [];
if ($status === 'approved') {
    $result = $upcoming;
} elseif ($status === 'pending') {
    $result = $pending;
} elseif ($status === 'completed,cancelled') {
    $result = $history;
} else {
    $result = $allBookings;
}


echo json_encode([
    'success' => true,
    'data' => $result
    ]);

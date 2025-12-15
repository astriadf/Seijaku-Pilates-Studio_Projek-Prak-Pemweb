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
require_once __DIR__ . '/../../classes/classSchedule.php';

$date = $_GET['date'] ?? date('Y-m-d');

$dayOfWeek = strtolower(date('l', strtotime($date)));
if (!in_array($dayOfWeek, ['friday', 'saturday', 'sunday'])) {
    echo json_encode([
        'success' => true,
        'data' => [],
        'message' => 'Class only available on Friday, Saturday, and Sunday.'
    ]);
    exit;
}

$database = new Database();
$db = $database->getConnection();
$schedule = new ClassSchedule($db);

$classes = $schedule->getAvailableClasses($date);

echo json_encode([
    'success' => true,
    'date' => $date,
    'day' => $dayOfWeek,
    'data' => $classes
]);

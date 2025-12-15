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
    require_once __DIR__ . '/../../classes/classType.php';
    require_once __DIR__ . '/../../classes/classSchedule.php';
    
    $classType = new ClassType($db);
    $schedule = new ClassSchedule($db);
    $level = $_GET['level'] ?? null;
    $classTypes = $classType->getAll($level);
    $schedules = $schedule->getAll();
} else {
    $level = $_GET['level'] ?? null;
    $classTypes = StaticData::getClassTypes();
    if ($level) {
        $classTypes = array_filter($classTypes, function($c) use ($level) {
            return $c['level'] === $level || $c['level'] === 'all';
        });
        $classTypes = array_values($classTypes);
    }
    $schedules = StaticData::getSchedules();
}

echo json_encode([
    'success' => true,
    'data' => [
        'class_types' => $classTypes,
        'schedules' => $schedules
    ]
]);

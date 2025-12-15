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
    require_once __DIR__ . '/../../classes/instructor.php';
    $instructor = new Instructor($db);
    $instructors = $instructor->getAll();
} else {
    $instructors = StaticData::getInstructors();
}

echo json_encode([
    'success' => true,
    'data' => $instructors
]);

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
require_once __DIR__ . '/../../classes/instructor.php';
require_once __DIR__ . '/../../utils/jwt.php';

$authUser = JWT::requireAuth(['instructor']);

$database = new Database();
$db = $database->getConnection();
$instructor = new Instructor($db);

$instructorData = $instructor->getByUserId($authUser['user_id']);

if (!$instructorData) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Instructor not found']);
    exit;
}

$classes = $instructor->getClasses($instructorData['instructor_id']);

echo json_encode([
    'success' => true,
    'data' => $classes
]);

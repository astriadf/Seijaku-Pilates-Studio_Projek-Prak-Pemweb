<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

session_start();
require_once '../../config/database.php';
require_once '../../classes/classType.php';

$db = new Database();
$conn = $db->getConnection();

if ($conn === null) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

$classType = new ClassType($conn);

$data = json_decode(file_get_contents('php://input'), true);

if (empty($data['name']) || empty($data['difficulty_level'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Name and difficulty level are required']);
    exit;
}

try {
    $id = $classType->create([
        'name' => $data['name'],
        'description' => $data['description'] ?? '',
        'difficulty_level' => $data['difficulty_level'],
        'duration_minutes' => $data['duration_minutes'] ?? 60,
        'icon' => $data['icon'] ?? 'yoga',
        'color' => $data['color'] ?? '#9FBF9E',
        'price' => $data['price'] ?? 75000
    ]);
    
    echo json_encode(['success' => true, 'message' => 'Class type created successfully', 'id' => $id]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to create class type: ' . $e->getMessage()]);
}

<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

require_once '../../config/database.php';
require_once '../../classes/instructor.php';

$db = new Database();
$conn = $db->getConnection();
$instructor = new Instructor($conn);

$id = $_GET['id'] ?? null;

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'ID instructor required']);
    exit;
}

$data = $instructor->getById($id);

if ($data) {
    echo json_encode(['success' => true, 'data' => $data]);
} else {
    echo json_encode(['success' => false, 'message' => 'Instructor not found']);
}

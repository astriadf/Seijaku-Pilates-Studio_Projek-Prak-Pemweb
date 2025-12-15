<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

require_once '../../config/database.php';
require_once '../../classes/instructor.php';

$db = new Database();
$conn = $db->getConnection();
$instructor = new Instructor($conn);

$input = json_decode(file_get_contents('php://input'), true);

$id = $input['instructor_id'] ?? null;

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'ID instructor required']);
    exit;
}

$data = [
    'specialization' => $input['specialization'] ?? '',
    'bio' => $input['bio'] ?? '',
    'experience_years' => $input['experience_years'] ?? 0,
    'certification' => $input['certification'] ?? '',
    'instagram' => $input['instagram'] ?? ''
];

$result = $instructor->update($id, $data);

if ($result) {
    echo json_encode(['success' => true, 'message' => 'Instructor successfully updated']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update instructor']);
}

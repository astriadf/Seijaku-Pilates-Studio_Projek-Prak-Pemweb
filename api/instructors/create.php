<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

require_once '../../config/database.php';
require_once '../../classes/instructor.php';
require_once '../../classes/user.php';

$db = new Database();
$conn = $db->getConnection();
$instructor = new Instructor($conn);
$userModel = new User($conn);

$input = json_decode(file_get_contents('php://input'), true);

$email = $input['email'] ?? null;

if (!$email) {
    echo json_encode(['success' => false, 'message' => 'Email required']);
    exit;
}

$user = $userModel->getByEmail($email);

if (!$user) {
    echo json_encode(['success' => false, 'message' => 'User with this email does not exist']);
    exit;
}

$existingInstructor = $instructor->getByUserId($user['user_id']);
if ($existingInstructor) {
    echo json_encode(['success' => false, 'message' => 'This user is already an instructor']);
    exit;
}

$data = [
    'user_id' => $user['user_id'],
    'specialization' => $input['specialization'] ?? '',
    'bio' => $input['bio'] ?? '',
    'experience_years' => $input['experience_years'] ?? 0,
    'certification' => $input['certification'] ?? '',
    'instagram' => $input['instagram'] ?? ''
];

$result = $instructor->create($data);

if ($result) {
    $updateQuery = "UPDATE users SET role = 'instructor' WHERE user_id = :user_id";
    $stmt = $conn->prepare($updateQuery);
    $stmt->execute([':user_id' => $user['user_id']]);
    
    echo json_encode(['success' => true, 'message' => 'Instruktur successfully added']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to add instructor']);
}

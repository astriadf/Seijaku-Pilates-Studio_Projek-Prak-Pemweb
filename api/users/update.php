<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, PUT, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../classes/user.php';
require_once __DIR__ . '/../../utils/jwt.php';

$authUser = JWT::requireAuth();

if (!in_array($_SERVER['REQUEST_METHOD'], ['POST', 'PUT'])) {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$database = new Database();
$db = $database->getConnection();
$user = new User($db);

if (!empty($data['email']) && $user->emailExists($data['email'], $authUser['user_id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Email already in use']);
    exit;
}

$updateData = [];
if (isset($data['full_name'])) $updateData['full_name'] = $data['full_name'];
if (isset($data['phone'])) $updateData['phone'] = $data['phone'];
if (isset($data['email'])) $updateData['email'] = $data['email'];
if (isset($data['avatar'])) $updateData['avatar'] = $data['avatar'];
if (!empty($data['password'])) $updateData['password'] = $data['password'];

if (empty($updateData)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'No data to update']);
    exit;
}

$result = $user->updateProfile($authUser['user_id'], $updateData);

if ($result) {
    $updatedUser = $user->getById($authUser['user_id']);
    echo json_encode([
        'success' => true,
        'message' => 'Profil successfully updated',
        'data' => $updatedUser
    ]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to update profile']);
}

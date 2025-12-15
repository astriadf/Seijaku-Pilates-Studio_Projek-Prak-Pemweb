<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../classes/user.php';
require_once __DIR__ . '/../../utils/jwt.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (empty($data['email']) || empty($data['password']) || empty($data['full_name'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Email, password, or name are required to fill.']);
    exit;
}

if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Email format invalid']);
    exit;
}

if (strlen($data['password']) < 6) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Minimum password length is 6 characters']);
    exit;
}

$database = new Database();
$db = $database->getConnection();
$user = new User($db);

if ($user->emailExists($data['email'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Email already registered']);
    exit;
}

$userId = $user->create([
    'email' => $data['email'],
    'password' => $data['password'],
    'full_name' => $data['full_name'],
    'phone' => $data['phone'] ?? ''
]);

if ($userId) {
    $userData = $user->getById($userId);
    $token = JWT::encode([
        'user_id' => $userData['user_id'],
        'email' => $userData['email'],
        'role' => $userData['role'],
        'full_name' => $userData['full_name']
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Registrasi success',
        'token' => $token,
        'user' => $userData
    ]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Registration failed' ]);
}

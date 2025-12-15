<?php
session_start();
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

if (empty($data['email']) || empty($data['password'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Email and password are required to fill.']);
    exit;
}

$database = new Database();
$db = $database->getConnection();
$user = new User($db);

$userData = $user->login($data['email'], $data['password']);

if ($userData) {
    $_SESSION['user_id'] = $userData['user_id'];
    $_SESSION['email'] = $userData['email'];
    $_SESSION['role'] = $userData['role'];
    $_SESSION['full_name'] = $userData['full_name'];

    $token = JWT::encode([
        'user_id' => $userData['user_id'],
        'email' => $userData['email'],
        'role' => $userData['role'],
        'full_name' => $userData['full_name']
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Login success',
        'token' => $token,
        'user' => [
            'user_id' => $userData['user_id'],
            'email' => $userData['email'],
            'full_name' => $userData['full_name'],
            'role' => $userData['role'],
            'avatar' => $userData['avatar']
        ]
    ]);
} else {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Email or password are wrong']);
}

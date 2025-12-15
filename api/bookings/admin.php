<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../classes/booking.php';
require_once __DIR__ . '/../../utils/jwt.php';

$user = JWT::requireAuth(['admin']);

$database = new Database();
$db = $database->getConnection();
$booking = new Booking($db);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $status = $_GET['status'] ?? 'pending';

    // validasi sederhana
    $allowed = ['pending', 'approved', 'completed', 'cancelled', 'rejected'];
    if (!in_array($status, $allowed)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Status tidak valid']);
        exit;
    }

    // ambil list booking per status
    $query = "SELECT b.booking_id,
                     b.booking_status AS status,
                     ci.class_date AS schedule_date,
                     ci.start_time,
                     ci.end_time,
                     ct.name AS class_name,
                     u.full_name AS member_name,
                     ins_u.full_name AS instructor_name
              FROM bookings b
              JOIN class_instances ci ON b.instance_id = ci.instance_id
              JOIN class_schedules cs ON ci.schedule_id = cs.schedule_id
              JOIN class_types ct ON cs.class_type_id = ct.class_type_id
              JOIN users u ON b.user_id = u.user_id
              JOIN instructors i ON cs.instructor_id = i.instructor_id
              JOIN users ins_u ON i.user_id = ins_u.user_id
              WHERE b.booking_status = :status
              ORDER BY ci.class_date DESC, ci.start_time DESC";

    $stmt = $db->prepare($query);
    $stmt->execute([':status' => $status]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'data' => $rows]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (empty($data['booking_id']) || empty($data['action'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Booking ID and action are required']);
        exit;
    }

    if ($data['action'] === 'approve') {
        $result = $booking->approve($data['booking_id'], $user['user_id']);
    } elseif ($data['action'] === 'reject') {
        $result = $booking->reject($data['booking_id'], $data['reason'] ?? '');
    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Action invalid']);
        exit;
    }

    echo json_encode($result);
}

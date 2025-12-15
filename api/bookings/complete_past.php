<?php
require_once '../../config/database.php';
require_once '../../classes/booking.php';

header('Content-Type: application/json');

try {
    $db   = new Database();
    $conn = $db->getConnection();
    $booking = new Booking($conn);

    // tandai semua booking approved yang tanggal+jam sudah lewat sebagai completed
    $stmt = $conn->prepare("
        UPDATE bookings
        SET booking_status = 'completed'
        WHERE booking_status = 'approved'
          AND CONCAT(class_date, ' ', end_time) <= NOW()
    ");
    $stmt->execute();
    $updated = $stmt->rowCount();

    echo json_encode([
        'success' => true,
        'updated' => $updated,
        'message' => 'Booking yang sudah lewat ditandai selesai.'
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}

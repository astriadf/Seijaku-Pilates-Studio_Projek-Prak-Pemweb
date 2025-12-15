<?php
class Booking {
    private $conn;
    private $table = 'bookings';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create($userId, $scheduleId, $date, $notes = '') {
        $instance = $this->getOrCreateInstance($scheduleId, $date);
        if (!$instance) {
            return ['success' => false, 'message' => 'Gagal membuat instance kelas'];
        }

        if ($instance['current_bookings'] >= $instance['capacity']) {
            return ['success' => false, 'message' => 'Kelas sudah penuh'];
        }

        $existing = $this->checkExisting($userId, $instance['instance_id']);
        if ($existing) {
            return ['success' => false, 'message' => 'Anda sudah booking kelas ini'];
        }

        $query = "INSERT INTO {$this->table} (user_id, instance_id, booking_status, notes)
                  VALUES (:user_id, :instance_id, 'pending', :notes)";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([
            ':user_id' => $userId,
            ':instance_id' => $instance['instance_id'],
            ':notes' => $notes
        ]);

        $this->updateInstanceCount($instance['instance_id'], 1);
        return ['success' => true, 'booking_id' => $this->conn->lastInsertId()];
        }
        private function countActiveBookings($instanceId) {
            $query = "SELECT COUNT(*) as total FROM {$this->table} 
                    WHERE instance_id = :instance_id AND booking_status IN ('pending', 'approved')";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([':instance_id' => $instanceId]);
            return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        }

        private function getOrCreateInstance($scheduleId, $date) {
            $query = "SELECT ci.*, cs.capacity as schedule_capacity, cs.start_time, cs.end_time
                    FROM class_instances ci
                    JOIN class_schedules cs ON ci.schedule_id = cs.schedule_id
                    WHERE ci.schedule_id = :schedule_id AND ci.class_date = :date";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([':schedule_id' => $scheduleId, ':date' => $date]);
            $instance = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($instance) return $instance;

        $scheduleQuery = "SELECT * FROM class_schedules WHERE schedule_id = :id";
        $scheduleStmt = $this->conn->prepare($scheduleQuery);
        $scheduleStmt->bindParam(':id', $scheduleId);
        $scheduleStmt->execute();
        $schedule = $scheduleStmt->fetch(PDO::FETCH_ASSOC);

        if (!$schedule) return null;

        $insertQuery = "INSERT INTO class_instances (schedule_id, class_date, start_time, end_time, capacity)
                        VALUES (:schedule_id, :date, :start, :end, :capacity)";
        $insertStmt = $this->conn->prepare($insertQuery);
        $insertStmt->execute([
            ':schedule_id' => $scheduleId,
            ':date' => $date,
            ':start' => $schedule['start_time'],
            ':end' => $schedule['end_time'],
            ':capacity' => $schedule['capacity']
        ]);

        return [
            'instance_id' => $this->conn->lastInsertId(),
            'capacity' => $schedule['capacity'],
            'current_bookings' => 0
        ];
    }

    private function checkExisting($userId, $instanceId) {
        $query = "SELECT booking_id FROM {$this->table} 
                  WHERE user_id = :user_id AND instance_id = :instance_id 
                  AND booking_status NOT IN ('cancelled', 'rejected')";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':user_id' => $userId, ':instance_id' => $instanceId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getUserBookings($userId, $status = null) {
        $query = "SELECT b.*, ci.class_date, ci.start_time, ci.end_time,
                         ct.name as class_name, ct.icon, ct.color, cs.level,
                         u.full_name as instructor_name
                  FROM {$this->table} b
                  JOIN class_instances ci ON b.instance_id = ci.instance_id
                  JOIN class_schedules cs ON ci.schedule_id = cs.schedule_id
                  JOIN class_types ct ON cs.class_type_id = ct.class_type_id
                  JOIN instructors i ON cs.instructor_id = i.instructor_id
                  JOIN users u ON i.user_id = u.user_id
                  WHERE b.user_id = :user_id";
        
        if ($status) {
            $query .= " AND b.booking_status = :status";
        }
        $query .= " ORDER BY ci.class_date DESC, ci.start_time DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        if ($status) {
            $stmt->bindParam(':status', $status);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllPending() {
        $query = "SELECT b.*, ci.class_date, ci.start_time, ci.end_time,
                         ct.name as class_name, ct.icon, cs.level,
                         u.full_name as member_name, u.email as member_email,
                         ins_u.full_name as instructor_name
                  FROM {$this->table} b
                  JOIN class_instances ci ON b.instance_id = ci.instance_id
                  JOIN class_schedules cs ON ci.schedule_id = cs.schedule_id
                  JOIN class_types ct ON cs.class_type_id = ct.class_type_id
                  JOIN instructors i ON cs.instructor_id = i.instructor_id
                  JOIN users ins_u ON i.user_id = ins_u.user_id
                  JOIN users u ON b.user_id = u.user_id
                  WHERE b.booking_status = 'pending'
                  ORDER BY b.booking_date ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function approve($bookingId, $adminId) {
        $query = "UPDATE {$this->table} SET booking_status = 'approved', approved_by = :admin_id, approved_at = NOW()
                  WHERE booking_id = :booking_id AND booking_status = 'pending'";
        $stmt = $this->conn->prepare($query);
        $result = $stmt->execute([':admin_id' => $adminId, ':booking_id' => $bookingId]);
        
        if ($result && $stmt->rowCount() > 0) {
            $booking = $this->getById($bookingId);
            $this->createNotification($booking['user_id'], 'booking_approved', 'Booking Disetujui', 'Booking kelas Anda telah disetujui.');
            $this->checkAndAwardBadges($booking['user_id']);
            return ['success' => true];
        }
        return ['success' => false, 'message' => 'Failed to approve booking'];
    }

    public function reject($bookingId, $reason = '') {
        $booking = $this->getById($bookingId);
        if (!$booking) {
            return ['success' => false, 'message' => 'Booking not found'];
        }
        $query = "UPDATE {$this->table} SET booking_status = 'rejected', cancellation_reason = :reason
                  WHERE booking_id = :booking_id AND booking_status = 'pending'";
        $stmt = $this->conn->prepare($query);
        $result = $stmt->execute([':reason' => $reason, ':booking_id' => $bookingId]);
        
        if ($result && $stmt->rowCount() > 0) {
            $this->updateInstanceCount($booking['instance_id'], -1);
            $this->createNotification($booking['user_id'], 'booking_rejected', 'Booking rejected', 'Sorry, your booking are rejected. ' . $reason);
            return ['success' => true];
        }
        return ['success' => false, 'message' => 'Failed to reject booking'];
    }

    public function cancel($bookingId, $userId) {
        $booking = $this->getById($bookingId);
        if (!$booking || $booking['user_id'] != $userId) {
            return ['success' => false, 'message' => 'Booking not found'];
        }
        $wasApproved = ($booking['booking_status'] == 'approved');
        $wasPending = ($booking['booking_status'] == 'pending');
        
        if (!$wasApproved && !$wasPending) {
            return ['success' => false, 'message' => 'Cannot cancel this booking'];
        }
        $query = "UPDATE {$this->table} SET booking_status = 'cancelled', cancelled_at = NOW()
                  WHERE booking_id = :booking_id AND user_id = :user_id AND booking_status IN ('pending', 'approved')";
        $stmt = $this->conn->prepare($query);
        $result = $stmt->execute([':booking_id' => $bookingId, ':user_id' => $userId]);
        
        if ($result && $stmt->rowCount() > 0) {
            $this->updateInstanceCount($booking['instance_id'], -1);
            return ['success' => true];
        }
        return ['success' => false, 'message' => 'Failed to cancel booking'];
    }

    public function complete($bookingId) {
        $booking = $this->getById($bookingId);
        $query = "UPDATE {$this->table} SET booking_status = 'completed', attended = TRUE WHERE booking_id = :id";
        $stmt = $this->conn->prepare($query);
        $result = $stmt->execute([':id' => $bookingId]);
        
        if ($result) {
            $userQuery = "UPDATE users SET total_classes_attended = total_classes_attended + 1 WHERE user_id = :user_id";
            $userStmt = $this->conn->prepare($userQuery);
            $userStmt->execute([':user_id' => $booking['user_id']]);
            $this->checkAndAwardBadges($booking['user_id']);
        }
        return $result;
    }

    private function checkAndAwardBadges($userId) {
        $countQuery = "SELECT COUNT(*) as total FROM {$this->table} WHERE user_id = :user_id AND booking_status IN ('approved', 'completed')";        $countStmt = $this->conn->prepare($countQuery);
        $countStmt->execute([':user_id' => $userId]);
        $count = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];

        $achievementsQuery = "SELECT * FROM achievements WHERE required_classes <= :count ORDER BY required_classes";
        $achievementsStmt = $this->conn->prepare($achievementsQuery);
        $achievementsStmt->execute([':count' => $count]);
        $achievements = $achievementsStmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($achievements as $achievement) {
            $checkQuery = "SELECT id FROM user_achievements WHERE user_id = :user_id AND achievement_id = :achievement_id";
            $checkStmt = $this->conn->prepare($checkQuery);
            $checkStmt->execute([':user_id' => $userId, ':achievement_id' => $achievement['achievement_id']]);
            
            if (!$checkStmt->fetch()) {
                $insertQuery = "INSERT INTO user_achievements (user_id, achievement_id) VALUES (:user_id, :achievement_id)";
                $insertStmt = $this->conn->prepare($insertQuery);
                $insertStmt->execute([':user_id' => $userId, ':achievement_id' => $achievement['achievement_id']]);
                
                $this->createNotification($userId, 'achievement_earned', 'New Badges!', 
                    'Selamat! Anda mendapatkan badge "' . $achievement['name'] . '"');
            }
        }
    }

    private function updateInstanceCount($instanceId, $change) {
        $query = "UPDATE class_instances SET current_bookings = current_bookings + :change WHERE instance_id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([':change' => $change, ':id' => $instanceId]);
    }

    private function createNotification($userId, $type, $title, $message) {
        $query = "INSERT INTO notifications (user_id, type, title, message) VALUES (:user_id, :type, :title, :message)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([':user_id' => $userId, ':type' => $type, ':title' => $title, ':message' => $message]);
    }

    public function getById($id) {
        $query = "SELECT * FROM {$this->table} WHERE booking_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getTotalCount() {
        $query = "SELECT COUNT(*) as total FROM {$this->table}";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function getPendingCount() {
        $query = "SELECT COUNT(*) as total FROM {$this->table} WHERE booking_status = 'pending'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
}

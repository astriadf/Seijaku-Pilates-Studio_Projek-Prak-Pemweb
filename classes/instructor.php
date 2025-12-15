<?php
class Instructor {
    private $conn;
    private $table = 'instructors';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $query = "SELECT i.*, u.full_name, u.email, u.phone, u.avatar
                  FROM {$this->table} i
                  JOIN users u ON i.user_id = u.user_id
                  WHERE i.is_active = TRUE
                  ORDER BY i.rating DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $query = "SELECT i.*, u.full_name, u.email, u.phone, u.avatar
                  FROM {$this->table} i
                  JOIN users u ON i.user_id = u.user_id
                  WHERE i.instructor_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByUserId($userId) {
        $query = "SELECT i.*, u.full_name, u.email, u.phone, u.avatar
                  FROM {$this->table} i
                  JOIN users u ON i.user_id = u.user_id
                  WHERE i.user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    // Mengambil jadwal kelas instruktur dengan informasi booking
    public function getClasses($instructorId) {
        $query = "SELECT cs.*, ct.name as class_name, ct.difficulty_level, ct.icon,
                        ci.class_date as schedule_date, ci.current_bookings, ci.capacity as max_capacity
                FROM class_schedules cs
                JOIN class_types ct ON cs.class_type_id = ct.class_type_id
                LEFT JOIN class_instances ci ON cs.schedule_id = ci.schedule_id AND ci.class_date >= CURDATE()
                WHERE cs.instructor_id = :id AND cs.is_active = TRUE
                ORDER BY ci.class_date ASC, cs.start_time";        
                    $stmt = $this->conn->prepare($query);
                    $stmt->bindParam(':id', $instructorId);
                    $stmt->execute();
             return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getStudents($instructorId, $date = null) {
        $query = "SELECT b.*, u.full_name, u.email, u.phone, u.avatar,
                         ci.class_date, ci.start_time, ci.end_time,
                         ct.name as class_name
                  FROM bookings b
                  JOIN users u ON b.user_id = u.user_id
                  JOIN class_instances ci ON b.instance_id = ci.instance_id
                  JOIN class_schedules cs ON ci.schedule_id = cs.schedule_id
                  JOIN class_types ct ON cs.class_type_id = ct.class_type_id
                  WHERE cs.instructor_id = :id AND b.booking_status IN ('approved', 'completed')";
        
        if ($date) {
            $query .= " AND ci.class_date = :date";
        }
        $query .= " ORDER BY ci.class_date DESC, ci.start_time";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $instructorId);
        if ($date) {
            $stmt->bindParam(':date', $date);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $query = "INSERT INTO {$this->table} (user_id, specialization, bio, experience_years, certification, instagram)
                  VALUES (:user_id, :spec, :bio, :exp, :cert, :ig)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':user_id' => $data['user_id'],
            ':spec' => $data['specialization'],
            ':bio' => $data['bio'],
            ':exp' => $data['experience_years'] ?? 0,
            ':cert' => $data['certification'] ?? '',
            ':ig' => $data['instagram'] ?? ''
        ]);
    }

    public function update($id, $data) {
        $query = "UPDATE {$this->table} SET 
                    specialization = :spec, bio = :bio, experience_years = :exp,
                    certification = :cert, instagram = :ig
                  WHERE instructor_id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':id' => $id,
            ':spec' => $data['specialization'],
            ':bio' => $data['bio'],
            ':exp' => $data['experience_years'],
            ':cert' => $data['certification'],
            ':ig' => $data['instagram']
        ]);
    }

    public function getTotalCount() {
        $query = "SELECT COUNT(*) as total FROM {$this->table} WHERE is_active = TRUE";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
}

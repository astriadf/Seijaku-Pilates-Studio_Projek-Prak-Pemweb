<?php
class ClassSchedule {
    private $conn;
    private $table = 'class_schedules';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $query = "SELECT cs.*, ct.name as class_name, ct.icon, ct.color, ct.price,
                         u.full_name as instructor_name
                  FROM {$this->table} cs
                  JOIN class_types ct ON cs.class_type_id = ct.class_type_id
                  JOIN instructors i ON cs.instructor_id = i.instructor_id
                  JOIN users u ON i.user_id = u.user_id
                  WHERE cs.is_active = TRUE
                  ORDER BY FIELD(cs.day_of_week, 'friday', 'saturday', 'sunday'), cs.start_time";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByDay($day) {
        $query = "SELECT cs.*, ct.name as class_name, ct.icon, ct.color, ct.price, ct.description,
                         u.full_name as instructor_name, i.instructor_id
                  FROM {$this->table} cs
                  JOIN class_types ct ON cs.class_type_id = ct.class_type_id
                  JOIN instructors i ON cs.instructor_id = i.instructor_id
                  JOIN users u ON i.user_id = u.user_id
                  WHERE cs.day_of_week = :day AND cs.is_active = TRUE
                  ORDER BY cs.start_time";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':day', $day);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAvailableClasses($date) {
        $dayOfWeek = strtolower(date('l', strtotime($date)));
        
        if (!in_array($dayOfWeek, ['friday', 'saturday', 'sunday'])) {
            return [];
        }

        $query = "SELECT cs.*, ct.name as class_name, ct.icon, ct.color, ct.price, ct.description,
                         u.full_name as instructor_name, i.instructor_id,
                         COALESCE(ci.instance_id, 0) as instance_id,
                         COALESCE(ci.current_bookings, 0) as current_bookings,
                         COALESCE(ci.status, 'scheduled') as instance_status,
                         (cs.capacity - COALESCE(ci.current_bookings, 0)) as available_spots                  
                  FROM {$this->table} cs
                  JOIN class_types ct ON cs.class_type_id = ct.class_type_id
                  JOIN instructors i ON cs.instructor_id = i.instructor_id
                  JOIN users u ON i.user_id = u.user_id
                  LEFT JOIN class_instances ci ON cs.schedule_id = ci.schedule_id AND ci.class_date = :date
                  WHERE cs.day_of_week = :day AND cs.is_active = TRUE
                  ORDER BY cs.start_time";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':day', $dayOfWeek);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $query = "INSERT INTO {$this->table} (class_type_id, instructor_id, day_of_week, start_time, end_time, capacity, level)
                  VALUES (:class_type_id, :instructor_id, :day, :start, :end, :capacity, :level)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':class_type_id' => $data['class_type_id'],
            ':instructor_id' => $data['instructor_id'],
            ':day' => $data['day_of_week'],
            ':start' => $data['start_time'],
            ':end' => $data['end_time'],
            ':capacity' => $data['capacity'] ?? 10,
            ':level' => $data['level']
        ]);
    }

    public function delete($id) {
        $query = "UPDATE {$this->table} SET is_active = FALSE WHERE schedule_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}

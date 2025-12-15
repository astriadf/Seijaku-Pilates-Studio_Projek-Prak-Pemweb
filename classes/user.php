<?php
class User {
    private $conn;
    private $table = 'users';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create($data) {
        $query = "INSERT INTO {$this->table} (email, password_hash, full_name, phone, role, status, avatar)
                  VALUES (:email, :password, :full_name, :phone, :role, :status, :avatar)";
        
        $stmt = $this->conn->prepare($query);
        $password_hash = password_hash($data['password'], PASSWORD_BCRYPT);
        
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':password', $password_hash);
        $stmt->bindParam(':full_name', $data['full_name']);
        $stmt->bindParam(':phone', $data['phone']);
        $role = $data['role'] ?? 'member';
        $status = 'active';
        $avatar = 'totoro';
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':avatar', $avatar);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    public function login($email, $password) {
        $query = "SELECT * FROM {$this->table} WHERE email = :email AND status = 'active'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && password_verify($password, $user['password_hash'])) {
            $this->updateLastLogin($user['user_id']);
            return $user;
        }
        return false;
    }

    public function emailExists($email, $excludeId = null) {
        $query = "SELECT user_id FROM {$this->table} WHERE email = :email";
        if ($excludeId) {
            $query .= " AND user_id != :exclude_id";
        }
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        if ($excludeId) {
            $stmt->bindParam(':exclude_id', $excludeId);
        }
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function getById($id) {
        $query = "SELECT user_id, email, full_name, phone, role, status, avatar, 
                         total_bookings, total_classes_attended, created_at, last_login 
                  FROM {$this->table} WHERE user_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function getByEmail($email) {
        $query = "SELECT user_id, email, full_name, phone, role, status, avatar, 
                         total_bookings, total_classes_attended, created_at, last_login 
                  FROM {$this->table} WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateLastLogin($id) {
        $query = "UPDATE {$this->table} SET last_login = CURRENT_TIMESTAMP WHERE user_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function updateProfile($id, $data) {
        $fields = [];
        $params = [':id' => $id];

        if (isset($data['full_name'])) {
            $fields[] = 'full_name = :full_name';
            $params[':full_name'] = $data['full_name'];
        }
        if (isset($data['phone'])) {
            $fields[] = 'phone = :phone';
            $params[':phone'] = $data['phone'];
        }
        if (isset($data['email'])) {
            $fields[] = 'email = :email';
            $params[':email'] = $data['email'];
        }
        if (isset($data['avatar'])) {
            $fields[] = 'avatar = :avatar';
            $params[':avatar'] = $data['avatar'];
        }
        if (isset($data['password']) && !empty($data['password'])) {
            $fields[] = 'password_hash = :password';
            $params[':password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        }

        if (empty($fields)) return false;

        $query = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE user_id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute($params);
    }

    public function getAllMembers() {
        $query = "SELECT user_id, email, full_name, phone, status, avatar,
                         total_bookings, total_classes_attended, created_at, last_login
                  FROM {$this->table} WHERE role = 'member' ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

        public function getStats($id) {
        $query = "SELECT 
                    (SELECT COUNT(*) FROM bookings WHERE user_id = :id1) AS total_bookings,
                    (SELECT COUNT(*) FROM bookings WHERE user_id = :id2 AND booking_status = 'pending') AS pending_bookings,
                    (SELECT COUNT(*) FROM bookings b
                     JOIN class_instances ci ON b.instance_id = ci.instance_id
                     WHERE b.user_id = :id3 AND b.booking_status IN ('approved', 'completed') AND ci.class_date < CURDATE()) AS total_classes,
                    (SELECT COUNT(*) FROM bookings b
                     JOIN class_instances ci ON b.instance_id = ci.instance_id
                     WHERE b.user_id = :id4 AND b.booking_status = 'approved' AND ci.class_date >= CURDATE()) AS upcoming_bookings,
                    (SELECT COUNT(*) FROM user_achievements WHERE user_id = :id5) AS badges_earned";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id1', $id);
        $stmt->bindParam(':id2', $id);
        $stmt->bindParam(':id3', $id);
        $stmt->bindParam(':id4', $id);
        $stmt->bindParam(':id5', $id);
        $stmt->execute();
        $stats = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats['current_streak'] = $this->calculateStreak($id);
        return $stats;
    }
    private function calculateStreak($userId) {
        $query = "SELECT DISTINCT YEARWEEK(ci.class_date) as week_num
                  FROM bookings b
                  JOIN class_instances ci ON b.instance_id = ci.instance_id
                  WHERE b.user_id = :user_id AND b.booking_status IN ('approved', 'completed')
                  ORDER BY week_num DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        $weeks = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        if (empty($weeks)) return 0;
        
        $currentWeek = date('oW');
        $streak = 0;
        $expectedWeek = $currentWeek;
        
        foreach ($weeks as $week) {
            if ($week == $expectedWeek || $week == $expectedWeek - 1) {
                $streak++;
                $expectedWeek = $week - 1;
            } else {
                break;
            }
        }
        
        return $streak;
    }

    public function incrementClassesAttended($id) {
        $query = "UPDATE {$this->table} SET total_classes_attended = total_classes_attended + 1 WHERE user_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function getTotalCount() {
        $query = "SELECT COUNT(*) as total FROM {$this->table} WHERE role = 'member'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
}

<?php
class Achievement {
    private $conn;
    private $table = 'achievements';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $query = "SELECT * FROM {$this->table} ORDER BY sort_order, required_classes";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserAchievements($userId) {
        $query = "SELECT a.*, ua.earned_at,
                         CASE WHEN ua.id IS NOT NULL THEN 1 ELSE 0 END as earned
                  FROM {$this->table} a
                  LEFT JOIN user_achievements ua ON a.achievement_id = ua.achievement_id AND ua.user_id = :user_id
                  ORDER BY a.sort_order, a.required_classes";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getEarnedCount($userId) {
        $query = "SELECT COUNT(*) as total FROM user_achievements WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function getLatestEarned($userId) {
        $query = "SELECT a.*, ua.earned_at
                  FROM user_achievements ua
                  JOIN {$this->table} a ON ua.achievement_id = a.achievement_id
                  WHERE ua.user_id = :user_id
                  ORDER BY ua.earned_at DESC
                  LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

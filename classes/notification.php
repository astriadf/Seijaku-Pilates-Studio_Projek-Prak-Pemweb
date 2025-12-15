<?php
class Notification {
    private $conn;
    private $table = 'notifications';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getUserNotifications($userId, $unreadOnly = false) {
        $query = "SELECT * FROM {$this->table} WHERE user_id = :user_id";
        if ($unreadOnly) {
            $query .= " AND is_read = FALSE";
        }
        $query .= " ORDER BY created_at DESC LIMIT 20";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUnreadCount($userId) {
        $query = "SELECT COUNT(*) as total FROM {$this->table} WHERE user_id = :user_id AND is_read = FALSE";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function markAsRead($notificationId, $userId) {
        $query = "UPDATE {$this->table} SET is_read = TRUE WHERE notification_id = :id AND user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([':id' => $notificationId, ':user_id' => $userId]);
    }

    public function markAllAsRead($userId) {
        $query = "UPDATE {$this->table} SET is_read = TRUE WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([':user_id' => $userId]);
    }

    public function create($userId, $type, $title, $message) {
        $query = "INSERT INTO {$this->table} (user_id, type, title, message) VALUES (:user_id, :type, :title, :message)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([':user_id' => $userId, ':type' => $type, ':title' => $title, ':message' => $message]);
    }
}

<?php
class Message {
    private $conn;
    private $table = 'contact_messages';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll($unreadOnly = false) {
        $query = "SELECT * FROM {$this->table}";
        if ($unreadOnly) {
            $query .= " WHERE is_read = FALSE";
        }
        $query .= " ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $query = "INSERT INTO {$this->table} (name, email, message) VALUES (:name, :email, :message)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([':name' => $data['name'], ':email' => $data['email'], ':message' => $data['message']]);
    }

    public function markAsRead($id) {
        $query = "UPDATE {$this->table} SET is_read = TRUE WHERE message_id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([':id' => $id]);
    }

    public function getUnreadCount() {
        $query = "SELECT COUNT(*) as total FROM {$this->table} WHERE is_read = FALSE";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
}

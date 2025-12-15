<?php
class Review {
    private $conn;
    private $table = 'reviews';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll($limit = null) {
        $query = "SELECT r.*, COALESCE(u.full_name, r.guest_name) as full_name, u.avatar
                  FROM {$this->table} r
                  LEFT JOIN users u ON r.user_id = u.user_id
                  WHERE r.is_approved = TRUE
                  ORDER BY r.is_featured DESC, r.created_at DESC";
        if ($limit) {
            $query .= " LIMIT :limit";
        }
        $stmt = $this->conn->prepare($query);
        if ($limit) {
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $query = "INSERT INTO {$this->table} (user_id, guest_name, rating, review_text, is_approved)
                  VALUES (:user_id, :guest_name, :rating, :review_text, TRUE)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':user_id' => $data['user_id'] ?? null,
            ':guest_name' => $data['guest_name'] ?? null,
            ':rating' => $data['rating'],
            ':review_text' => $data['review_text']
        ]);
    }

    public function getFeatured() {
        $query = "SELECT r.*, COALESCE(u.full_name, r.guest_name) as full_name, u.avatar
                  FROM {$this->table} r
                  LEFT JOIN users u ON r.user_id = u.user_id
                  WHERE r.is_approved = TRUE AND r.is_featured = TRUE
                  ORDER BY r.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getApproved($featured = null) {
        $query = "SELECT r.*, COALESCE(u.full_name, r.guest_name) as full_name, u.avatar
                  FROM {$this->table} r
                  LEFT JOIN users u ON r.user_id = u.user_id
                  WHERE r.is_approved = TRUE";
        if ($featured) {
            $query .= " AND r.is_featured = TRUE";
        }
        $query .= " ORDER BY r.is_featured DESC, r.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

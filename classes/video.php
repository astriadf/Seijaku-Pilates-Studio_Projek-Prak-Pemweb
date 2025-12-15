<?php
class Video {
    private $conn;
    private $table = 'videos';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll($level = null, $category = null, $featured = null) {
        $query = "SELECT * FROM {$this->table} WHERE is_active = TRUE";
        $params = [];
        
        if ($level) {
            $query .= " AND level = :level";
            $params[':level'] = $level;
        }
        if ($category) {
            $query .= " AND category = :category";
            $params[':category'] = $category;
        }
        if ($featured) {
            $query .= " AND is_featured = TRUE";
        }
        
        $query .= " ORDER BY is_featured DESC, view_count DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $query = "SELECT * FROM {$this->table} WHERE video_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getFeatured($limit = 6) {
        $query = "SELECT * FROM {$this->table} WHERE is_active = TRUE ORDER BY is_featured DESC, view_count DESC LIMIT :limit";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function incrementViews($id) {
        $query = "UPDATE {$this->table} SET view_count = view_count + 1 WHERE video_id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([':id' => $id]);
    }

    public function create($data) {
        $query = "INSERT INTO {$this->table} (title, description, youtube_url, duration_minutes, level, category, is_featured, created_by)
                  VALUES (:title, :desc, :url, :duration, :level, :category, :featured, :created_by)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':title' => $data['title'],
            ':desc' => $data['description'],
            ':url' => $data['youtube_url'],
            ':duration' => $data['duration_minutes'],
            ':level' => $data['level'],
            ':category' => $data['category'],
            ':featured' => $data['is_featured'] ?? 0,
            ':created_by' => $data['created_by']
        ]);
    }

    public function update($id, $data) {
        $query = "UPDATE {$this->table} SET title = :title, description = :desc, youtube_url = :url,
                  duration_minutes = :duration, level = :level, category = :category, is_featured = :featured
                  WHERE video_id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':id' => $id,
            ':title' => $data['title'],
            ':desc' => $data['description'],
            ':url' => $data['youtube_url'],
            ':duration' => $data['duration_minutes'],
            ':level' => $data['level'],
            ':category' => $data['category'],
            ':featured' => $data['is_featured'] ?? 0
        ]);
    }

    public function delete($id) {
        $query = "UPDATE {$this->table} SET is_active = FALSE WHERE video_id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([':id' => $id]);
    }

    public function getCategories() {
        $query = "SELECT DISTINCT category FROM {$this->table} WHERE is_active = TRUE ORDER BY category";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}

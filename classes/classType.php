<?php
class ClassType {
    private $conn;
    private $table = 'class_types';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll($level = null) {
        $query = "SELECT * FROM {$this->table} WHERE is_active = TRUE";
        if ($level) {
            $query .= " AND difficulty_level = :level";
        }
        $query .= " ORDER BY difficulty_level, name";
        
        $stmt = $this->conn->prepare($query);
        if ($level) {
            $stmt->bindParam(':level', $level);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $query = "SELECT * FROM {$this->table} WHERE class_type_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $query = "INSERT INTO {$this->table} (name, slug, description, difficulty_level, duration_minutes, icon, color, price)
                  VALUES (:name, :slug, :description, :level, :duration, :icon, :color, :price)";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([
            ':name' => $data['name'],
            ':slug' => $this->createSlug($data['name']),
            ':description' => $data['description'],
            ':level' => $data['difficulty_level'],
            ':duration' => $data['duration_minutes'] ?? 60,
            ':icon' => $data['icon'] ?? 'yoga',
            ':color' => $data['color'] ?? '#9FBF9E',
            ':price' => $data['price'] ?? 75000
        ]);
        return $this->conn->lastInsertId();
    }

    public function update($id, $data) {
        $query = "UPDATE {$this->table} SET 
                    name = :name, description = :description, difficulty_level = :level,
                    duration_minutes = :duration, icon = :icon, color = :color, price = :price
                  WHERE class_type_id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':id' => $id,
            ':name' => $data['name'],
            ':description' => $data['description'],
            ':level' => $data['difficulty_level'],
            ':duration' => $data['duration_minutes'],
            ':icon' => $data['icon'],
            ':color' => $data['color'],
            ':price' => $data['price']
        ]);
    }

    public function delete($id) {
        $query = "UPDATE {$this->table} SET is_active = FALSE WHERE class_type_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    private function createSlug($name) {
        return strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $name));
    }

    public function getTotalCount() {
        $query = "SELECT COUNT(*) as total FROM {$this->table} WHERE is_active = TRUE";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
}

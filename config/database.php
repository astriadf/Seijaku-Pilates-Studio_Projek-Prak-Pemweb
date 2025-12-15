<?php
class Database {
    private $host = '127.0.0.1';
    private $db_name = 'seijaku_pilates';
    private $username = 'root';
    private $password = '';
    private $port = '3306';
    public $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->db_name};charset=utf8mb4";

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::ATTR_TIMEOUT            => 2,
            ];

            $this->conn = new PDO($dsn, $this->username, $this->password, $options);

        } catch (PDOException $exception) {
            return null;
        }

        return $this->conn;
    }

    public function testConnection() {
        try {
           $conn = $this->getConnection();
            return $conn !== null;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function isAvailable() {
        $db = new self();
        return $db->getConnection() !== null;
    }
}

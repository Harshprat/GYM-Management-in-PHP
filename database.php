<?php
class Database {
    private $host = "localhost";
    private $db_name = "fitness_center";
    private $username = "root";
    private $password = "";
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password,
                array(
                    PDO::ATTR_PERSISTENT => true,
                    PDO::ATTR_TIMEOUT => 5,
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
                    PDO::ATTR_EMULATE_PREPARES => false
                )
            );
            
            // Set session wait_timeout
            $this->conn->exec("SET SESSION wait_timeout=28800");
            $this->conn->exec("SET SESSION interactive_timeout=28800");
            
        } catch(PDOException $e) {
            error_log("Connection Error: " . $e->getMessage());
            // Try to reconnect once
            try {
                $this->conn = new PDO(
                    "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                    $this->username,
                    $this->password,
                    array(
                        PDO::ATTR_PERSISTENT => true,
                        PDO::ATTR_TIMEOUT => 5,
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
                        PDO::ATTR_EMULATE_PREPARES => false
                    )
                );
                $this->conn->exec("SET SESSION wait_timeout=28800");
                $this->conn->exec("SET SESSION interactive_timeout=28800");
            } catch(PDOException $e2) {
                error_log("Reconnection Error: " . $e2->getMessage());
                throw new Exception("Database connection failed: " . $e2->getMessage());
            }
        }
        return $this->conn;
    }

    public function getDbName() {
        return $this->db_name;
    }
}
?> 
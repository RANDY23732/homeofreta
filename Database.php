<?php
// Home page: INDEX.html
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

class Database {
    private $host = "localhost";
    private $db_name = "reta_ecommerce";  // Updated database name
    private $username = "root";  // Change to your MySQL username
    private $password = "";      // Change to your MySQL password
    public $conn;

    public function __construct() {
        $this->getConnection();
    }

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, 
                                  $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("set names utf8");
        } catch(PDOException $exception) {
            echo json_encode(["error" => "Connection error: " . $exception->getMessage()]);
            exit();
        }
        return $this->conn;
    }

    public function insert($table, $data) {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        
        $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        $stmt = $this->conn->prepare($sql);
        
        $values = array_values($data);
        $stmt->execute($values);
        
        return $this->conn->lastInsertId();
    }

    public function update($table, $data, $where) {
        $setClause = [];
        foreach (array_keys($data) as $key) {
            $setClause[] = "$key = ?";
        }
        $setClause = implode(', ', $setClause);
        
        $whereClause = [];
        foreach (array_keys($where) as $key) {
            $whereClause[] = "$key = ?";
        }
        $whereClause = implode(' AND ', $whereClause);
        
        $sql = "UPDATE $table SET $setClause WHERE $whereClause";
        $stmt = $this->conn->prepare($sql);
        
        $values = array_merge(array_values($data), array_values($where));
        $stmt->execute($values);
        
        return $stmt->rowCount();
    }

    public function select($table, $where = [], $columns = '*') {
        $sql = "SELECT $columns FROM $table";
        
        if (!empty($where)) {
            $whereClause = [];
            foreach (array_keys($where) as $key) {
                $whereClause[] = "$key = ?";
            }
            $whereClause = ' WHERE ' . implode(' AND ', $whereClause);
            $sql .= $whereClause;
        }
        
        $stmt = $this->conn->prepare($sql);
        
        if (!empty($where)) {
            $stmt->execute(array_values($where));
        } else {
            $stmt->execute();
        }
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function beginTransaction() {
        return $this->conn->beginTransaction();
    }

    public function commit() {
        return $this->conn->commit();
    }

    public function rollback() {
        return $this->conn->rollback();
    }

    public function query($sql, $params = []) {
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
}
?>



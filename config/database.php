<?php
// Demo-mode friendly Database adapter
// If APP_DEMO=true in environment, all DB calls return safe defaults to allow running without MySQL

// Ensure environment loader is available (usually included via config.php before this file)
if (!class_exists('Environment') && file_exists(__DIR__ . '/environment.php')) {
    require_once __DIR__ . '/environment.php';
}

class Database {
    private static $instance = null;
    private $conn;
    private $host = DB_HOST;
    private $db_name = DB_NAME;
    private $username = DB_USER;
    private $password = DB_PASS;
    private $demo = false;

    private function __construct() {
        // Detect demo mode from environment
        $this->demo = function_exists('getenv') ? (Environment::get('APP_DEMO', false) === true) : false;
        $this->connect();
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    private function connect() {
        if ($this->demo) {
            // In demo mode, do not connect to a database
            $this->conn = null;
            return;
        }
        $this->conn = null;
        try {
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
            ];
            
            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
        } catch(PDOException $exception) {
            error_log("Database connection error: " . $exception->getMessage());
            die("Error de conexión con la base de datos. Por favor intente más tarde.");
        }
    }

    public function getConnection() {
        return $this->conn;
    }

    public function query($sql, $params = []) {
        if ($this->demo) {
            // Return a stub statement with minimal interface
            return new class {
                public function execute($params = []) { return true; }
                public function fetch() { return null; }
                public function fetchAll() { return []; }
                public function rowCount() { return 1; }
            };
        }
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch(PDOException $exception) {
            error_log("Database query error: " . $exception->getMessage());
            error_log("SQL: " . $sql);
            error_log("Params: " . json_encode($params));
            throw $exception;
        }
    }

    public function fetchAll($sql, $params = []) {
        if ($this->demo) {
            return [];
        }
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }

    public function fetchOne($sql, $params = []) {
        if ($this->demo) {
            // If a COUNT(*) is requested, return 0 as default
            if (preg_match('/COUNT\s*\(\s*\*\s*\)/i', $sql)) {
                return ['count' => 0];
            }
            return null;
        }
        $stmt = $this->query($sql, $params);
        return $stmt->fetch();
    }

    public function execute($sql, $params = []) {
        if ($this->demo) {
            // Pretend the operation succeeded
            return 1;
        }
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }

    public function lastInsertId() {
        if ($this->demo) {
            return 1;
        }
        return $this->conn->lastInsertId();
    }

    public function beginTransaction() {
        if ($this->demo) { return true; }
        return $this->conn->beginTransaction();
    }

    public function commit() {
        if ($this->demo) { return true; }
        return $this->conn->commit();
    }

    public function rollBack() {
        if ($this->demo) { return true; }
        return $this->conn->rollBack();
    }

    public function inTransaction() {
        if ($this->demo) { return false; }
        return $this->conn->inTransaction();
    }

    // Prevent cloning and unserialization
    public function __clone() {
        throw new Exception("Cannot clone database instance");
    }

    public function __wakeup() {
        throw new Exception("Cannot unserialize database instance");
    }
}
?>

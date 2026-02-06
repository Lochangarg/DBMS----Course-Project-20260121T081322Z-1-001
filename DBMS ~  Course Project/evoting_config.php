<?php
/**
 * E-Voting System - Database Configuration
 * File: includes/config.php
 */

// Prevent direct access
if (!defined('EVOTING_SYSTEM')) {
    define('EVOTING_SYSTEM', true);
}

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'evoting_system');

// Application Configuration
define('SITE_NAME', 'Indian E-Voting System');
define('SITE_URL', 'http://localhost/e-voting-system');
define('ADMIN_EMAIL', 'admin@evoting.gov.in');

// Security Configuration
define('SESSION_TIMEOUT', 1800); // 30 minutes
define('PASSWORD_MIN_LENGTH', 8);
define('MAX_LOGIN_ATTEMPTS', 3);
define('LOCKOUT_TIME', 900); // 15 minutes

// File Upload Configuration
define('UPLOAD_PATH', dirname(__DIR__) . '/uploads/');
define('MAX_FILE_SIZE', 5242880); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'pdf']);

// Timezone
date_default_timezone_set('Asia/Kolkata');

// Error Reporting (Set to 0 in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database Connection Class
class Database {
    private static $instance = null;
    private $conn;
    
    private function __construct() {
        try {
            $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            
            if ($this->conn->connect_error) {
                throw new Exception("Connection failed: " . $this->conn->connect_error);
            }
            
            $this->conn->set_charset("utf8mb4");
        } catch (Exception $e) {
            die("Database Connection Error: " . $e->getMessage());
        }
    }
    
    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->conn;
    }
    
    public function query($sql) {
        return $this->conn->query($sql);
    }
    
    public function prepare($sql) {
        return $this->conn->prepare($sql);
    }
    
    public function escape($string) {
        return $this->conn->real_escape_string($string);
    }
    
    public function lastInsertId() {
        return $this->conn->insert_id;
    }
    
    // Prevent cloning
    private function __clone() {}
}

// Get database instance
function getDB() {
    return Database::getInstance();
}

// Helper function to get connection
function getConnection() {
    return Database::getInstance()->getConnection();
}
?>
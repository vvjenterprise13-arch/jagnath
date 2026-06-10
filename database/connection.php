<?php
$db_host = getenv('DB_HOST') ?: 'localhost';
$db_user = getenv('DB_USER') ?: 'root';
$db_pass = getenv('DB_PASS') ?: '';
$db_name = getenv('DB_NAME') ?: 'railway';
$db_port = (int)(getenv('DB_PORT') ?: 3306);

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name, $db_port);

if ($conn->connect_error) {
    $conn = null;
} else {
    $conn->set_charset("utf8mb4");
}

class DbSessionHandler implements SessionHandlerInterface {
    private $conn;
    public function __construct($conn) { $this->conn = $conn; }
    public function open($p, $n): bool { return $this->conn !== null; }
    public function close(): bool { return true; }
    public function read($id): string|false {
        if (!$this->conn) return '';
        $stmt = $this->conn->prepare("SELECT data FROM php_sessions WHERE session_id=? AND expires>NOW()");
        if (!$stmt) return '';
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $r = $stmt->get_result();
        return ($row = $r->fetch_assoc()) ? $row['data'] : '';
    }
    public function write($id, $data): bool {
        if (!$this->conn) return false;
        $expires = date('Y-m-d H:i:s', time() + 86400);
        $stmt = $this->conn->prepare("INSERT INTO php_sessions (session_id,data,expires) VALUES(?,?,?) ON DUPLICATE KEY UPDATE data=VALUES(data),expires=VALUES(expires)");
        if (!$stmt) return false;
        $stmt->bind_param("sss", $id, $data, $expires);
        return $stmt->execute();
    }
    public function destroy($id): bool {
        if (!$this->conn) return false;
        $stmt = $this->conn->prepare("DELETE FROM php_sessions WHERE session_id=?");
        if (!$stmt) return false;
        $stmt->bind_param("s", $id);
        return $stmt->execute();
    }
    public function gc($max): int|false {
        if ($this->conn) $this->conn->query("DELETE FROM php_sessions WHERE expires<NOW()");
        return true;
    }
}

if ($conn) {
    session_set_save_handler(new DbSessionHandler($conn), true);
}

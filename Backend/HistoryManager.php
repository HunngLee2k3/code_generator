<?php
class HistoryManager {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function saveDatabase($database_name, $user_id) {
        $database_name = $this->conn->real_escape_string($database_name);
        $user_id = (int)$user_id;

        $query = "INSERT INTO created_databases (database_name, user_id, created_at) 
                  VALUES ('$database_name', $user_id, NOW())";
        if ($this->conn->query($query)) {
            return $this->conn->insert_id; // Trả về ID của cơ sở dữ liệu vừa tạo
        }
        return false;
    }

    public function saveTable($table_name, $fields, $theme, $custom_css, $database_id = null) {
        $table_name = $this->conn->real_escape_string($table_name);
        $fields = $this->conn->real_escape_string($fields); // Chuỗi JSON của fields
        $theme = $this->conn->real_escape_string($theme);
        $custom_css = $this->conn->real_escape_string($custom_css);
        $database_id = $database_id !== null ? (int)$database_id : 'NULL';

        $query = "INSERT INTO tables (database_id, table_name, fields, theme, custom_css, created_at) 
                  VALUES ($database_id, '$table_name', '$fields', '$theme', '$custom_css', NOW())";
        if ($this->conn->query($query)) {
            return $this->conn->insert_id; // Trả về ID của bảng vừa tạo
        }
        return false;
    }

    public function assignTableToDatabase($table_id, $database_id) {
        $table_id = (int)$table_id;
        $database_id = (int)$database_id;

        $query = "UPDATE tables SET database_id = $database_id WHERE id = $table_id";
        return $this->conn->query($query);
    }
}
?>
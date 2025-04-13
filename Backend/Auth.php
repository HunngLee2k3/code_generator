<?php
class Auth {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
        // Bắt đầu session nếu chưa có
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // Kiểm tra xem người dùng đã đăng nhập chưa
    public function checkSession() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: login.php");
            exit;
        }
    }

    // Xử lý đăng nhập
    public function login($email, $password) {
        $stmt = $this->conn->prepare("SELECT id, username, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                return true;
            }
        }
        return false;
    }

    // Xử lý đăng xuất
    public function logout() {
        session_destroy();
        header("Location: login.php");
        exit;
    }

    // Lấy thông tin người dùng hiện tại
    public function getUser() {
        if (isset($_SESSION['user_id'])) {
            return [
                'id' => $_SESSION['user_id'],
                'username' => $_SESSION['username']
            ];
        }
        return null;
    }
}
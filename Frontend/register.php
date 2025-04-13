<?php
require_once '../Backend/config.php';

$page_title = "Đăng ký";
$breadcrumbs = [
    ['title' => 'Đăng ký', 'url' => 'register.php']
];

ob_start();
?>

<h3>Đăng ký tài khoản</h3>
<form method="POST" action="register.php">
    <div class="mb-3">
        <label>Tên người dùng:</label>
        <input type="text" name="username" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Email:</label>
        <input type="email" name="email" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Mật khẩu (tối thiểu 8 ký tự):</label>
        <input type="password" name="password" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Xác nhận mật khẩu:</label>
        <input type="password" name="confirm_password" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-custom">Đăng ký</button>
    <a href="login.php" class="btn btn-secondary">Đã có tài khoản? Đăng nhập</a>
</form>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = htmlspecialchars($_POST['username']);
    $email = htmlspecialchars($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Kiểm tra mật khẩu và xác nhận mật khẩu có khớp không
    if ($password !== $confirm_password) {
        echo "<p class='text-danger mt-3'>Mật khẩu và xác nhận mật khẩu không khớp!</p>";
    } else {
        // Kiểm tra độ dài mật khẩu
        if (strlen($password) < 8) {
            echo "<p class='text-danger mt-3'>Mật khẩu phải có ít nhất 8 ký tự!</p>";
        } else {
            // Mã hóa mật khẩu
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $email, $hashed_password);

            if ($stmt->execute()) {
                echo "<p class='text-success mt-3'>Đăng ký thành công! <a href='login.php'>Đăng nhập ngay</a></p>";
            } else {
                echo "<p class='text-danger mt-3'>Email đã tồn tại!</p>";
            }

            $stmt->close();
        }
    }
    $conn->close();
}
?>

<?php
$content = ob_get_clean();
require_once 'layout.php';
?>
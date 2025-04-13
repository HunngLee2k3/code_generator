<?php
require_once '../Backend/config.php';
require_once '../Backend/Auth.php';

$auth = new Auth($conn);

$page_title = "Đăng nhập";
$breadcrumbs = [
    ['title' => 'Đăng nhập', 'url' => 'login.php']
];

ob_start();
?>

<h3>Đăng nhập</h3>
<form method="POST" action="login.php">
    <div class="mb-3">
        <label>Email:</label>
        <input type="email" name="email" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Mật khẩu:</label>
        <input type="password" name="password" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-custom">Đăng nhập</button>
    <a href="register.php" class="btn btn-secondary">Chưa có tài khoản? Đăng ký</a>
</form>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = htmlspecialchars($_POST['email']);
    $password = $_POST['password'];

    if ($auth->login($email, $password)) {
        header("Location: index.php");
        exit;
    } else {
        echo "<p class='text-danger mt-3'>Email hoặc mật khẩu không đúng!</p>";
    }
}

$conn->close();
?>

<?php
$content = ob_get_clean();
require_once 'layout.php';
?>
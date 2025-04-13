<?php
require_once '../Backend/config.php';
require_once '../Backend/Auth.php';

$auth = new Auth($conn);
$user = $auth->getUser();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) : 'Công cụ tạo mã tự động'; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
    <link rel="stylesheet" href="../public/stylesheet/style.css">
</head>
<body>
    <!-- Thanh điều hướng -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">Công cụ tạo mã</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <?php if ($user): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php">Tạo mã</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="history.php">Lịch sử</a>
                        </li>
                        <li class="nav-item">
                            <span class="nav-link">Xin chào, <?php echo htmlspecialchars($user['username']); ?>!</span>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">Đăng xuất</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">Đăng nhập</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="register.php">Đăng ký</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Breadcrumb -->
    <div class="container mt-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <?php
                $breadcrumbs = isset($breadcrumbs) ? $breadcrumbs : [];
                array_unshift($breadcrumbs, ['title' => 'Trang chủ', 'url' => 'index.php']);

                foreach ($breadcrumbs as $index => $breadcrumb) {
                    if ($index === count($breadcrumbs) - 1) {
                        echo '<li class="breadcrumb-item active" aria-current="page">' . htmlspecialchars($breadcrumb['title']) . '</li>';
                    } else {
                        echo '<li class="breadcrumb-item"><a href="' . htmlspecialchars($breadcrumb['url']) . '">' . htmlspecialchars($breadcrumb['title']) . '</a></li>';
                    }
                }
                ?>
            </ol>
        </nav>
    </div>

    <!-- Nội dung chính -->
    <div class="container mt-4">
        <?php echo $content; ?>
    </div>

    <!-- Footer -->
    <footer class="text-white text-center py-3">
        <p>© 2025 Công cụ tạo mã tự động. All rights reserved.</p>
    </footer>

    <!-- Script Bootstrap và jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
require_once '../Backend/config.php';
require_once '../Backend/Auth.php';

// Kiểm tra session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra đăng nhập
$auth = new Auth($conn);
$auth->checkSession();
$user = $auth->getUser();

$page_title = "Lịch sử tạo mã";
$breadcrumbs = [
    ['title' => 'Trang chủ', 'href' => 'index.php'],
    ['title' => 'Lịch sử tạo mã', 'href' => '#']
];

ob_start();
?>

<div class="container mt-4">
    <h4>Lịch sử tạo mã</h4>
    <?php
    // Hiển thị thông báo lỗi nếu có
    if (isset($_SESSION['error'])) {
        echo "<div class='alert alert-danger'>" . htmlspecialchars($_SESSION['error']) . "</div>";
        unset($_SESSION['error']);
    }
    ?>
    <div id="history">
        <?php
        // Truy vấn danh sách cơ sở dữ liệu của người dùng
        $result = $conn->query("SELECT * FROM created_databases WHERE user_id = {$user['id']} ORDER BY created_at DESC");
        if ($result->num_rows > 0) {
            while ($db_row = $result->fetch_assoc()) {
                $database_id = $db_row['id'];
                $database_name = htmlspecialchars($db_row['database_name']);
                $created_at = htmlspecialchars($db_row['created_at']);

                echo "<h5>Cơ sở dữ liệu: $database_name - Tạo lúc: $created_at</h5>";

                // Truy vấn các bảng thuộc cơ sở dữ liệu này
                $tables_result = $conn->query("SELECT * FROM tables WHERE database_id = $database_id ORDER BY created_at DESC");
                if ($tables_result->num_rows > 0) {
                    while ($table_row = $tables_result->fetch_assoc()) {
                        $fields = json_decode($table_row['fields'], true);
                        $fields_display = '';
                        if (is_array($fields)) {
                            foreach ($fields as $field) {
                                $fields_display .= $field['name'] . " (" . $field['type'];
                                if (isset($field['foreign_key']) && $field['foreign_key']) {
                                    $fields_display .= ", FK -> " . $field['foreign_key'];
                                }
                                $fields_display .= "), ";
                            }
                            $fields_display = rtrim($fields_display, ", ");
                        } else {
                            $fields_display = "Không có trường nào";
                        }

                        echo "<p>";
                        echo "<strong>Bảng:</strong> " . htmlspecialchars($table_row['table_name']) . " - ";
                        echo "<strong>Giao diện:</strong> " . htmlspecialchars($table_row['theme']) . " - ";
                        echo "<strong>Tạo lúc:</strong> " . htmlspecialchars($table_row['created_at']) . " - ";
                        echo "<strong>Các trường:</strong> " . htmlspecialchars($fields_display) . " - ";
                        echo "<a href='view_history.php?id=" . htmlspecialchars($table_row['id']) . "'>Xem chi tiết</a>";
                        echo "</p>";
                    }
                } else {
                    echo "<p>Chưa có bảng nào trong cơ sở dữ liệu này.</p>";
                }
            }
        } else {
            echo "<p>Chưa có cơ sở dữ liệu nào được tạo.</p>";
        }
        $conn->close();
        ?>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once 'layout.php';
?>
<?php
// Thông tin kết nối cơ sở dữ liệu
$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "code_generator_db";

// Tạo kết nối
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
?>
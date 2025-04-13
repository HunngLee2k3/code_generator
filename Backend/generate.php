<?php
require_once 'config.php';
require_once 'Auth.php';
require_once 'CodeGenerator.php';
require_once 'HistoryManager.php';
require_once 'ActionHandler.php';

$auth = new Auth($conn);
$auth->checkSession();
$user = $auth->getUser();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $serverType = $_POST['server_type'];
    $dbType = $_POST['db_type'];
    $tableName = $_POST['table_name'];
    $fields = $_POST['fields'];
    $action = $_POST['action'];
    $theme = $_POST['theme'];
    $customCss = $_POST['custom_css'];

    // Tạo mã bằng CodeGenerator
    $generator = new CodeGenerator($tableName, $fields, $theme, $customCss);
    $sql = $generator->generateSQL();
    $html = $generator->generateHTML();
    $js = $generator->generateJavaScript();
    $php = $generator->generatePHP();

    // Lưu lịch sử bằng HistoryManager
    $historyManager = new HistoryManager($conn);
    $historyManager->saveHistory($tableName, $fields, $theme, $customCss, $user['id']);

    // Xử lý hành động bằng ActionHandler
    $actionHandler = new ActionHandler($sql, $html, $js, $php, $tableName);
    if ($action === 'view') {
        $actionHandler->handleView($_POST['view_type']);
    } elseif ($action === 'run') {
        $actionHandler->handleRun();
    } elseif ($action === 'download') {
        $actionHandler->handleDownload();
    }
}

$conn->close();
?>
<?php
require_once '../Backend/config.php';
require_once '../Backend/Auth.php';
require_once '../Backend/CodeGenerator.php';

$auth = new Auth($conn);
$auth->checkSession();
$user = $auth->getUser();

if (!isset($_GET['id'])) {
    header("Location: history.php");
    exit;
}

$history_id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM history WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $history_id, $user['id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: history.php");
    exit;
}

$history = $result->fetch_assoc();
$fields = json_decode($history['fields'], true);
$tableName = $history['table_name'];
$theme = $history['theme'];
$customCss = $history['custom_css'];

// Tạo lại mã bằng CodeGenerator
$generator = new CodeGenerator($tableName, $fields, $theme, $customCss);
$sql = $generator->generateSQL();
$html = $generator->generateHTML();
$js = $generator->generateJavaScript();
$php = $generator->generatePHP();

$conn->close();

$page_title = "Chi tiết lịch sử tạo mã";
$breadcrumbs = [
    ['title' => 'Lịch sử', 'url' => 'history.php'],
    ['title' => 'Chi tiết', 'url' => 'view_history.php?id=' . $history_id]
];

ob_start();
?>

<div class="history-card">
    <h4>Thông tin bảng: <?php echo htmlspecialchars($tableName); ?></h4>
    <p><strong>Giao diện:</strong> <?php echo htmlspecialchars($theme); ?></p>
    <p><strong>Thời gian tạo:</strong> <?php echo htmlspecialchars($history['created_at']); ?></p>

    <h5>Danh sách các trường</h5>
    <table class="fields-table">
        <thead>
            <tr>
                <th>Tên trường</th>
                <th>Kiểu dữ liệu</th>
                <th>Xác thực</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($fields as $field) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($field['name']) . "</td>";
                echo "<td>" . htmlspecialchars($field['type']);
                if (isset($field['max_length']) && $field['type'] === 'VARCHAR') {
                    echo "(" . htmlspecialchars($field['max_length']) . ")";
                }
                echo "</td>";
                echo "<td>" . ($field['validation'] !== 'none' ? htmlspecialchars($field['validation']) : 'Không xác thực') . "</td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>

    <h5>Mã được tạo</h5>
    <ul class="nav nav-tabs" id="codeTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="sql-tab" data-bs-toggle="tab" data-bs-target="#sql" type="button" role="tab" aria-controls="sql" aria-selected="true">SQL</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="html-tab" data-bs-toggle="tab" data-bs-target="#html" type="button" role="tab" aria-controls="html" aria-selected="false">HTML</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="js-tab" data-bs-toggle="tab" data-bs-target="#js" type="button" role="tab" aria-controls="js" aria-selected="false">JavaScript</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="php-tab" data-bs-toggle="tab" data-bs-target="#php" type="button" role="tab" aria-controls="php" aria-selected="false">PHP</button>
        </li>
    </ul>
    <div class="tab-content" id="codeTabContent">
        <div class="tab-pane fade show active" id="sql" role="tabpanel" aria-labelledby="sql-tab">
            <button class="copy-btn" onclick="copyCode('sql-code')">Sao chép</button>
            <pre id="sql-code"><?php echo htmlspecialchars($sql); ?></pre>
        </div>
        <div class="tab-pane fade" id="html" role="tabpanel" aria-labelledby="html-tab">
            <button class="copy-btn" onclick="copyCode('html-code')">Sao chép</button>
            <pre id="html-code"><?php echo htmlspecialchars($html); ?></pre>
        </div>
        <div class="tab-pane fade" id="js" role="tabpanel" aria-labelledby="js-tab">
            <button class="copy-btn" onclick="copyCode('js-code')">Sao chép</button>
            <pre id="js-code"><?php echo htmlspecialchars($js); ?></pre>
        </div>
        <div class="tab-pane fade" id="php" role="tabpanel" aria-labelledby="php-tab">
            <button class="copy-btn" onclick="copyCode('php-code')">Sao chép</button>
            <pre id="php-code"><?php echo htmlspecialchars($php); ?></pre>
        </div>
    </div>
</div>

<script>
function copyCode(elementId) {
    const code = document.getElementById(elementId).innerText;
    navigator.clipboard.writeText(code).then(() => {
        alert("Đã sao chép mã!");
    }).catch(err => {
        console.error("Lỗi khi sao chép mã:", err);
    });
}
</script>

<?php
$content = ob_get_clean();
require_once 'layout.php';
?>
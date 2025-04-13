<?php
require_once '../Backend/config.php';
require_once '../Backend/Auth.php';

$auth = new Auth($conn);
$auth->checkSession();
$user = $auth->getUser();

$page_title = "Công cụ tạo mã tự động";
$breadcrumbs = []; // Chỉ có "Trang chủ" (được thêm tự động trong layout)

ob_start();
?>

<div class="container mt-4">
    <!-- Giao diện và phần mềm -->
    <h4>Giao diện và phần mềm</h4>
    <label>Giao diện:</label>
    <p>Datatables, Buttons, Date Time, Editor, Select, jQuery 1, Moment</p>
    <label>Phần mềm:</label>
    <button class="btn btn-custom">Tùy chỉnh</button>

    <!-- Tùy chỉnh giao diện -->
    <h4 class="mt-4">Tùy chỉnh giao diện</h4>
    <label>Chọn giao diện:</label><br>
    <select name="theme" id="theme" class="form-control w-25">
        <option value="Bootstrap">Bootstrap</option>
        <option value="Tailwind">Tailwind CSS</option>
        <option value="Custom">Tùy chỉnh</option>
    </select>
    <div id="customTheme" style="display: none;" class="mt-2">
        <label>CSS tùy chỉnh:</label>
        <textarea id="customCss" class="form-control" placeholder="Nhập CSS tùy chỉnh"></textarea>
    </div>

    <!-- Máy chủ và cơ sở dữ liệu -->
    <h4 class="mt-4">Máy chủ và cơ sở dữ liệu</h4>
    <form id="generatorForm" method="POST" action="../Backend/generate.php">
        <input type="hidden" name="theme" id="themeInput">
        <input type="hidden" name="custom_css" id="customCssInput">
        <label>Loại máy chủ:</label><br>
        <select name="server_type" class="form-control w-25">
            <option value="PHP">PHP</option>
            <option value="NodeJS">NodeJS</option>
            <option value="NET Core">.NET Core</option>
            <option value="NET Framework">.NET Framework</option>
        </select><br>

        <label>Loại cơ sở dữ liệu:</label><br>
        <select name="db_type" class="form-control w-25">
            <option value="MySQL">MySQL/MariaDB</option>
            <option value="PostgreSQL">PostgreSQL</option>
            <option value="SQL Server">SQL Server</option>
        </select><br>

        <label>Tên bảng:</label>
        <input type="text" name="table_name" class="form-control" placeholder="Nhập tên bảng" required><br>

        <label>Khóa chính:</label>
        <input type="text" name="primary_key" class="form-control" value="id" readonly><br>

        <!-- Biểu mẫu / Bảng -->
        <h4>Biểu mẫu / Bảng</h4>
        <div id="fields">
            <div class="row mb-2 field-row align-items-center">
                <div class="col-md-2">
                    <label class="form-label">Tên trường</label>
                    <input type="text" name="fields[0][name]" class="form-control" placeholder="Trường 1" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Kiểu dữ liệu</label>
                    <select name="fields[0][type]" class="form-control type-select">
                        <option value="TEXT">Text</option>
                        <option value="INT">Number (Integer)</option>
                        <option value="FLOAT">Number (Float)</option>
                        <option value="DECIMAL">Number (Decimal)</option>
                        <option value="DATE">Date</option>
                        <option value="DATETIME">DateTime</option>
                        <option value="BOOLEAN">Boolean</option>
                        <option value="VARCHAR">Email (VARCHAR)</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Độ dài tối đa</label>
                    <input type="number" name="fields[0][max_length]" class="form-control max-length" placeholder="255" min="1" max="1000">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Giá trị mặc định</label>
                    <input type="text" name="fields[0][default_value]" class="form-control default-value" placeholder="Giá trị mặc định">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Xác thực</label>
                    <select name="fields[0][validation]" class="form-control">
                        <option value="none">Không xác thực</option>
                        <option value="required">Bắt buộc</option>
                        <option value="email">Email</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <label class="form-label">Cho phép NULL</label>
                    <input type="checkbox" name="fields[0][allow_null]" class="form-check-input allow-null" value="1">
                </div>
                <div class="col-md-1">
                    <label class="form-label d-none d-md-block"> </label>
                    <button type="button" class="btn btn-secondary remove-field">Xóa</button>
                </div>
            </div>
        </div>
        <button type="button" class="btn btn-secondary mt-2" id="addField">+ Thêm trường</button>

        <!-- Hành động -->
        <h4 class="mt-4">Hành động</h4>
        <button type="submit" name="action" value="download" class="btn btn-custom">Tải xuống gói mã</button>
        <button type="submit" name="action" value="run" class="btn btn-custom">Chạy ngay</button><br><br>

        <label>Xem:</label>
        <button type="button" class="btn btn-custom view-code" data-type="sql">SQL</button>
        <button type="button" class="btn btn-custom view-code" data-type="html">HTML</button>
        <button type="button" class="btn btn-custom view-code" data-type="javascript">JavaScript</button>
        <button type="button" class="btn btn-custom view-code" data-type="php">PHP</button>
    </form>

    <!-- Xem trước -->
    <h4 class="mt-4">Xem trước</h4>
    <div id="preview" class="preview"></div>

    <!-- Lịch sử -->
    <h4 class="mt-4">Lịch sử tạo mã</h4>
    <div id="history">
        <?php
        $result = $conn->query("SELECT * FROM history WHERE user_id = {$user['id']} ORDER BY created_at DESC");
        while ($row = $result->fetch_assoc()) {
            echo "<p>Bảng: " . $row['table_name'] . " - Giao diện: " . $row['theme'] . " - Tạo lúc: " . $row['created_at'] . "</p>";
        }
        $conn->close();
        ?>
    </div>
</div>

<script>
    let fieldCount = 1;

    // Hàm kiểm tra và ẩn/hiện trường "Độ dài tối đa"
    function toggleMaxLengthField(row) {
        const typeSelect = row.querySelector(".type-select");
        const maxLengthInput = row.querySelector(".max-length");
        const maxLengthDiv = maxLengthInput.parentElement;

        if (typeSelect.value === "VARCHAR") {
            maxLengthDiv.style.display = "block";
        } else {
            maxLengthDiv.style.display = "none";
        }
    }

    // Thêm trường mới
    document.getElementById("addField").addEventListener("click", function() {
        const fieldHtml = `
            <div class="row mb-2 field-row align-items-center">
                <div class="col-md-2">
                    <label class="form-label">Tên trường</label>
                    <input type="text" name="fields[${fieldCount}][name]" class="form-control" placeholder="Trường ${fieldCount + 1}" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Kiểu dữ liệu</label>
                    <select name="fields[${fieldCount}][type]" class="form-control type-select">
                        <option value="TEXT">Text</option>
                        <option value="INT">Number (Integer)</option>
                        <option value="FLOAT">Number (Float)</option>
                        <option value="DECIMAL">Number (Decimal)</option>
                        <option value="DATE">Date</option>
                        <option value="DATETIME">DateTime</option>
                        <option value="BOOLEAN">Boolean</option>
                        <option value="VARCHAR">Email (VARCHAR)</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Độ dài tối đa</label>
                    <input type="number" name="fields[${fieldCount}][max_length]" class="form-control max-length" placeholder="255" min="1" max="1000">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Giá trị mặc định</label>
                    <input type="text" name="fields[${fieldCount}][default_value]" class="form-control default-value" placeholder="Giá trị mặc định">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Xác thực</label>
                    <select name="fields[${fieldCount}][validation]" class="form-control">
                        <option value="none">Không xác thực</option>
                        <option value="required">Bắt buộc</option>
                        <option value="email">Email</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <label class="form-label">Cho phép NULL</label>
                    <input type="checkbox" name="fields[${fieldCount}][allow_null]" class="form-check-input allow-null" value="1">
                </div>
                <div class="col-md-1">
                    <label class="form-label d-none d-md-block"> </label>
                    <button type="button" class="btn btn-secondary remove-field">Xóa</button>
                </div>
            </div>`;
        document.getElementById("fields").insertAdjacentHTML("beforeend", fieldHtml);
        const newRow = document.getElementById("fields").lastElementChild;
        toggleMaxLengthField(newRow);
        fieldCount++;
    });

    // Xóa trường
    document.addEventListener("click", function(e) {
        if (e.target.classList.contains("remove-field")) {
            const fieldRows = document.querySelectorAll(".field-row");
            if (fieldRows.length > 1) {
                e.target.closest(".field-row").remove();
            }
        }
    });

    // Ẩn/hiện trường "Độ dài tối đa" khi thay đổi kiểu dữ liệu
    document.addEventListener("change", function(e) {
        if (e.target.classList.contains("type-select")) {
            const row = e.target.closest(".field-row");
            toggleMaxLengthField(row);
        }
    });

    // Hiển thị textarea CSS tùy chỉnh
    document.getElementById("theme").addEventListener("change", function() {
        const customThemeDiv = document.getElementById("customTheme");
        if (this.value === "Custom") {
            customThemeDiv.style.display = "block";
        } else {
            customThemeDiv.style.display = "none";
        }
    });

    // Cập nhật giá trị theme và custom CSS trước khi gửi form
    document.getElementById("generatorForm").addEventListener("submit", function() {
        document.getElementById("themeInput").value = document.getElementById("theme").value;
        document.getElementById("customCssInput").value = document.getElementById("customCss").value;
    });

    // Xem mã
    document.querySelectorAll(".view-code").forEach(button => {
        button.addEventListener("click", function() {
            const type = this.getAttribute("data-type");
            const formData = new FormData(document.getElementById("generatorForm"));
            formData.append("action", "view");
            formData.append("view_type", type);

            fetch("../Backend/generate.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                document.getElementById("preview").innerHTML = "<pre>" + data + "</pre>";
            })
            .catch(error => console.error("Error:", error));
        });
    });

    // Khởi tạo trạng thái ban đầu cho trường "Độ dài tối đa"
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll(".field-row").forEach(row => {
            toggleMaxLengthField(row);
        });
    });
</script>

<?php
$content = ob_get_clean();
require_once 'layout.php';
?>
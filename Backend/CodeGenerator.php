<?php
class CodeGenerator {
    private $tableName;
    private $fields;
    private $theme;
    private $customCss;

    public function __construct($tableName, $fields, $theme, $customCss) {
        $this->tableName = $tableName;
        $this->fields = $fields;
        $this->theme = $theme;
        $this->customCss = $customCss;
    }

    public function generateSQL() {
        $sql = "CREATE TABLE {$this->tableName} (id INT AUTO_INCREMENT PRIMARY KEY";
        foreach ($this->fields as $field) {
            $sql .= ", " . $field['name'] . " " . $field['type'];
            if ($field['validation'] === 'required') {
                $sql .= " NOT NULL";
            }
        }
        $sql .= ");";
        return $sql;
    }

    public function generateHTML() {
        $html = "<!DOCTYPE html><html lang='vi'><head><title>{$this->tableName}</title>";
        if ($this->theme === "Bootstrap") {
            $html .= "<link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>";
        } elseif ($this->theme === "Tailwind") {
            $html .= "<script src='https://cdn.tailwindcss.com'></script>";
        } elseif ($this->theme === "Custom" && !empty($this->customCss)) {
            $html .= "<style>{$this->customCss}</style>";
        }
        $html .= "</head><body>";
        if ($this->theme === "Bootstrap") {
            $html .= "<div class='container mt-4'><h1>{$this->tableName}</h1>";
            $html .= "<form method='POST' action=''><table class='table table-dark'>";
        } elseif ($this->theme === "Tailwind") {
            $html .= "<div class='container mx-auto mt-4'><h1 class='text-2xl font-bold'>{$this->tableName}</h1>";
            $html .= "<form method='POST' action=''><table class='w-full text-white'>";
        } else {
            $html .= "<div><h1>{$this->tableName}</h1>";
            $html .= "<form method='POST' action=''><table>";
        }

        foreach ($this->fields as $field) {
            $html .= "<tr><td>" . ucfirst($field['name']) . "</td><td><input type='text' name='{$field['name']}'";
            if ($this->theme === "Bootstrap") {
                $html .= " class='form-control'";
            } elseif ($this->theme === "Tailwind") {
                $html .= " class='border p-2 w-full'";
            }
            if ($field['validation'] === 'required') {
                $html .= " required";
            } elseif ($field['validation'] === 'email') {
                $html .= " type='email'";
            }
            $html .= "></td></tr>";
        }

        if ($this->theme === "Bootstrap") {
            $html .= "<tr><td colspan='2'><button type='submit' class='btn btn-primary'>Gửi</button></td></tr>";
            $html .= "</table></form></div>";
        } elseif ($this->theme === "Tailwind") {
            $html .= "<tr><td colspan='2'><button type='submit' class='bg-blue-500 text-white p-2 rounded'>Gửi</button></td></tr>";
            $html .= "</table></form></div>";
        } else {
            $html .= "<tr><td colspan='2'><button type='submit'>Gửi</button></td></tr>";
            $html .= "</table></form></div>";
        }
        $html .= "</body></html>";

        return $html;
    }

    public function generateJavaScript() {
        return "console.log('Form for {$this->tableName} loaded');";
    }

    public function generatePHP() {
        $php = "<?php\n";
        $php .= "\$conn = new mysqli('localhost', 'root', '', 'database');\n";
        $php .= "if (\$_SERVER['REQUEST_METHOD'] === 'POST') {\n";
        foreach ($this->fields as $field) {
            $php .= "    \${$field['name']} = \$_POST['{$field['name']}'];\n";
        }
        $php .= "    \$sql = \"INSERT INTO {$this->tableName} (";
        $fieldNames = array_column($this->fields, 'name');
        $php .= implode(", ", $fieldNames) . ") VALUES ('" . implode("', '", array_fill(0, count($fieldNames), "'")) . "');\n";
        foreach ($this->fields as $field) {
            $php .= "    \$sql = str_replace(\"'{$field['name']}'\", \"'\" . \${$field['name']} . \"'\", \$sql);\n";
        }
        $php .= "    \$conn->query(\$sql);\n";
        $php .= "}\n";

        return $php;
    }
}
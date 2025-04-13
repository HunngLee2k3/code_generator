<?php
require_once 'FileHandler.php';

class ActionHandler {
    private $sql;
    private $html;
    private $js;
    private $php;
    private $tableName;

    public function __construct($sql, $html, $js, $php, $tableName) {
        $this->sql = $sql;
        $this->html = $html;
        $this->js = $js;
        $this->php = $php;
        $this->tableName = $tableName;
    }

    public function handleView($viewType) {
        if ($viewType === 'sql') {
            echo htmlspecialchars($this->sql);
        } elseif ($viewType === 'html') {
            echo htmlspecialchars($this->html);
        } elseif ($viewType === 'javascript') {
            echo htmlspecialchars($this->js);
        } elseif ($viewType === 'php') {
            echo htmlspecialchars($this->php);
        }
        exit;
    }

    public function handleRun() {
        echo "<script>document.getElementById('preview').innerHTML = " . json_encode($this->html) . ";</script>";
        header("Location: ../Frontend/index.php");
        exit;
    }

    public function handleDownload() {
        $fileHandler = new FileHandler();
        $fileHandler->createZip($this->tableName, $this->sql, $this->html, $this->js, $this->php);
        exit;
    }
}
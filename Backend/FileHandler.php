<?php
class FileHandler {
    public function createZip($tableName, $sql, $html, $js, $php) {
        $zip = new ZipArchive();
        $zipFile = "code_$tableName.zip";
        if ($zip->open($zipFile, ZipArchive::CREATE) === TRUE) {
            $zip->addFromString("create_table.sql", $sql);
            $zip->addFromString("index.html", $html);
            $zip->addFromString("script.js", $js);
            $zip->addFromString("save.php", $php);
            $zip->close();

            header("Content-Type: application/zip");
            header("Content-Disposition: attachment; filename=$zipFile");
            header("Content-Length: " . filesize($zipFile));
            readfile($zipFile);
            unlink($zipFile);
        }
    }
}
<?php
require_once '../Backend/config.php';
require_once '../Backend/Auth.php';

$auth = new Auth($conn);
$auth->logout();
?>
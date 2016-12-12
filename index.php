<?php
require_once realpath('.')."/page/index/indexContent.php";
consoleData((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') ?$protocol = "https": $protocol = "http");
?>
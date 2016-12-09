<?php
$thisPageName = "Homepage";
$thisPageMeta = "";
$thisPageHeading = "Welcome to Modern Business";
$thisPageSubHeading = "";
$thisPageUpperContent = $_SERVER['DOCUMENT_ROOT']."/page/index/view/indexUpperView.php";
$thisPageDownerContent = "";
$thisPageCSS = $_SERVER['DOCUMENT_ROOT']."/page/index/view/css/cssIndex.php";
$thisPageJS = $_SERVER['DOCUMENT_ROOT']."/page/index/view/js/jsIndex.php";
$thisPageContent = $_SERVER['DOCUMENT_ROOT']."/page/index/view/indexView.php";
$thisPageController = $_SERVER['DOCUMENT_ROOT']."/page/index/controller/controllerIndex.php";
if (!empty($thisPageController)){require_once $thisPageController;}
require_once $_SERVER['DOCUMENT_ROOT']."/page/_layout/_layout.php";
//echo realpath('.');
?>
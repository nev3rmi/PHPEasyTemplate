<?php
$thisPageName = "This is new page";
$thisPageMeta = "";
$thisPageHeading = "New page";
$thisPageSubHeading = "";
$thisPageUpperContent = $_SERVER['DOCUMENT_ROOT']."/page/index/view/newpageUpperView.php";
$thisPageDownerContent = "";
$thisPageCSS = $_SERVER['DOCUMENT_ROOT']."/page/index/view/css/cssNewpage.php";
$thisPageJS = $_SERVER['DOCUMENT_ROOT']."/page/index/view/js/jsNewpage.php";
$thisPageContent = $_SERVER['DOCUMENT_ROOT']."/page/index/view/newpageView.php";
$thisPageController = $_SERVER['DOCUMENT_ROOT']."/page/index/controller/controllerNewpage.php";
if (!empty($thisPageController)){require_once $thisPageController;}
require_once $_SERVER['DOCUMENT_ROOT']."/page/_layout/_layout.php";
//echo realpath('.');
?>
<?php
$thisPageName = "Register";
$thisPageMeta = "";
$thisPageHeading = "Register New Account";
$thisPageContent = $_SERVER['DOCUMENT_ROOT']."/page/user/register/view/registerView.php";
$thisPageController = $_SERVER['DOCUMENT_ROOT']."/page/user/register/controller/registerController.php";
if (!empty($thisPageController)){require_once $thisPageController;}
require_once $_SERVER['DOCUMENT_ROOT']."/page/_layout/_layout.php";
?>
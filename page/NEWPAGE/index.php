<?php
// *INPUT String
$thisPageName = ""; // Page Title
$thisPageMeta = ""; // Page Additional Meta
$thisPageHeading = "Welcome to Modern Business"; // Heading of page
$thisPageSubHeading = ""; // Subheading of page will be near PageHeading
// Should be in newpageView.php
//$thisPageCSS = ""; // Additional Onpage CSS
//$thisPageJS = ""; // Addition Onpage JS

// *INPUT Location File
$thisPageUpperContent = $_SERVER['DOCUMENT_ROOT']."/page/index/view/indexUpperView.php"; // Before go inside class container
$thisPageContent = $_SERVER['DOCUMENT_ROOT']."/page/index/view/indexView.php"; // Inside class container also know as main content.
$thisPageDownerContent = ""; // After go outside class container
$thisPageController = $_SERVER['DOCUMENT_ROOT']."/page/index/controller/controllerIndex.php";

// *INPUT boolean [USE: 0 - Disable, 1 - Enable]
$thisPageBreadCrumbUse = 1; // Auto display how to access page, Ex: Product > Viewdetails > 1


// =======>>>>
// Process
if (!empty($thisPageController)){require_once $thisPageController;}
require_once $_SERVER['DOCUMENT_ROOT']."/page/_layout/_layout.php";
?>
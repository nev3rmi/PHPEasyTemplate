<body>
<?php require_once $_phpPath."page/_layout/body/_navigationbar.php"; ?>
<?php require_once $_phpPath."page/_layout/body/_header.php"; ?>
<?php require_once $_phpPath."page/_layout/body/_container.php"; ?>
<?php require_once $_phpPath."css/css.php"; ?>
<?php //if(!empty($thisPageCSS)){require_once $thisPageCSS;}
echo $thisPageCSS;
?>
<?php require_once $_phpPath."js/js.php"; ?>
<?php //if(!empty($thisPageJS)){require_once $thisPageJS;}
echo $thisPageJS;
?>
</body>
<div class="upper-containner">
<?php if (!empty($thisPageUpperContent)){require_once $thisPageUpperContent;} ?>
</div>
<div class="container">
<hr>
<?php require_once $thisPageContent ?>
<hr>
<?php require_once $_phpPath."page/_layout/body/_footer.php"; ?>
</div>
<div class="downer-containner">
<?php if (!empty($thisPageDownerContent)){require_once $thisPageDownerContent;} ?>
</div>
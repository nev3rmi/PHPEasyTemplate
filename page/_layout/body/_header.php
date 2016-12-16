<div class="upper-containner">
<?php if (!empty($thisPageUpperContent)){require_once $thisPageUpperContent;} ?>
</div>
<header class="business-header">
  <div class="container">
    <div class="row">
      <div class="col-lg-12">
        <h1 class="tagline"><?php echo $thisPageHeading?> <small><?php echo $thisPageSubHeading?></small></h1>
        <?php 
		 echo ($thisPageBreadCrumbUse == 1?'<ol class="breadcrumb">'.generateLinkPath($_documentPath, $_url).'</ol>':'');
		?>
      </div>
    </div>
  </div>
</header>
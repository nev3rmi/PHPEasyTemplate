<header class="business-header">
  <div class="container">
    <div class="row">
      <div class="col-lg-12">
        <h1 class="tagline"><?php echo $thisPageHeading?> <small><?php echo $thisPageSubHeading?></small></h1>
        <?php 
		if ($thisPageBreadCrumbUse != 1) 
		{
			echo ''; 
		} else { 
			echo '  
			<ol class="breadcrumb">
			  <!-- Need code to auto run directory -->
			</ol>
		';
		}
		?>
      </div>
    </div>
  </div>
</header>
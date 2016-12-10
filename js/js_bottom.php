<?php
// Create priority for jquery.js must be load first
foreach (priorityKey(getAllFileInFolderWithType($_phpPath.'js', 'js'), "jquery-3.1.1.min.js") as $jsFile){
	echo '<script src="'.$_url.'js/'.$jsFile.'"></script>';
}
?>
<script>
$(function() {
     var pgurl = window.location.href;
	 /*
	 console.log($(this).attr("href"));
	 console.log(pgurl);
     */
	 $("#nav ul li a").each(function(){
          if(/*regexCheck($(this).attr("href"), pgurl) ||*/ $(this).attr("href") == pgurl || $(this).attr("href") == '' )
          $(this).parent().addClass("active");
     })
});
</script>
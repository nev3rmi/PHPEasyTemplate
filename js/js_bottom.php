<?php
// Create priority for jquery.js must be load first
foreach (priorityKey(getAllFileInFolderWithType($_phpPath.'js', 'js'), "jquery-3.1.1.min.js") as $jsFile){
	echo '<script src="'.$_url.'js/'.$jsFile.'"></script>';
}
foreach (getAllFileInFolderWithType($_phpPath.'page'.(empty(substr($_SERVER['REQUEST_URI'],1))?'/index':$_SERVER['REQUEST_URI']).'/view/js', 'js') as $jsFile){
	echo '<script src="'.$_url.'page/'.(empty(substr($_SERVER['REQUEST_URI'],1))?'index':$_SERVER['REQUEST_URI']).'/view/js/'.$jsFile.'"></script>';
}
?>
<script>
// This function need to be improve:
// Because if second level apply it will be wrong.
$(function() {
     var pgurl = window.location.href;
	 $("#nav ul li a").each(function(){
          if(/*regexCheck($(this).attr("href"), pgurl) ||*/ $(this).attr("href") == pgurl || $(this).attr("href") == '' )
          $(this).parent().addClass("active");
     })
});
</script>
<script src="<?php echo $_url;?>js/jquery-3.1.1.min.js" ></script>
<script src="<?php echo $_url;?>js/bootstrap.min.js" ></script>
<script src="<?php echo $_url;?>js/phpeasy_library.js" ></script>
<script>
$(function() {
     var pgurl = window.location.href;
	 console.log($(this).attr("href"));
	 console.log(pgurl);
     $("#nav ul li a").each(function(){
          if(/*regexCheck($(this).attr("href"), pgurl) ||*/ $(this).attr("href") == pgurl || $(this).attr("href") == '' )
          $(this).parent().addClass("active");
     })
});
</script>
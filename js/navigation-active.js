// JavaScript Document
// This function need to be improve:
// Because if second level apply it will be wrong.
$(function() {
     var pgurl = window.location.href;
	 $("#nav ul li a").each(function(){
          if(/*regexCheck($(this).attr("href"), pgurl) ||*/ $(this).attr("href") == pgurl || $(this).attr("href") == '' )
          $(this).parent().addClass("active");
     })
});

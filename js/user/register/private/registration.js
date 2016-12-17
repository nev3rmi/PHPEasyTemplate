// JavaScript Document
$("#submitNewAccount").click(function (){
	"use strict";
	var registrationForm = $("#registerNewAccount").serialize();
	$.post(getURL() + "page/user/register/controller/registerController.php",registrationForm,function(result){
		$("#registerStatus").html(result);
	});
});

// Need finish on tomorrow
$("#inputEmail").on("keydown change",function (e){
	"use strict";
	var value = ($("#inputEmail").val() + String.fromCharCode(e.which));
	if (regexCheck(emailPattern(),value) == true)
	{
		$("#inputEmailStatus").parent().removeClass("has-error").addClass("has-success");
		$("#inputEmailStatus").html("Correct");
	}else{
		$("#inputEmailStatus").parent().addClass("has-error").removeClass("has-success");
		$("#inputEmailStatus").html("Incorrect type of email. Ex: xxx@xxx.com");
	}
		
});


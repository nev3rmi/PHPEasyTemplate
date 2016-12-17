// JavaScript Document
$("#submitNewAccount").click(function (){
	"use strict";
	var registrationForm = $("#registerNewAccount").serialize();
	$.post(getURL() + "page/user/register/controller/registerController.php",registrationForm,function(result){
		$("#registerStatus").html(result);
	});
});

// Need finish on tomorrow
$("#inputEmail").on("keyup",function (e){
	"use strict";
	var value = ($("#inputEmail").val() + String.fromCharCode(e.which)).toLowerCase();
	if (regexCheck(emailPattern(),value))
	{
		$("#inputEmailStatus").parent().removeClass("has-error").addClass("has-success");
		$("#inputEmailStatus").html('<i class="fa fa-check" aria-hidden="true"></i> Correct');
	}else{
		$("#inputEmailStatus").parent().addClass("has-error").removeClass("has-success");
		$("#inputEmailStatus").html('<i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Incorrect type of email. Ex: xxx@xxx.com');
	}
		
});

// Bug
$("#inputRetypePassword, #inputPassword").on("keyup",function (e){
	"use strict";
	var typePassword = ($("#inputPassword").val() + String.fromCharCode(e.which)).toLowerCase();
	var retypePassword = ($("#inputRetypePassword").val() + String.fromCharCode(e.which)).toLowerCase();
	if (regexCheck(matching2WordsPattern(retypePassword),typePassword) && ((typePassword != null) && (retypePassword != null)))
	{
		$("#inputRetypePasswordStatus").parent().removeClass("has-error").addClass("has-success");
		$("#inputRetypePasswordStatus").html('<i class="fa fa-check" aria-hidden="true"></i> Correct');
	}else{
		$("#inputRetypePasswordStatus").parent().addClass("has-error").removeClass("has-success");
		$("#inputRetypePasswordStatus").html('<i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Re-typed password does not match');
	}
});


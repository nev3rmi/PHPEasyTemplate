// JavaScript Document
$(function() {
  // Handler for .ready() called.
  	$.session.set('emailCheck', false);
	$.session.set('passwordCheck', false);
	$.session.set('retypeCheck', false);
	$.session.set('termAgreeCheck', false);
});

$("#submitNewAccount").click(function (){
	"use strict";
	// Make sure all true
	var emailCheck = $.session.get('emailCheck');
	var passwordCheck = $.session.get('passwordCheck');
	var retypeCheck = $.session.get('retypeCheck');
	var termAgreeCheck = $.session.get('termAgreeCheck');
	
	if (emailCheck === true && passwordCheck === true && retypeCheck === true && termAgreeCheck === true){
		var registrationForm = $("#registerNewAccount").serialize();
		$.post(getURL() + "page/user/register/controller/registerController.php",registrationForm,function(result){
			$("#registerStatus").html(result);
		});
	}else if(termAgreeCheck != true){
		BootstrapDialog.show({
			message: 'By sign up you must accept term and condition.',
			title: 'REQUIREMENT',
			type: BootstrapDialog.TYPE_WARNING
		});
	}else{
		BootstrapDialog.show({
			message: 'Please correct the form before submit',
			title: 'ERROR',
			type: BootstrapDialog.TYPE_DANGER
		});
	}
});

// Need finish on tomorrow
$("#inputEmail").on("keyup",function (e){
	"use strict";
	var value = $("#inputEmail").val();
	if (regexCheck(emailPattern(),value))
	{
		$("#inputEmailStatus").parent().removeClass("has-error").addClass("has-success");
		$("#inputEmailStatus").html('<i class="fa fa-check" aria-hidden="true"></i> Correct');
		$.session.set('emailCheck', true);
	}else{
		$("#inputEmailStatus").parent().addClass("has-error").removeClass("has-success");
		$("#inputEmailStatus").html('<i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Incorrect type of email. Ex: xxx@xxx.com');
		$.session.set('emailCheck', false);
	}
		
});

$("#inputPassword").on("keyup",function (e){
	"use strict";
	var typePassword = $("#inputPassword").val();
	if (regexCheck(passwordPattern(),typePassword) && regexCheck(passwordComplexPattern(), typePassword)){
		$("#inputPasswordStatus").parent().removeClass("has-error").addClass("has-success");
		$("#inputPasswordStatus").html('<i class="fa fa-check" aria-hidden="true"></i> Correct');
		$.session.set('passwordCheck', true);
	}else if(!regexCheck(passwordPattern(),typePassword)){
		$("#inputPasswordStatus").parent().addClass("has-error").removeClass("has-success");
		$("#inputPasswordStatus").html('<i class="fa fa-exclamation-triangle" aria-hidden="true"></i> The password should not content any space or quotation mark. Ex not: "' + "'");
		$.session.set('passwordCheck', false);
	}else{
		$("#inputPasswordStatus").parent().addClass("has-error").removeClass("has-success");
		$("#inputPasswordStatus").html('<i class="fa fa-exclamation-triangle" aria-hidden="true"></i> The password should contain at least one letter, one capital letter, one symbol and one number. Ex: tEst!23');
		$.session.set('passwordCheck', false);
	}
});


$("#inputRetypePassword, #inputPassword").on("keyup",function (e){
	"use strict";
	var typePassword = $("#inputPassword").val();
	var retypePassword = $("#inputRetypePassword").val();
	if (regexCheck(matching2WordsPattern(retypePassword),typePassword) && regexCheck(passwordComplexPattern(), retypePassword))
	{
		$("#inputRetypePasswordStatus").parent().removeClass("has-error").addClass("has-success");
		$("#inputRetypePasswordStatus").html('<i class="fa fa-check" aria-hidden="true"></i> Correct');
		$.session.set('retypeCheck', true);
	}else{
		$("#inputRetypePasswordStatus").parent().addClass("has-error").removeClass("has-success");
		$("#inputRetypePasswordStatus").html('<i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Re-typed password does not match');
		$.session.set('retypeCheck', false);
	}
});

$('#registerRuleAccepted').on("click", function (){
	if($('#registerRuleAccepted').prop('checked')) {
    $.session.set('termAgreeCheck', true);
	} else {
		$.session.set('termAgreeCheck', false);
	}
})





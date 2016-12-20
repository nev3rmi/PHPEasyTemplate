// JavaScript Document
$(function() {
  // Handler for .ready() called.
  	$.session.set('emailCheck', false);
	$.session.set('passwordCheck', false);
	$.session.set('retypeCheck', false);
	$.session.set('termAgreeCheck', false);
	// Load Tooltips
	$('#inputEmail').tooltip(
	{
		'trigger':'focus', 
		'title': 'Example: xxx@xxx.com',
		'html': true
	});
	$('#inputPassword').tooltip(
	{
		'trigger':'focus', 
		'title': '<kbd>The password should contain <br>at least:</kbd><br> 8 charaters<br> 1 letter <br> 1 capital letter <br> 1 symbol <br> 1 number <br><br> Example: 1234ASDd5!',
		'html': true
	});
	$('#inputRetypePassword').tooltip(
	{
		'trigger':'focus', 
		'title': 'Re-type the password again!',
		'html': true
	});
	
});

$("#submitNewAccount").click(function (){
	"use strict";
	// Make sure all true
	var emailCheck = $.session.get('emailCheck');
	var passwordCheck = $.session.get('passwordCheck');
	var retypeCheck = $.session.get('retypeCheck');
	var termAgreeCheck = $.session.get('termAgreeCheck');
	if(emailCheck === "false"){
		BootstrapDialog.show({
			message: 'Please input and correct the email field.',
			title: 'REQUIREMENT',
			type: BootstrapDialog.TYPE_WARNING
		});
		return false;
	}
	if(passwordCheck === "false"){
		BootstrapDialog.show({
			message: 'Please input and correct the password field.',
			title: 'REQUIREMENT',
			type: BootstrapDialog.TYPE_WARNING
		});
		return false;
	}
	if(retypeCheck === "false"){
		BootstrapDialog.show({
			message: 'Please correct re-type the password field.',
			title: 'REQUIREMENT',
			type: BootstrapDialog.TYPE_WARNING
		});
		return false;
	}
	if(termAgreeCheck === "false"){
		BootstrapDialog.show({
			message: 'By sign up you must accept term and condition.',
			title: 'REQUIREMENT',
			type: BootstrapDialog.TYPE_WARNING
		});
		return false;
	}
	if (emailCheck === "true" && passwordCheck === "true" && retypeCheck === "true" && termAgreeCheck === "true"){
		// Script run
		var registrationForm = $("#registerNewAccount").serialize();
		$.post(getURL() + "page/user/register/partialview/validateAndAddUser.php",registrationForm,function(result){
			$("#registerStatus").html(result);
		})
		  return true;
	}else{
		BootstrapDialog.show({
			message: 'Unknown Error, please contact administrator to get help!',
			title: 'ERROR',
			type: BootstrapDialog.TYPE_DANGER
		});
		return false;
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
	var countPassword = typePassword.length;
	if (countPassword >= 8){
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
			$("#inputPasswordStatus").html('<i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Incorrect password typed. Ex: tEst!23');
			$.session.set('passwordCheck', false);
		}
	}else{
		$("#inputPasswordStatus").parent().addClass("has-error").removeClass("has-success");
		$("#inputPasswordStatus").html('<i class="fa fa-exclamation-triangle" aria-hidden="true"></i> The password should be at least 8 characters.');
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





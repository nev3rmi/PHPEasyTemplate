// JavaScript Document
function loadLoginNavigationBar(){
	$.post(getURL() + 'page/_layout/body/partialview/_afterlogin.php',{},function (result){
		$("#userNavbar").html(result);	
	});
	return;
}
function loadBeforeLoginNavigationBar(){
	$.post(getURL() + 'page/_layout/body/partialview/_beforelogin.php',{},function (result){
		$("#userNavbar").html(result);	
	});
	return;
}
function submitForm(formID){
	var formValue = $(formID).serialize();
	$.post(getURL() + "page/user/login/controller/validatelogin.php",formValue,function (result){
		$("#loginMessage").html(result);
	});
	return;
}

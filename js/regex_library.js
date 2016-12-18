// JavaScript Document
function regexCheck(regexPattern, inputString){
  "use strict";	
  return (regexPattern.test(inputString))?true:false;
}

function emailPattern(){
	"use strict";
	const emailPattern = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
	return emailPattern;
}

/*function passwordPattern(){
	const passwordPattern = //;
	return passwordPattern;
}
*/
function matching2WordsPattern(inputString){
	"use strict";
	const matching2WordsPattern = new RegExp("^("+inputString+")$");
	return matching2WordsPattern;
}

function passwordPattern(){
	"use strict";
	const passwordPattern = /^[^\s\"\']+$/;
	return passwordPattern;
}

function passwordComplexPattern(){
	"use strict";
	const passwordComplexPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*(_|[^\w])).+$/;
	return passwordComplexPattern;
}

function textPattern(){
	"use strict";
	const textPattern = /^[\d\w\s\!\@\#\$\%\^\&\*\(\)\+\-\.\_\-]+$/;
	return textPattern;
}
	
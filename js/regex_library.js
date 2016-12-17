// JavaScript Document
function regexCheck(regexPattern, inputString){
  /*
	regexPattern = new RegExp(regexPattern);

  //console.log(regexPattern);

  if (regexPattern.test(inputString)){

  	return true;

  }

  return false;	
  
	const regexPattern = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
const inputString = `nev3rmi@gmail.com`;
*/
	// Need Fix
	let test;

	console.log(regexPattern);
	if ((test = regexPattern.exec(inputString)) !== null) {
		// The result can be accessed through the `m`-variable.
		test.forEach((match, groupIndex) => {
			"use strict";
			console.log(`Found match, group ${groupIndex}: ${match}`);
		});
		return true;
	}else{
		return false;
	}	
}

function emailPattern(){
	"use strict";
	var emailPattern = '^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$';
	return emailPattern;
}

	
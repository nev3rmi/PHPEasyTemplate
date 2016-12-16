// JavaScript Document
function regexCheck(regexPattern, string){
  regexPattern = new RegExp(regexPattern);
  console.log(regexPattern);
  if (regexPattern.test(string)){
  	return true;
  }
  return false;	
}
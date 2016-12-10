Current Version: 2.0.0.0 Alpha

Consider to update:

	Get absolute path file: NOT YET
		substr(__FILE__, 0, -strlen($_SERVER['SCRIPT_NAME']));

	Rewrite URL: DONE
		$URL/page/about
		to 
		$URL/about
		
		Multiple mini project in big project:
			Original: $URL/page/User/.../
			User/register/
			User/login/
			User/forgotpassword/
			User/viewdetails/
			User/index = User/

	Auto get CSS and JS in folder: DONE 1st Level
		Need: Create a function

	Auto Active Link in Navbar: DONE 1st Level
		Finish auto active link in first level page: Index, Contact, About
		Unfinish: Product > Viewdetail
		Need: 
			- Do loop to get all level of directory and go backward to active link.
			- Or: Find the url and active the link if inside link active outside do to apply on CSS.

***********************************************************************
Task:
	

	Planning Tomorrow Task:
		- Finish wiki for create new page, need to check the logic again.	
	
	Daily Task:	

	
***********************************************************************
****************This website template made by NeV3RmI******************
***********************************************************************

1/ Config

FOR RMIT Student:
Open File and change all the s3515215 to your student number

2/ Config MySQL

Open setting/mysql_config.php

Notice this template use [mysqli] not [mysql]

3/ Enable

JS

+ Bootstrap
+ Jquery

PHP
 
+ Connect MySQL
+ All link to one

Other Feature

+ GZIP Compression
+ .htaccess
+ .htpasswd
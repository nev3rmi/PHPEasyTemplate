Consider to update:

	Get absolute path file:
		substr(__FILE__, 0, -strlen($_SERVER['SCRIPT_NAME']));

	Rewrite URL:
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

***********************************************************************
****************This website template made by NeV3RmI******************
***********************************************************************

1/ Config

Open File and change all the s3515215 to your student number

2/ Config MySQL

Open setting/mysql_config.php

Notice this template use [mysqli] not [mysql]

3/ Enable

JS

+ Bootstrap
+ Jquery

PHP
+ 
+ Connect MySQL
+ All link to one

Other Feature
+ GZIP Compression
+ .htaccess
+ .htpasswd
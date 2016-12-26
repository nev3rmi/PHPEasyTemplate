<?php
class LoggedUser{
	var $username = $_SESSION['username'];
	var $password = $_SESSION['password'];
	
	function GetUserInformation($username, $password){
		return ;
	}
}
?>
<?php
// Input Username, Password => get Id, Role, Group, And All Type Of Information, Create cookie for it base on Id All information must be encrypted

/*function GetUserInformation($userName, $password){
	
}


// Get Page Id
$getPage = mysqli_query($connect5,"SELECT * FROM `Pages` WHERE `PageURL` = '".$urlc_s."'");
if (mysqli_num_rows($getPage) == 1){
while($r = mysqli_fetch_array($getPage)){
	$pageId = $r['Page_Id'];
}
}else{
	$pageId = "Cannot get page Id";
	debugToConsole($pageId);
}

// Get User permission
$getUserPermission = mysqli_query($connect5, "
SELECT * FROM `ActionPermission` 
WHERE `UserId` = '".$_SESSION['id']."'
AND `PageId` = '".$page[0]."'
");
$countUser = mysqli_num_rows($getUserPermission);

if ($countUser == 1){
	while ($r = mysqli_fetch_array($getUserPermission)){
		
	}
}else{
	echo "You don't have permission to view this page.";
}

/*
$userId = $_SESSION['id'] or 0;
$groupId = $_SESSION['groupId'] or 0;
$pageId = $page[0];
$isSuperUser = set variable;
$permissionName = set variable(read,write) or 0;
*/
/*
function IfThisUserCan($permissionName, $userId, $groupId, $pageId, $isSuperUser){
	if (/* Match All ){*/
/*		return true;
	}
	return false;
}


/*
4 main table

Group
`GroupId`


User
`UserId`

Permission
`PermissionId`
	=> 0 => All
`PermissionName`
	=> Edit, Update, Delete, etc

Page
`PageId`
`PageURL`



1 Join Table

`ActionPermission`

`ActPId`
`UserId` 
`GroupId`
`PageId`
`PermissionId`
`IsSuperUser` 
`ActPAvailable`

Game Rules:

UserId == 0 && GroupId == 0 && IsSuperUser == 0 
=> Everyone can do the acction PerId for PageId

UserId == 0 && GroupId == n && IsSuperUser == 0 
=> Only People in group have permisison can do the acction PerId for PageId

UserId == n && GroupId == 0 && IsSuperUser == 0 
=> Only that person can do the action in PerId for PageId

UserId == n && GroupId == n && IsSuperUser == 0 
=> This person in group can set this type of permission to the other people in group

// Can Set permission to another

UserId == n && GroupId == n && IsSuperUser == 1 
=> This person can set this type of permission to all people in this page

UserId == 0 && GroupId == n && IsSuperUser == 1 
=> This group can set this type of permission to all another people in this page

UserId == n && GroupId == 0 && IsSuperUser == 1
=> This person can set permission to all another people in this site

UserId == 0 && GroupId == 0 && IsSuperUser == 1
=> This page doesn't have permission to do or in develop



///// Notice

Need to check full URL










*/
?>
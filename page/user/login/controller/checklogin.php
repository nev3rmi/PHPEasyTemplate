<?php include_once realpath($_SERVER["DOCUMENT_ROOT"])."/setting/config.php"; ?>
<?php
for ($x = 0; $x < 2; $x++){
	$v[$x] = htmlentities($_POST['v'.$x], ENT_QUOTES, "UTF-8");
}
emailValid($v[0]);
passwordValid($v[1]);
if ($encrypt_u_pwd_s == 1){
	$v[1] = creatingHash_Nev($v[1]);
	$u[0] = mysqli_query($connect5, "SELECT * FROM `user` WHERE `u_email`='".$v[0]."' AND `u_level` < '1000000'");
	$u[1] = mysqli_num_rows($u[0]);
	if ($u[1] == 1){
		while($r = mysqli_fetch_array($u[0])){
			$pwd = $r['u_password'];
			$phn = $r['u_mphone'];
			$lel = $r['u_level'];
			$uid = $r['u_id'];
			$ugr = $r['gr_id'];
		}
	}else{
		echo 'Whether you has been banned or this account has been deleted!';
		exit();
	}
	if (validUserandPassin($v[1],$pwd) == true){
		$_SESSION['uema'] = $v[0];
		$_SESSION['upws'] = $v[1];
		$_SESSION['uphn'] = $phn;  
		$_SESSION['uuse'] = explode('@',$_SESSION['uema']); 
		$_SESSION['ulev'] = $lel;
		$_SESSION['uid'] = $uid;
		$_SESSION['grid'] = $ugr;
		echo '<script type="text/javascript">
		$("#formGetin").load("'.$url_s.'page/mainpage/insideloginbox.php");
		$("#alreadylogin").load("'.$url_s.'page/mainpage/insidenavigation.php");
		$("#loadingPage").load("'.$url_s.'page/mainpage/index/insided.php");
		$("#messageInfo").html("");
		</script>';
		exit();
	}else{
		echo 'Email/password is invalid';
		exit();		
	}
}else{
	$v[1] = creatingHash_Nev($v[1]);
	// Check user
	$u[0] = mysqli_query($connect5, "SELECT * FROM `user` WHERE `u_email`='".$v[0]."' AND `u_password`='".$v[1]."' AND  `u_level` < '1000000'");
	$u[1] = mysqli_num_rows($u[0]);
	if ($u[1] == 1){
		while($r = mysqli_fetch_array($u[0])){
			$_SESSION['ulev'] = $r['u_level'];
			$_SESSION['uid'] = $r['u_id'];
			$_SESSION['grid'] = $r['gr_id'];
		}
			$_SESSION['uema'] = $v[0];
			$_SESSION['upws'] = $v[1]; 
			$_SESSION['uuse'] = explode('@',$_SESSION['uema']); 
		echo '<script type="text/javascript">
		$("#formGetin").load("'.$url_s.'page/mainpage/insideloginbox.php");
		$("#alreadylogin").load("'.$url_s.'page/mainpage/insidenavigation.php");
		$("#loadingPage").load("'.$url_s.'page/mainpage/index/insided.php");
		$("#messageInfo").html("");
		</script>';
		exit();
	}else{
		echo 'Email/password is invalid';	
		exit();
	}
}
?>

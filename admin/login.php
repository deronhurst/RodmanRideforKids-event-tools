<?php
require_once('config.php');

$_POST['user'] = addslashes($_POST['user']);
$_POST['pass'] = $_POST['pass'];


if ( ($_POST['user']!=ADMIN_USER && $_POST['pass']==ADMIN_PASS) || !$_POST['user']) {
	?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html>
<head>
	<title>Login</title>
</head>
<body>
	<div style="height:45px;">

	</div>
	<form action="<?php echo $_SERVER[PHP_SELF];?>" method="post">
		<table cellpadding="5" cellspacing="0" border="0">
			<tr>
				<th colspan="2" align="left">Rodman Ride for Kids - Admin Login</th>
			</tr>
			<tr>
				<td>Username:</td>
				<td><input type="text" name="user" id="username"></td>
			</tr>
			<tr>
				<td>Password:</td>
				<td><input type="password" name="pass" id="password"></td>
			</tr>
			<tr>
				<td></td>
				<td><input type="submit" value="Login"></td>
			</tr>
		</table>
	</form>
</body>
</html>

	<?php

}else{

	session_start();

	$_SESSION['user'] = $_POST['user'];
	$_SESSION['pass'] = $_POST['pass'];

	header('Location: index.php');
	exit;
} 
?>
<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['username'] == '') {
	header("Location:loginform.php");
}		
?>

<!DOCTYPE html>

<html>
<head>

<title>Account management</title>
<style>
/*<![CDATA[*/
* {
	font-family:Arial;
	font-size:100%;
}
/*]]>*/
</style>
</head>

<body>

<?php include("menu.php"); ?>

<?php
$username = $_SESSION['username'];
?>


<div style="width: auto; height: auto; max-width: 100%">

<h1>Account management</h1>

<?php
if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
	$ip = $_SERVER['HTTP_CLIENT_IP'];
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
	$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else {
	$ip = $_SERVER['REMOTE_ADDR'];
}
printf ("<P>You are logged in from IP address %s</p>", $ip);
?>

<h2>Password change</h2>

<p>Passwords must be at least 8 characters long and contain a mix of uppercase, lowercase and numerics.</p>

<form name="changepassword" action="changepassword.php" method="post">
<table>
<tr><td>Username</td><td><input type="text" name="username"></td></tr>
<tr><td>Existing password</td><td><input type="password" name="oldpassword"></td></tr>
<tr><td>New password</td><td><input type="password" name="newpassword1"></td></tr>
<tr><td>Repeat new password</td><td><input type="password" name="newpassword2"></td></tr>
<tr><td><input type="submit" name="submit" value="Update" style="box-shadow: 5px 5px 5px #7f7f7f;"></td></tr>
</table>

<?php
if(isset($_GET['error']))
{
	$error = $_GET['error'];
	switch ($error) {
    	case "nomatch":
        	echo "<p><b>New passwords did no match.</b></p>";
        	break;
    	case "failed":
        	echo "<p><b>Password update failed.</b></p>";
        	break;
    	case "success":
        	echo "<p><b>Password updated.</b></p>";
        	break;
	case "authentication":
		echo "<p><b>Authentication failed</b></p>";
		break;
	case "weak":
		echo "<p><b>Password is too weak, choose another</b></p>";
		break;
	}
}
?>

</div>
</body>
</html>

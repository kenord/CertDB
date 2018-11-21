<!DOCTYPE html>

<html>
<head>

<title>Login page</title>
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

<div style="width: auto; height: auto; max-width: 100%">

<h1>Login</h1>

<p>This is a secure system. You must not access this service if you do not have approval from IS Security. Activity on this system is logged and monitored and will be used to investigate and prosecute anyone responsible for unauthorised activity</p>

<P>To request access or for help please contact <a href="mailto:kenord@gmail.com?subject=Certificate%20survey">Kenneth Ord</a>.</p>

<form name="login" action="login.php" method="post">
<table>
<tr><td>Username</td><td><input type="text" name="username"></td></tr>
<tr><td>Password</td><td><input type="password" name="password"></td></tr>
<tr><td><input type="submit" name="submit" value="Login"></td></tr>
</table>

<?php
if(isset($_GET['error']))
{
	$error = $_GET['error'];
	switch ($error) {
    	case "incorrect":
        	echo "<p><b>Incorrect credentials.</b></p>";
        	break;
	}
}
?>
		

</div>
</body>
</html>

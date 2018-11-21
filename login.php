<?php
session_start();

include("opendb.php");

if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
	$ip = $_SERVER['HTTP_CLIENT_IP'];
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
	$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else {
	$ip = $_SERVER['REMOTE_ADDR'];
}

$sqlloginattempt = "INSERT into t_loginattempts (username, password, clientip, date, success) values ($1, $2, $3, $4, $5)";

// Did we get a username and password?
if(count($_POST) > 0) {
	
	$username = $_POST["username"];
	$password = $_POST["password"];
	
	// Are either the username or password emptry?
	if ($username == '' OR $password == '') {
		// Return to login form
		header("Location:loginform.php");
	}		

	// Get data for this user
	$sqlgetpasswordhash = "SELECT passwordhash FROM t_users WHERE username = $1";

	$result = pg_query_params($db, $sqlgetpasswordhash, array($username));

	// Did the query return any results?
	if(pg_num_rows($result) > 0) {
		$row = pg_fetch_row($result);
		$passwordhash = $row[0];
		if(password_verify($password, $passwordhash)) {
			// Account verified, set the session
			// Set session
			$_SESSION["username"] = $username;

			// Record the login attempt as a success
			$result = pg_query_params($db, $sqlloginattempt, array($username, 'secret', $ip, 'now', 't'));

			// Go to account page
			header("Location:alldata.php");
		}
		else {
			$result = pg_query_params($db, $sqlloginattempt, array($username,'secret', $ip, 'now', 'f'));
			// Verification failed, return to login
			header("Location:loginform.php?error=incorrect");
		}	
	}
	else {
		$result = pg_query_params($db, $sqlloginattempt, array($username, 'secret', $ip, 'now', 'f'));
		// No matching user found
		header("Location:loginform.php?error=incorrect");
	}
}
else {
	$result = pg_query_params($db, $sqlloginattempt, array($username, 'secret', $ip, 'now', 'f'));
	// Didn't get a username and password
	header("Location:loginform.php?error=incorrect");
}

?>


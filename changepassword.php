<?php
session_start();

include("opendb.php");

// Did we get a username and password?
if(count($_POST) > 0) {
	
	$username = $_POST["username"];
	$oldpassword = $_POST["oldpassword"];
	$newpassword1 = $_POST["newpassword1"];
	$newpassword2 = $_POST["newpassword2"];
	
	// Are any of the values empty?
	if ($username == '' OR $oldpassword == '' OR $newpassword1 == '' OR $newpassword2 == '') {
		// Return to login form
		header("Location:account.php?error=failed");
	}		

	// Do the new passwords match?
	if (strcmp($newpassword1, $newpassword2) !== 0) {
		header("Location:account.php?error=nomatch");
		exit(0);
	}

	// Passwords match, check strength
	if (strlen($newpassword1) < 8) {
		// Password too short
		header("Location:account.php?error=weak");
		exit(0);
	}
	if (strtoupper($newpassword1) == $newpassword1) {
		// Password all uppercase
		header("Location:account.php?error=weak");
		exit(0);
	}
	if (strtolower($newpassword1) == $newpassword1) {
		// Password all lowercase
		header("Location:account.php?error=weak");
		exit(0);
	}
		

	// Get data for this user
	$sqlgetpasswordhash = "SELECT passwordhash FROM t_users WHERE username = $1";

	$result = pg_query_params($db, $sqlgetpasswordhash, array($username));

	// Did the query return any results?
	if(pg_num_rows($result) > 0) {
		$row = pg_fetch_row($result);
		$passwordhash = $row[0];
		if(password_verify($oldpassword, $passwordhash)) {
			// Change the password
			$passwordhash = password_hash ($newpassword1, PASSWORD_DEFAULT);
			$sqlsetpasswordhash = "UPDATE t_users SET passwordhash = $1 WHERE username = $2";
			$result = pg_query_params($db, $sqlsetpasswordhash, array($passwordhash, $username));
			header("Location:account.php?error=success");
			exit(0);
		}
		else {
			// Verification failed, return to login
			header("Location:account.php?error=authentication");
			exit(0);
		}	
	}
	else {
		// No matching user found
		header("Location:account.php?error=authentication");
		exit(0);
	}
}
else {
	// Didn't get all values
	header("Location:account.php?error=failed");
	exit(0);
}

?>


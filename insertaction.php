<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['username'] == '') {
	header("Location:loginform.php");
}		
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
<meta name="generator" content="HTML Tidy for Linux (vers 25 March 2009), see www.w3.org">

<title>Insert action</title>
</head>

<body>

<?php include("opendb.php"); ?>

<?php
$actionuser = $_POST["actionuser"];
$actionip = $_POST["actionip"];
$actiontext = $_POST["actiontext"];

// Trim leading and trailing spaces
$actionuser = trim($actionuser);
$actionip = trim($actionip);
$actiontext = trim($actiontext);

// Test if the IP is valid
if (!filter_var($actionip, FILTER_VALIDATE_IP)) {
	header("Location:" . $_SERVER["HTTP_REFERER"]);
}

// Strip dangerous characters
$actionuser = filter_var($actionuser, FILTER_SANITIZE_STRING);
$actiontext = filter_var($actiontext, FILTER_SANITIZE_STRING);

// Prepare statement
$sqlsubmitaction = "INSERT INTO t_actions (actionuser, targetip, action, datetime) VALUES ($1, $2, $3, $4)";

//$result = pg_query($sqlsubmitaction);

// Param statement
$result = pg_query_params($db, $sqlsubmitaction, array($actionuser, $actionip, $actiontext, 'now'));

header("Location:" . $_SERVER["HTTP_REFERER"]);
?>

</body>
</html>

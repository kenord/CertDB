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

<title>Insert certificate action</title>
<style type="text/css">
* {
	font-family:Arial;
	font-size:98%;
}
</style>
</head>

<body>

<?php include("opendb.php"); ?>

<?php
$actionid = $_GET["actionid"];
settype($actiondid, 'integer');

// Prepare statement
$sqldeleteaction = "DELETE FROM t_actions where tablekey = $1";

//$result = pg_query($sqldeleteaction);

// Param statement
$result = pg_query_params($db, $sqldeleteaction, array($actionid));

header("Location: " . $_SERVER["HTTP_REFERER"]);
?>

</body>
</html>

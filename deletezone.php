<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['username'] == '') {
	header("Location:loginform.php");
}		
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>

<title>Delete zone</title>
</head>

<body>

<?php include("opendb.php"); ?>

<?php
$zoneid = $_GET["zone"];

// Prepare statement
$sqldeletezone = "DELETE FROM t_skydomains WHERE domain = $1";

// Param statement
$result = pg_query_params($db, $sqldeletezone, array($zoneid));


header("Location: " . $_SERVER["HTTP_REFERER"]);
?>

</body>
</html>




<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['username'] == '') {
	header("Location:loginform.php");
}		
?>

<!DOCTYPE html>

<html>
<head>

<title>Delete IP address/hostname</title>
<style type="text/css">
* {
	font-family:Arial;
	font-size:98%;
}
</style>
</head>

<body>

<?php include ("opendb.php"); ?>

<?php
$ipaddr = $_GET["ipaddr"];

// Prepare query
$sqldeleteipaddrhostname = "DELETE FROM t_manuallookups WHERE ipaddr = $1";

// Parameterise statement
$result = pg_query_params($db, $sqldeleteipaddrhostname, array($ipaddr));

header("Location: " . $_SERVER["HTTP_REFERER"]);
?>

</body>
</html>

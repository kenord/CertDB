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
$iprange = $_GET["iprange"];

// Prepare statement
$sqldeleteiprange = "DELETE FROM t_gbranges WHERE iprange = $1";

// Param statement
$result = pg_query_params($db, $sqldeleteiprange, array($iprange));


header("Location: " . $_SERVER["HTTP_REFERER"]);
?>

</body>
</html>




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

<title>Insert Sky zone</title>

</head>

<body>

<?php include ("opendb.php"); ?>

<?php
$zoneid = $_POST["zoneid"];

$zoneid = trim($zoneid);
$zoneid = filter_var($zoneid, FILTER_SANITIZE_STRING);

// Prepare query
$sqlsubmitzone = "INSERT INTO t_skydomains (domain) VALUES ($1)";

// $result = pg_query($sqlsubmitzone);

// Parameterise statment
$result = pg_query_params($db, $sqlsubmitzone, array($zoneid));

header("Location: " . $_SERVER["HTTP_REFERER"]);
?>

</body>
</html>

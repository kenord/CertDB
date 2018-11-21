<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['username'] == '') {
	header("Location:loginform.php");
}		
?>

<!DOCTYPE html>

<html>
<head>
<title>Insert IP address/hostname</title>
</head>

<body>

<?php include ("opendb.php"); ?>

<?php
if(isset($_POST["ipaddr"])) {
	$ipaddr = $_POST["ipaddr"];
	$hostname = $_POST["hostname"];
}
else {
	$ipaddr = $_GET["ipaddr"];
	$hostname = $_GET["hostname"];
}

// Check valid IP address
if(!filter_var($ipaddr, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
	header("Location:" . $_SERVER["HTTP_REFERER"]);
	exit();
}


// Trim and sanitise input
$hostname = trim($hostname);
$hostname = filter_var($hostname, FILTER_SANITIZE_STRING);

// Prepare query
$sqlinsertipaddrhostname = "INSERT INTO t_manuallookups (ipaddr, hostname) VALUES ($1, $2)";

// $result = pg_query($sqlsubmitzone);

// Parameterise statment
$result = pg_query_params($db, $sqlinsertipaddrhostname, array($ipaddr, $hostname));

header("Location: " . $_SERVER["HTTP_REFERER"]);
?>

</body>
</html>

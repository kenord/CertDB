<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['username'] == '') {
	header("Location:loginform.php");
}		
?>

<!DOCTYPE html>

<html>
<head>

<title>Insert target IP</title>

</head>

<body>

<?php include ("opendb.php"); ?>

<?php
$targetip = $_POST["targetip"];

// Test if valid IP
if (!filter_var($targetip, FILTER_VALIDATE_IP)) {
	header("Location:" . $_SERVER["HTTP_REFERER"]);
	exit();
}

// Prepare query
$sqlinserttargetip = "INSERT INTO t_certs (targetip) SELECT $1 WHERE NOT EXISTS (SELECT 1 FROM t_certs WHERE targetip = $2)";

// Parameterise statment
$result = pg_query_params($db, $sqlinserttargetip, array($targetip, $targetip));

header("Location: /certificate.php?targetip=" . $targetip);
?>

</body>
</html>

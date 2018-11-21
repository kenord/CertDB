<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['username'] == '') {
	header("Location:loginform.php");
}		
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>

<title>Insert IP range</title>

</head>

<body>

<?php include ("opendb.php"); ?>

<?php
$iprangeid = $_POST["iprangeid"];

$iprangeid = trim($iprangeid);
$iprangeid = filter_var($iprangeid, FILTER_SANITIZE_STRING);

// Prepare query
$sqlsubmitiprange = "INSERT INTO t_gbranges (iprange) VALUES ($1)";

// $result = pg_query($sqlsubmitiprange);

// Parameterise statment
$result = pg_query_params($db, $sqlsubmitiprange, array($iprangeid));

header("Location: " . $_SERVER["HTTP_REFERER"]);
?>

</body>
</html>

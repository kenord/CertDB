<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
<meta name="generator" content="HTML Tidy for Linux (vers 25 March 2009), see www.w3.org">

<title>Lookup certificate subject DNS A record</title>
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

$sqlgetscanpid = "SELECT pid FROM t_scanstatus";
$result = pg_query($db, $sqlgetscanpid);
$row = pg_fetch_row($result);

# Run scanning script
posix_kill ((int)$row[0], 2);

header("Location: " . $_SERVER["HTTP_REFERER"]);
?>

</body>
</html>

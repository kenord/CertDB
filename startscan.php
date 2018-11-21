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

# Run scanning script
exec ('nohup /var/www/html/scripts/db-zmap-ranges.sh 2>&1 /dev/null &');

header("Location: " . $_SERVER["HTTP_REFERER"]);
?>

</body>
</html>

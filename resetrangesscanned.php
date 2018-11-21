<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
<meta name="generator" content="HTML Tidy for Linux (vers 25 March 2009), see www.w3.org">

<title>Reset ranges scanned</title>
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
$sqlresetrangesscanned = "UPDATE t_gbranges SET scanned = false";
$result = pg_query($db, $sqlresetrangesscanned);

header("Location: " . $_SERVER["HTTP_REFERER"]);
?>

</body>
</html>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>

<title>Get screenshot and store link in DB</title>
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

$targetip = $_GET['targetip'];
$link = "https://$targetip/";
$filelink = "/images/$targetip.png";
$filename = "/var/www/html/images/$targetip.png";

// Delete previous image
// Prepare statement
$sqldelimage = "DELETE FROM t_screenshots WHERE targetip = $1";

// Param statement
$result = pg_query_params($db, $sqldelimage, array($targetip));

// Save screenshot to file
exec("xvfb-run -a --server-args=\"-screen 0, 1024x768x24\" wkhtmltoimage --crop-w 800 --crop-h 800 -f png --enable-javascript $link $filename");

if (file_exists ($filename)) {

	// Prepare statement
	$sqladdimage = "INSERT INTO t_screenshots (targetip, imagelink, testdate) VALUES ($1, $2, $3)";

	// Param statement
	$result = pg_query_params($db, $sqladdimage, array($targetip, $filelink, 'now'));
}

header("Location: " . $_SERVER["HTTP_REFERER"]);
?>

</body>
</html>

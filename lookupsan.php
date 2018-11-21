<!DOCTYPE html>

<html>
<head>
<meta name="generator" content="HTML Tidy for Linux (vers 25 March 2009), see www.w3.org">

<title>Lookup certificate SAN DNS A record</title>
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
$lookupsanname = $_GET["sanname"];


// Prepare statement to delete previous IP addresses for SAN
$sqldelsandns = "DELETE FROM t_sanips WHERE sanname = $1";

// Run param statement
$result = pg_query_params($db, $sqldelsandns, array($lookupsanname));

// Get IP addresses for sanname
$hosts = explode (':', `/usr/bin/dig +noall +answer $lookupsanname | grep -P 'IN\sA' | awk -F" " '{printf "%s:",$5}' | sed 's/:$//'`);

if (empty($hosts[0])) {}
else {
	foreach ($hosts as $ip) {
		$sqladdsandns = "INSERT INTO t_sanips (sanname, sanip) VALUES ('$lookupsanname', '$ip')";
		$result = pg_query($sqladdsandns);
	}
}

header("Location: " . $_SERVER["HTTP_REFERER"]);

?>

</body>
</html>

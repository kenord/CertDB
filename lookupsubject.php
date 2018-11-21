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
$lookupsubject = $_GET["subject"];


// Prepare statement to delete previous IP addresses for subject
$sqldelsubjectdns = "DELETE FROM t_subjectips WHERE certsubject = $1";

// Run param statement
$result = pg_query_params($db, $sqldelsubjectdns, array($lookupsubject));

// Get IP addresses for subject
$hosts = explode (':', `/usr/bin/dig +noall +answer $lookupsubject | grep -P 'IN\sA' | awk -F" " '{printf "%s:",$5}'  | sed 's/:$//'`);

if (empty($hosts[0])) {}
else {
	foreach ($hosts as $ip) {
		$sqlsubjectdns = "INSERT INTO t_subjectips (certsubject, subjectip) VALUES ('$lookupsubject', '$ip')";
		$result = pg_query($sqlsubjectdns);
	}
}

header("Location: " . $_SERVER["HTTP_REFERER"]);

?>

</body>
</html>

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
$lookuptargetip = $_GET["targetip"];


// Prepare statement to delete previous IP addresses for subject
$sqldelptrs = "DELETE FROM t_targetptrs WHERE targetip = $1";

// Run param statement
$result = pg_query_params($db, $sqldelptrs, array($lookuptargetip));

// Get PTRs addresses for target IP
$hosts = explode (':', rtrim(`/usr/bin/dig +noall +answer -x $lookuptargetip | grep -P 'IN\sPTR' | awk -F" " '{print $5 ":"}' | head -c -2`));

if (empty($hosts[0])) {}
else {
	foreach ($hosts as $ptr) {
		$sqladdptrs = "INSERT INTO t_targetptrs (targetip, targetptr) VALUES ('$lookuptargetip', '$ptr')";
		$result = pg_query($sqladdptrs);
	}
}

header("Location: " . $_SERVER["HTTP_REFERER"]);

?>

</body>
</html>

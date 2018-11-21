<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['username'] == '') {
	header("Location:loginform.php");
}		
?>

<!DOCTYPE html>

<html>
<head>
<title>Test all target IPs with sslscan</title>
</head> 

<style>
.progress_wrapper {
	width:360px;
	border:1px solid #ccc;
	position:absolute;
	top:200px;
	left:50%;
	margin-left:-150px
}

.progress {
	height:20px;
	background-color:#00F
}

.progresspc {
	margin-top:-20px;
	height:20px;
	margin-left:300px;
	width:60px;
	background-color:#FF0;
	color:#000
}
</style>

<body>

<?php include("menu.php");?>

<h1>sslscan in progress. Do not interrupt</h1>

<?php include("opendb.php");

$sqlcounttargets = "SELECT count(distinct targetip) FROM t_certs;";
$result = pg_query($sqlcounttargets);
$row = pg_fetch_row($result);
$counttargets = $row[0];

$width 		  = 0;					// starting width
$percentage_num	  = 0;					// starting percentage number
$percentage_bar   = 0;					// starting percentage bar
$total_iterations = $counttargets;			// iterations to perform
$width_per_iteration = 300 / $total_iterations;		// pixels progress div is increased each iteration
$percentage_num_per_iteration = 100 / $total_iterations;		// % in progresspc div increased each iteration

ob_start();
header( 'Content-type: text/html; charset=utf-8' );

$sqlgettargetips = "SELECT distinct targetip FROM t_certs;";
$resultips = pg_query($sqlgettargetips);

while ($row = pg_fetch_row($resultips)) {

//	echo '<div class="progress_wrapper">';
//	echo '	<div class="progress" style="width:' . $width . 'px;"></div>';
//	echo '	<div class="progresspc">' . number_format($percentage_num, 2) . '%' . '</div>';
//	echo '</div><br>';
//	echo str_pad('',4096)."\n";    

	$percentage_num += $percentage_num_per_iteration;
	$width += $width_per_iteration;
	ob_flush();
	flush(); // Both flushes are necessary

	$targetip = $row[0];

	$score = "Pass";	// Set to Fail if any of the following are true
	$weakversions = "f";	// Set true later if SSLv2 or SSLv3 found
	$weakkeys ="f";		// Set true later if 40 or 56 bit keys found
	$weakciphers = "f";	// Set true later if Export or Anonymous cipher found
	$weakrc4 = "t";		// Set false later if a non-RC4 cipher found

	$xmlfile = "/tmp/sslscan-$targetip.xml";
	$jsonfile = "/tmp/sslscan-$targetip.json";

	// sslscan the targetip and convert XML results to JSON
	$cmd = "sslscan --no-heartbleed --ipv4 --no-renegotiation --no-compression --xml=$xmlfile $targetip; xml2json < $xmlfile > $jsonfile; /bin/rm -f $xmlfile"; 
	$result = exec ($cmd);

	// Look for SSLv2 or SSLv3 protocol support
	$cmd = "jq -r '.document.ssltest.cipher[].sslversion' $jsonfile | head -c -1";
	$result = shell_exec ($cmd);			// Use shell_exec because returns multiple lines
	$lines = explode (PHP_EOL, $result);
	foreach ($lines as $sslversion) {
		if ($sslversion == 'SSLv2' OR $sslversion == 'SSLv3') {
			$weakversions = "t";		// Found an SSlv2 or SSLv3 cipher
			break;				// Leave the loop
		}
	}

	// Look for 40 or 56 bit key sizes
	$cmd = "jq -r '.document.ssltest.cipher[].bits' $jsonfile | head -c -1";
	$result = shell_exec ($cmd);
	$lines = explode (PHP_EOL, $result);
	foreach ($lines as $sslbits) {
		if ($sslbits == '40' OR $sslbits == '56') {
			$weakkeys = "t";		// Found a 40 or 56 bit key
			break;				// Leave the loop
		}
	}

	// Look for Anonymous or Export ciphers
	$cmd = "jq -r '.document.ssltest.cipher[].cipher' $jsonfile | head -c -1";
	$result = shell_exec ($cmd);
	$lines = explode (PHP_EOL, $result);
	foreach ($lines as $sslcipher) {
		if (substr($sslcipher,0,3) == 'EXP' OR substr($sslcipher,0,5) == 'AECDH') {
			$weakciphers = "t";		// Found an Export or Anonymous cipher
			break;				// Leave the loop
		}
	}

	// Look for just RC4 ciphers
	$cmd = "jq -r '.document.ssltest.cipher[].cipher' $jsonfile | head -c -1";
	$result = shell_exec ($cmd);
	$lines = explode (PHP_EOL, $result);
	foreach ($lines as $sslcipher) {
		if (strpos ($sslcipher, 'RC4') === FALSE) {
			$weakrc4 = "f";			// Found a non-RC4 cipher
			break;				// Leave the loop
		}
	}

	// Check if Pass or Fail
	if ($weakversions == "t" OR $weakkeys == "t" OR $weakciphers == "t" OR $weakrc4 == "t") {
		$score = "Fail";
	}

	// Remove JSON file
	$cmd = "/bin/rm -f $jsonfile";
	$result = exec ($cmd);

	// Remove old results from DB
	$sqldelsslscan = "DELETE FROM t_sslscan WHERE targetip = '$targetip'";
	$result = pg_query($sqldelsslscan);

	// Add new results to DB
	$sqladdsslscan = "INSERT INTO t_sslscan (targetip, score, testdate, weakversions, weakkeys, weakciphers, weakrc4) VALUES ('$targetip','$score',now(), '$weakversions', '$weakkeys', '$weakciphers', '$weakrc4')";
	$result = pg_query($sqladdsslscan);

	echo '<div class="progress_wrapper">';
        echo '  <div class="progress" style="width:' . $width . 'px;"></div>';
        echo '  <div class="progresspc">' . number_format($percentage_num, 2) . '%' . '</div>';
        echo '</div><br>';
        echo str_pad('',4096)."\n";


}
ob_flush_clean();
header("Location: " . $_SERVER["HTTP_REFERER"]);
?>

</body>
</html>

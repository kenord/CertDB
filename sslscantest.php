<!DOCTYPE html>

<html>
<head>
<title>Test target with sslscan</title>
</head> 

<body>

<?php include("opendb.php");

$score = "Pass";	// Set to Fail if any of the following are true
$weakversions = "f";	// Set true later if SSLv2 or SSLv3 found
$weakkeys ="f";		// Set true later if 40 or 56 bit keys found
$weakciphers = "f";	// Set true later if Export or Anonymous cipher found
$weakrc4 = "t";		// Set false later if a non-RC4 cipher found

$targetip = $_GET["targetip"];
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

header("Location: " . $_SERVER["HTTP_REFERER"]);
?>

</body>
</html>

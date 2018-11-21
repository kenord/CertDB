<!DOCTYPE html>

<html>
<head>
<title>Test target with SSLLabs</title>
</head> 

<body>

<?php include("opendb.php"); ?>

<?php
$targetip = $_GET["targetip"];
$targetname = $_GET["targetname"];

// SSLLabs test the target's hostname
$cmd = "/usr/local/bin/ssllabs-scan --ignore-mismatch $targetname > /tmp/$targetip.json"; 
$result = exec ($cmd);
$cmd = "jq -r '.[].endpoints[] | select (.ipAddress == \"$targetip\") | .gradeTrustIgnored' /tmp/$targetip.json";
$score = exec ($cmd);
$cmd = "/bin/rm -f /tmp/$targetip.json";
$result = exec ($cmd);

// Remove old results from DB
$sqldelssl = "delete from t_ssllabs where targetip = '$targetip'";
$result = pg_query($sqldelssl);

// Add new results to DB
$sqladdssl = "INSERT INTO t_ssllabs (targetip, hostname, score, testdate) VALUES ('$targetip','$targetname','$score',now())";
$result = pg_query($sqladdssl);

header("Location: " . $_SERVER["HTTP_REFERER"]);
?>

</body>
</html>

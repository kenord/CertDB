<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
<meta name="generator" content="HTML Tidy for Linux (vers 25 March 2009), see www.w3.org">

<title>Recheck a specfic IP address</title>
<style type="text/css">
* {
	font-family:Arial;
	font-size:98%;
}
</style>
</head> 

<body>

<?php include("menu.php"); ?>


<?php include("opendb.php"); ?>

<?php
$targetip = $_GET["targetip"];

// Get the certificate from the target in JSON format
$json = `echo $targetip | /home/web02/gocode/bin/zgrab --port 443 --tls --tls-version TLSv1.2 2> /dev/null | grep subject`;

if (empty($json)) {
	$json = `echo $targetip | /home/web02/gocode/bin/zgrab --port 443 --tls --tls-version TLSv1.1 2> /dev/null | grep subject`;
}

if (empty($json)) {
        $json = `echo $targetip | /home/web02/gocode/bin/zgrab --port 443 --tls --tls-version TLSv1.0 2> /dev/null | grep subject`;
}


// Test if a certificate was returned
if ($json == '') {
	// Nothing returned

	// Delete existing entries from single entry JSON table
	$deletejsonfromsingletable = "DELETE FROM t_singlejson";
	$result = pg_query ($deletejsonfromsingletable);

	// Delete existing cert entry from target
	$deletetarget = "DELETE FROM t_certs WHERE targetip = '$targetip'";
	$result = pg_query ($deletetarget);

	// Delete existing SANs from target cert
	$deletesans = "DELETE FROM t_sans WHERE targetip = '$targetip'";
	$result = pg_query ($deletesans);

	header("Location: " . $_SERVER["HTTP_REFERER"]);
}
else {
	// Certificate returned

	// Delete existing entries from single entry JSON table
	$deletejsonfromsingletable = "DELETE FROM t_singlejson";
	$result = pg_query ($deletejsonfromsingletable);

	// Delete existing cert entry from target
	$deletetarget = "DELETE FROM t_certs WHERE targetip = '$targetip'";
	$result = pg_query ($deletetarget);

	// Delete existing SANs from target cert
	$deletesans = "DELETE FROM t_sans WHERE targetip = '$targetip'";
	$result = pg_query ($deletesans);

	// Place JSON into single JSON table
	$insertjsontosingletable = "INSERT INTO t_singlejson (data) values ('$json')";
	$result = pg_query ($insertjsontosingletable);

	// Populate main table with data from single JSON row
	$insertjsonascert = "insert into t_certs (targetip,certsubject,organisation,issuer,startdate,enddate,algorithm,keysize)  (select data->>'ip', regexp_replace(data#>>'{data,tls,server_certificates,certificate,parsed,subject,common_name}', '[\[\]\"]', '', 'g'), regexp_replace(data#>>'{data,tls,server_certificates,certificate,parsed,issuer,common_name}', '[\[\]\"]', '', 'g'), regexp_replace(data#>>'{data,tls,server_certificates,certificate,parsed,issuer,organization}', '[\[\]\"]', '', 'g'), cast(data#>>'{data,tls,server_certificates,certificate,parsed,validity,start}' as timestamp), cast(data#>>'{data,tls,server_certificates,certificate,parsed,validity,end}' as timestamp), data#>>'{data,tls,server_certificates,certificate,parsed,signature,signature_algorithm,name}', cast(data#>>'{data,tls,server_certificates,certificate,parsed,subject_key_info,rsa_public_key,length}' as integer) from t_singlejson)";
	$result = pg_query ($insertjsonascert);

	// Update checked timestamp
	$updatetimestamp = "UPDATE t_certs SET testdate = now() WHERE targetip = '$targetip'";
	$result = pg_query ($updatetimestamp);

	// Extract SANs and insert into SANS table
	$insertsansfromjson = "INSERT INTO t_sans (targetip, certsubject, sanname) SELECT t.data ->> 'ip'::text AS targetip, regexp_replace(t.data #>> '{data,tls,server_certificates,certificate,parsed,subject,common_name}'::text[], '[\[\]\"]'::text, ''::text, 'g'::text) AS certsubject, elem.value AS sanname FROM t_singlejson t, LATERAL jsonb_array_elements_text(t.data #> '{data,tls,server_certificates,certificate,parsed,extensions,subject_alt_name,dns_names}'::text[]) elem(value) ";

	$result = pg_query ($insertsansfromjson);

	header("Location: " . $_SERVER["HTTP_REFERER"]);
}
?>

</body>
</html>

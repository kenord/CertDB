<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['username'] == '') {
	header("Location:loginform.php");
}		
?>

<!DOCTYPE html>

<html>
<head>

<title>About the certificate survey</title>
<style type="text/css">
/*<![CDATA[*/
* {
	font-family:Arial;
	font-size:100%;
}
/*]]>*/
</style>
<script src="/sorttable.js" type="text/javascript"></script>
</head>

<body>
<div style="width: auto; height: auto; max-width: 100%">

<?php include("menu.php"); ?>

<h1>About certificate survey website</h1>

<p>This is an experimental website holding data gathered by a survey of all X.509 digital server certificates in the GB IP address space.</p>

<p>The purpose of the service is to allow discovery and analysis of all certificates supporting, or purporting to support, selected online services.</p>

<p>Please contact <a href="mailto:kenord@gmail.com?subject=Certificate%20survey">Kenneth Ord</a> for more information.</p>

<h2>Using the website</h2>

<h3>Certs and flags view</h3>

<p>The <a href="/">Certs and flags</a> view presents all certificates discovered by the survey. The information presented is:</p>

<ul>
<li>Expired flag - shows if the certificate is past its end date</li>
<li>Expiring flag - shows if the certificate will expire in the next 30 days</li>
<li>Weak flag - shows if the certificate was generated with a weak algorithm or a short key</li>
<li>Subject DNS flag - shows if the certificate subject can be resolved to an IP address through a public DNS A record</li>
<li>Target IP - IP address of the system holding the certificate</li>
<li>Cert subject - subject distinguished name from the certificate</li>
<li>Cert issuer - issuer of the certificate</li>
<li>Cert start date - date and time at which the certificate became valid</li>
<li>Cert end date - date and time at which the certificate validity expired</li>
<li>Cert algorithm - algorithm used to generate the certificate and keys</li>
<li>Cert key size - the size of the public/private key in the certificate</li>
</ul>

<h3>Expired certs view</h3>

<p>The <a href="/expired.php">Expired certs</a> view presents expired certificates.</p>

<h3>Expiring certs view</h3>

<p>The <a href="/expiring.php">Expiring certs</a> view presents certificates which will expire in the next 30 days.All certificate tables can be sorted by clicking on column headings.Target IP addresses in the certificate tables are links to more detailed information about the certificate issued by the target.</p>

<h3>Certificate view</h3>

<p>Individual certificate views show detailed information about the certificate.Warning are presented for expired certificates, those with weak ciphers or keys and where the certificate subject does not have a public DNS A record.Certificate can have multiple Subject Alternative Names and these are displayed under each certificate if any are found in the certificate. The table includes IP addresses resolved by DNS A records for each SAN.</p>
</div>
</body>
</html>

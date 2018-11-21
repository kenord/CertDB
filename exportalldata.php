<?php include ("opendb.php"); ?>

<?php

// create a file pointer connected to the output stream
$output = fopen('/tmp/alldata.csv', 'w');

// output the column headings
fputcsv($output, array('Expired','Expiring','Weak','Subject DNS','SSLLabs','sslscan','Target IP','Cert subject','Cert organisation','Cert issuer','Cert start date','Cert end date','Algorithm','Key size','Action user','Last action text','Action date'));

// Get the data
//$sqlcerts = "SELECT a.expiredflag, a.expiringflag, a.weakflag, a.missingdnsflag, a.score, a.targetip, a.certsubject, a.organisation, a.issuer, a.startdate, a.enddate, a.algorithm, a.keysize, b.actionuser, b.action, b.datetime FROM v_allcertsandflags a left join v_latestactions b on a.targetip = b.targetip";

$sqlcerts = "SELECT a.expiredflag, a.expiringflag, a.weakflag, a.missingdnsflag, a.score, c.score, a.targetip, a.certsubject, a.organisation, a.issuer, a.startdate, a.enddate, a.algorithm, a.keysize, b.actionuser, b.action, b.datetime FROM v_allcertsandflags a LEFT JOIN v_latestactions b on a.targetip = b.targetip LEFT JOIN t_sslscan c on a.targetip = c.targetip";

$result = pg_query($db, $sqlcerts);

while ($row = pg_fetch_row($result)) {
	fputcsv($output, $row);
}

// output headers so that the file is downloaded rather than displayed
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=alldata.csv');
readfile('/tmp/alldata.csv');
header("Location: " . $_SERVER["HTTP_REFERER"]);
?>


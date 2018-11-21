<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['username'] == '') {
	header("Location:loginform.php");
}		
?>

<html>
<head>
<style type="text/css">
/*<![CDATA[*/
* {
	font-family:Arial;
	font-size:100%;
}
/*]]>*/
</style>
<script src="/sorttable.js" type="text/javascript"></script>

<title>Trends</title>

<?php include("opendb.php"); ?>

<?php
// Total unique target IPs
$result = pg_query($db, "select count(distinct targetip) from t_certs");
$row = pg_fetch_row($result);
$totaltargetips = $row[0];

// Total unique certificate subjects
$result = pg_query($db, "select count(distinct certsubject) from t_certs");
$row = pg_fetch_row($result);
$totalcertsubjects = $row[0];

// Total expired certificates
$result = pg_query($db, "select count(targetip) from v_expiredcerts");
$row = pg_fetch_row($result);
$totalexpiredcerts = $row[0];

// Total expiring certificates
$result = pg_query($db, "select count(targetip) from v_expiringcerts");
$row = pg_fetch_row($result);
$totalexpiringcerts = $row[0];

// Total valid certificates
$totalvalidcerts = $totaltargetips - $totalexpiredcerts - $totalexpiringcerts;

// Total sslscan fails
$result = pg_query($db, "select count(*) from t_sslscan where score = 'Pass'");
$row = pg_fetch_row($result);
$totalsslscanpasses = $row[0];

// Total sslscan fails
$result = pg_query($db, "select count(*) from t_sslscan where score = 'Fail'");
$row = pg_fetch_row($result);
$totalsslscanfails = $row[0];

// Total SSLLabs C or better
$result = pg_query($db, "select count(distinct targetip) from t_ssllabs where score = 'A+' OR score = 'A' OR score = 'A-' OR score = 'B' or score = 'c'");
$row = pg_fetch_row($result);
$totalssllabsgood = $row[0];

// Total SSLLabs D or worse
$result = pg_query($db, "select count(distinct targetip) from t_ssllabs where score = 'D' OR score = 'E' OR score = 'F'");
$row = pg_fetch_row($result);
$totalssllabsbad = $row[0];

$sqlinsertstats = "INSERT INTO t_trends(totaltargetips, totalcertsubjects, totalvalidcerts, totalexpiredcerts, totalexpiringcerts, totalsslscanpasses, totalsslscanfails, totalssllabsgood, totalssllabsbad, testdate) VALUES ($totaltargetips, $totalcertsubjects, $totalvalidcerts, $totalexpiredcerts, $totalexpiringcerts, $totalsslscanpasses, $totalsslscanfails, $totalssllabsgood, $totalssllabsbad, now());";
$result = pg_query($db, $sqlinsertstats);

header("Location: " . $_SERVER["HTTP_REFERER"]);
?>

</head>

<body>
</body>
</html>

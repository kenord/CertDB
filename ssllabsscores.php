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

<title>Certificates index</title>

<?php include("menu.php"); ?>
<?php include("opendb.php"); ?>


<H1>SSLLabs HTTPS server scores</H1>

<P>All servers with X.509 certificates from HTTPS/443 servers in UK IP address space</P>
<P>Systems may be untested if a public DNA A record cannot be found for the targetip IP address, or if SSLLabs resuls are not reported for the target IP address.</p>

</div>

<?php
$result = pg_query($db, "select
		(select count(distinct targetip) from t_certs) as targets,
		(select count(distinct targetip) from v_expiredcerts) as expired,
		(select count(distinct targetip) from v_expiringcerts) as expiring");


$row = pg_fetch_array($result);
$total = $row[0];
$expired = $row[1];
$expiring = $row[2];
$good = $total - $expired -$expiring;
$certentry = "['Live certs = $good'," . $good . " ]," .
	"['Expired certs = $expired'," . $expired . "]," .
	"['Expiring certs = $expiring'," . $expiring . "]";
?>

<?php
// SSLLabs results
$result = pg_query($db, "select 
		(select count(distinct targetip) from t_ssllabs where score = 'A+') as scoreaplus,
		(select count(distinct targetip) from t_ssllabs where score = 'A') as scorea,
		(select count(distinct targetip) from t_ssllabs where score = 'A-') as scoreaminus,		
		(select count(distinct targetip) from t_ssllabs where score = 'B') as scoreb,
		(select count(distinct targetip) from t_ssllabs where score = 'C') as scorec,
		(select count(distinct targetip) from t_ssllabs where score = 'D') as scored,
		(select count(distinct targetip) from t_ssllabs where score = 'E') as scoree,
		(select count(distinct targetip) from t_ssllabs where score = 'F') as scoref,
		(select count(distinct targetip) from t_ssllabs where score = '') as scorenodns");

$row = pg_fetch_array($result);
$ssllabsentry = "['A+, exceptional = $row[0]'," . $row[0] . "]," .
	"['A, very good = $row[1]'," . $row[1] . "]," .
	"['A-, good = $row[2]'," . $row[2] . "]," .
	"['B, fair = $row[3]'," . $row[3] . "]," .
	"['C, average = $row[4]'," . $row[4] . "]," .
	"['D, poor = $row[5]'," . $row[5] . "]," .
	"['E, bad = $row[6]'," . $row[6] . "]," .
	"['F, very bad = $row[7]'," . $row[7] . "]," .
	"['Untested = $row[8]'," . $row[8] . "]";
?>

<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">

google.load("visualization", "1", {packages:["corechart"]});
google.setOnLoadCallback(drawChart);

function drawChart() {

	var datassllabs = google.visualization.arrayToDataTable([
			['Category','Count'],
			<?php echo $ssllabsentry ?>
	]);
	var optionsssllabs = {
		title: 'All targets SSLLabs scores', is3D: true, colors:['#009933','#33CC33','#66FF66','#CCFF99','#D2D200','#FF6600','#FF5050','#FF0000','#669999'],
	};

	var chartssllabs = new google.visualization.PieChart(document.getElementById('ssllabspiechart'));
	chartssllabs.draw(datassllabs, optionsssllabs);
}

</script>
</head>

<body>

<div id="ssllabspiechart" style="width: 700px; height: 300px;"></div>

<div>

<h2> Individual SSLLabs scores </h2>

<table border="1" class="sortable">
<tr bgcolor="#AAAAFF">
	<th>Target</th>
	<th>Cert subject</th>
	<th>Score</th>
	<th>Tested</th>
	<th>Delete</th>
</tr>
<?php
$sqlssllabs = "SELECT a.targetip, b.certsubject, a.score, cast(a.testdate as timestamp(0)) as testdate FROM t_ssllabs a LEFT JOIN t_certs b ON a.targetip = b.targetip ORDER BY score";

$result = pg_query($db, $sqlssllabs);

while ($row = pg_fetch_row($result)) {
        printf('<tr bgcolor=white align=center><td><A HREF=/certificate.php?targetip=%s >%s</A></td><td>%s</td><td>%s</td><td>%s</td><td><A HREF=/deletessllabsscore.php?targetip=%s onclick="return confirm(\'Delete %s?\')">X</A></td></tr>', $row[0], $row[0], $row[1], $row[2], $row[3], $row[0], $row[0]);
}
?>

</table>

</div>

</body>
</html>

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

<title>Sky certificates index</title>

<?php include("menu.php"); ?>
<?php include("opendb.php"); ?>


<H1>Sky public X.509 certificate survey</H1>

<P>All Sky X.509 certificates from HTTPS/443 servers in UK IP address space</P>

<div class="centered" style="width: 600px; ">
<?php
$sqlstats = "select (select count(distinct targetip) from t_certs) as totaltargetips,
        (select count(distinct certsubject) from t_certs) as totalsubjects,
        (select count(targetip) from v_expiredcerts) as totalexpired,
        (select count(targetip) from v_expiringcerts) as totalexpiring,
        (select count(targetip) from v_weakflag) as totalweak,
        (select count(targetip) from v_wildcardflags) as totalwildcards";

$result = pg_query($db, $sqlstats);
$row = pg_fetch_row($result);

echo "<table frame=box style=\"background: white;\">";
echo "<tr><td align=right><a href=/alldata.php>Total target IPs</a> = </td><td align=left>" . $row[0] . "</td></tr>";
echo "<tr><td align=right>Total Unique certificates = </td><td align=left>" . $row[1] . "</td></tr>";
echo "<tr><td align=right><a href=/expired.php>IPs with expired certificates</a> = </td><td align=left>" . $row[2] . "</td></tr>";
echo "<tr><td align=right><a href=/expiring.php>IPs with expiring certificates</a> = </td><td align=left>" . $row[3] . "</td></tr>";
echo "<tr><td align=right>IPs with weak certificates = </td><td align=left>" . $row[4] . "</td></tr>";
echo "<tr><td align=right>IPs with wildcard certificates = </td><td align=left>" . $row[5] . "</td></tr>";
echo "</table></p>";

?>

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
		(select count(distinct targetip) from t_ssllabs where score = 'No DNS') as scorenodns");

$row = pg_fetch_array($result);
$ssllabsentry = "['A+ = $row[0]'," . $row[0] . "]," .
	"['A = $row[1]'," . $row[1] . "]," .
	"['A- = $row[2]'," . $row[2] . "]," .
	"['B = $row[3]'," . $row[3] . "]," .
	"['C = $row[4]'," . $row[4] . "]," .
	"['D = $row[5]'," . $row[5] . "]," .
	"['E = $row[6]'," . $row[6] . "]," .
	"['F = $row[7]'," . $row[7] . "]," .
	"['No DNS = $row[8]'," . $row[8] . "]";
?>



<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">

google.load("visualization", "1", {packages:["corechart"]});
google.setOnLoadCallback(drawChart);
function drawChart() {

	var datacert = google.visualization.arrayToDataTable([
			['Category','Count'],
			<?php echo $certentry ?>
	]);
	var optionscert = {
		title: 'All Sky targets and certificates'
	};

	var chartcert = new google.visualization.PieChart(document.getElementById('certpiechart'));
	chartcert.draw(datacert, optionscert);
}

/*function drawChart() {

	var datassllabs = google.visualization.arrayToDataTable([
			['Category','Count'],
			<?php echo $ssllabsentry ?>
	]);
	var optionsssllabs = {
		title: 'All Sky targets SSLLabs scores'
	};

	var chartssllabs = new google.visualization.PieChart(document.getElementById('ssllabspiechart'));
	chartssllabs.draw(datassllabs, optionsssllabs);
}*/

</script>
</head>

<body>

<div id="certpiechart" style="width: 800px; height: 400px;"></div>
</body>
</html>

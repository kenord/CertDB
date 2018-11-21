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
</head>

<title>Certificates index</title>

<script type="text/javascript" src="https://www.google.com/jsapi"></script>

<?php include("menu.php"); ?>
<?php include("opendb.php"); ?>


<H1>Public HTTPS server survey</H1>

<P>HTTPS/443 servers in UK IP address space</P>


</div>

<?php
$sqlcerts2 = "SELECT a.expiredflag, a.expiringflag, a.weakflag, a.missingdnsflag, a.score AS ssllabsscore, c.score AS sslscanscore, a.targetip, a.owner, a.certsubject, a.organisation, a.issuer, a.startdate, a.enddate, a.algorithm, a.keysize, b.actionuser, b.action, b.datetime FROM v_allcertsandflags a LEFT JOIN v_latestactions b on a.targetip = b.targetip LEFT JOIN t_sslscan c on a.targetip = c.targetip";

$result2 = pg_query($db, $sqlcerts2);

$count_all = 0;
$count_expired = 0;
$count_expiring = 0;
$count_live = 0;
$count_aplus = 0;
$count_a = 0;
$count_aminus = 0;
$count_b = 0;
$count_c = 0;
$count_d = 0;
$count_e = 0;
$count_f = 0;
$count_pass = 0;
$count_fail = 0;
$count_none = 0;

while ($row = pg_fetch_row($result2)) {
	// Count all
	$count_all++;
	if ($row[0] == 'EXPIRED') $count_expired++;
	if ($row[1] == 'EXPIRING') $count_expiring++;
	switch($row[4]) {
		case 'A+' : $count_aplus++; break;
		case 'A' : $count_a++; break;
		case 'A-' : $count_aminus++; break;
		case 'B' : $count_b++; break;
		case 'C' : $count_c++; break;
		case 'D' : $count_d++; break;
		case 'E' : $count_e++; break;
		case 'F' : $count_f++; break;
		default  : $count_none++; break;
	}
	if ($row[5] == 'Pass') $count_pass++;
	if ($row[5] == 'Fail') $count_fail++;
}
$count_live = $count_all - $count_expired - $count_expiring;

$certentry =	"['Live certs = $count_live'," . $count_live . " ]," .
		"['Expiring certs = $count_expiring'," . $count_expiring . "]," .
		"['Expired certs = $count_expired'," . $count_expired . "]";

$ssllabsentry =	"['A+, exceptional = $count_aplus'," . $count_aplus . "]," .
		"['A, very good = $count_a'," . $count_a . "]," .
		"['A-, good = $count_aminus'," . $count_aminus . "]," .
		"['B, fair = $count_b'," . $count_b . "]," .
		"['C, average = $count_c'," . $count_c . "]," .
		"['D, poor = $count_d'," . $count_d . "]," .
		"['E, bad = $count_e'," . $count_e . "]," .
		"['F, very bad = $count_f'," . $count_f . "]," .
		"['Untested = $count_none'," . $count_none . "]";

$sslscanentry = "['Pass = $count_pass'," . $count_pass . "]," .
		"['Fail = $count_fail'," . $count_fail . "]";
?>


<?php
// sslscan results
$result = pg_query($db, "select 
		(select count(*) from t_sslscan where score = 'Pass') as scorepass,
		(select count(*) from t_sslscan where score = 'Fail') as scorefail");

$row = pg_fetch_array($result);
$sslscanentry = "['Pass = $row[0]'," . $row[0] . "]," .
	"['Fail = $row[1]'," . $row[1] . "]";
?>

<?php
// sslscan SSLv2 and SSLv3 results
$result = pg_query($db, "select 
		(select count(*) from t_sslscan where weakversions = 't') as versionfail,
		(select count(*) from t_sslscan where score = 'Fail' AND weakversions != 't') as otherfail");

$row = pg_fetch_array($result);
$sslscanversionsentry = "['SSLv2 or SSLv3 fail = $row[0]'," . $row[0] . "]," .
	"['Other fail = $row[1]'," . $row[1] . "]";
?>

<?php
// sslscan 40 and 56 bit keys results
$result = pg_query($db, "select 
		(select count(*) from t_sslscan where weakkeys = 't') as keyfail,
		(select count(*) from t_sslscan where score = 'Fail' AND weakkeys != 't') as otherfail");

$row = pg_fetch_array($result);
$sslscankeysentry = "['40 or 56 bit fail = $row[0]'," . $row[0] . "]," .
	"['Other fail = $row[1]'," . $row[1] . "]";
?>

<?php
// sslscan Export and Anonymous ciphers results
$result = pg_query($db, "select 
		(select count(*) from t_sslscan where weakciphers = 't') as cipherfail,
		(select count(*) from t_sslscan where score = 'Fail' AND weakciphers != 't') as otherfail");

$row = pg_fetch_array($result);
$sslscanciphersentry = "['Export or Anonymous cipher fail = $row[0]'," . $row[0] . "]," .
	"['Other fail = $row[1]'," . $row[1] . "]";
?>



<script type="text/javascript">google.load("visualization", "1", {packages:["corechart"]});
google.setOnLoadCallback(drawChart1);
function drawChart1() {
	<!-- Certificate chart -->
	var datacert = google.visualization.arrayToDataTable([
			['Category','Count'],
			<?php echo $certentry ?>
	]);
	var optionscert = {
		title: 'All targets and certificates', is3D: true, colors:['#009933','orange','red']
	};

	var chartcert = new google.visualization.PieChart(document.getElementById('certpiechart'));
	chartcert.draw(datacert, optionscert);

	<!-- SSLLabs chart -->
	var datassllabs = google.visualization.arrayToDataTable([
			['Category','Count'],
			<?php echo $ssllabsentry ?>
	]);
	var optionsssllabs = {
		title: 'All SSLLabs scores', is3D: true, colors:['#009933','#33CC33','#66FF66','#CCFF99','#D2D200','#FF6600','#FF5050','#FF0000','#669999'],
		sliceVisibilityThreshold: 0.001
	};

	var chartssllabs = new google.visualization.PieChart(document.getElementById('ssllabspiechart'));
	chartssllabs.draw(datassllabs, optionsssllabs);

	<!-- sslscan chart -->
	var datasslscan = google.visualization.arrayToDataTable([
			['Category','Count'],
			<?php echo $sslscanentry ?>
	]);
	var optionssslscan = {
		title: 'All targets sslscan scores', is3D: true, colors:['#009933','#FF0000'],
	};
	var chartsslscan = new google.visualization.PieChart(document.getElementById('sslscanpiechart'));
	chartsslscan.draw(datasslscan, optionssslscan);
}

google.setOnLoadCallback(drawChart2);
function drawChart2() {
	<!-- sslscan SSLv2 or SSLv3 fail -->
	var datasslscanversions = google.visualization.arrayToDataTable([
			['Category','Count'],
			<?php echo $sslscanversionsentry ?>
	]);
	var optionssslscanversions = {
		title: 'sslscan fails with SSLv2 or SSLv3', is3D: true, colors:['red','orange'],
	};
	var chartsslscanversions = new google.visualization.PieChart(document.getElementById('sslscanversionspiechart'));
	chartsslscanversions.draw(datasslscanversions, optionssslscanversions);

	<!-- sslscan 40 or 56 bit keys fail -->
	var datasslscankeys = google.visualization.arrayToDataTable([
			['Category','Count'],
			<?php echo $sslscankeysentry ?>
	]);
	var optionssslscankeys = {
		title: 'sslscan fails with 40 or 56 bit keys', is3D: true, colors:['red','orange'],
	};
	var chartsslscankeys = new google.visualization.PieChart(document.getElementById('sslscankeyspiechart'));
	chartsslscankeys.draw(datasslscankeys, optionssslscankeys);

	<!-- sslscan Export or Anonymous ciphers fail -->
	var datasslscanciphers = google.visualization.arrayToDataTable([
			['Category','Count'],
			<?php echo $sslscanciphersentry ?>
	]);
	var optionssslscanciphers = {
		title: 'sslscan files with Export or Anonymous ciphers', is3D: true, colors:['red','orange'],
	};
	var chartsslscanciphers = new google.visualization.PieChart(document.getElementById('sslscancipherspiechart'));
	chartsslscanciphers.draw(datasslscanciphers, optionssslscanciphers);

}
</script>

<body>
<div style="float:left;clean:left;">
	<div id="certpiechart" style="width: 600px; height: 350px; margin-bottom:10px"></div>
	<div id="ssllabspiechart" style="width: 600px; height: 350px; margin-bottom:10px"></div>
	<div id="sslscanpiechart" style="width: 600px; height: 350px; margin-bottom:10px"></div>
</div>
<div style="float:left;margin-left:10px;">
	<div id="sslscanversionspiechart" style="width: 600px; height: 350px; margin-bottom:10px"></div>
	<div id="sslscankeyspiechart" style="width: 600px; height: 350px; margin-bottom:10px"></div>
	<div id="sslscancipherspiechart" style="width: 600px; height: 350px; margin-bottom:10px"></div>
</div>
<!--
<div style="float:left;clean:left;margin-left:10px;">
	<div id="sslscanversionspiechart" style="width: 600px; height: 350px; margin-bottom:10px"></div>
	<div id="sslscankeyspiechart" style="width: 600px; height: 350px; margin-bottom:10px"></div>
	<div id="sslscancipherspiechart" style="width: 600px; height: 350px; margin-bottom:10px"></div>
</div>
-->


</body>
</html>

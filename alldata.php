<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['username'] == '') {
	header("Location:loginform.php");
}		
?>

<HTML>

<HEAD>
<?php include("menu.php"); ?>

<?php include("opendb.php"); ?>

<TITLE>Certificates and warning flags</TITLE>
<STYLE>
* {
	font-family:Arial;
	font-size:100%;
}
table {
	border-collapse: collapse;
	width: 100%;
}
tbody tr:nth-child(even){background-color: #e1e1e1}
</style>
<script src="/sorttable.js" type="text/javascript"></script>

<?php
	$subjectfilter = '%';
	$targetipfilter = '%';
	$organisationfilter = '%';
	$issuerfilter = '%';

	if(isset($_POST['subjectfilter']))
	{
		$subjectfilter = $_POST['subjectfilter'];
		$subjectfilter = trim($subjectfilter);	
		if ($subjectfilter == '') {
			$subjectfilter = '%';
		}
	}
	if(isset($_POST['targetipfilter']))
	{
		$targetipfilter = $_POST['targetipfilter'];
		$targetipfilter = trim($targetipfilter);	
		if ($targetipfilter == '') {
			$targetipfilter = '%';
		}
	}
	if(isset($_POST['organisationfilter']))
	{
		$organisationfilter = $_POST['organisationfilter'];
		$organisationfilter = trim($organisationfilter);	
		if ($organisationfilter == '') {
			$organisationfilter = '%';
		}
	}
	if(isset($_POST['issuerfilter']))
	{
		$issuerfilter = $_POST['issuerfilter'];
		$issuerfilter = trim($issuerfilter);	
		if ($issuerfilter == '') {
			$issuerfilter = '%';
		}
	}
?>

<?php
//$sqlchart1 = "select
//		(select count(distinct targetip) from t_certs) as targets,
//		(select count(distinct targetip) from v_expiredcerts) as expired,
//		(select count(distinct targetip) from v_expiringcerts) as expiring";

//$result = pg_query($db, $sqlchart1);

// Extract same date as table view for counting.

$sqlcerts2 = "SELECT a.expiredflag, a.expiringflag, a.weakflag, a.missingdnsflag, a.score, c.score, a.targetip, a.owner, a.certsubject, a.organisation, a.issuer, a.startdate, a.enddate, a.algorithm, a.keysize, b.actionuser, b.action, b.datetime FROM v_allcertsandflags a LEFT JOIN v_latestactions b on a.targetip = b.targetip LEFT JOIN t_sslscan c on a.targetip = c.targetip WHERE COALESCE(a.certsubject, '(none)') LIKE '$subjectfilter' AND COALESCE(a.targetip, '(none)') like '$targetipfilter' AND COALESCE(a.organisation, '(none)') LIKE '$organisationfilter' AND COALESCE(a.issuer, '(none)') like '$issuerfilter'";

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
$count_none = 0;
$count_fail = 0;

while ($row = pg_fetch_row($result2)) {
	// Count all
	$count_all++;
	if ($row[0] == 'EXPIRED') $count_expired++;
	if ($row[1] == 'EXPIRING') $count_expiring++;
	switch($row[4]) {
		case "A+" : $count_aplus++; break;
		case "A" : $count_a++; break;
		case "A-" : $count_aminus++; break;
		case "B" : $count_b++; break;
		case "C" : $count_c++; break;
		case "D" : $count_d++; break;
		case "E" : $count_e++; break;
		case "F" : $count_f++; break;
		default  : $count_none++; break;
	}
	if ($row[5] == 'Pass') $count_pass++;
	if ($row[5] == 'Fail') $count_fail++;
}
$count_live = $count_all - $count_expired - $count_expiring;

//$row = pg_fetch_array($result);
//$total = $row[0];
//$expired = $row[1];
//$expiring = $row[2];
//$good = $total - $expired -$expiring;
//$certentry = "['Live certs = $good'," . $good . " ]," .
//	"['Expiring certs = $expiring'," . $expiring . "]," .
//	"['Expired certs = $expired'," . $expired . "]";

$certentry = "['Live certs = $count_live'," . $count_live . " ]," .
	"['Expiring certs = $count_expiring'," . $count_expiring . "]," .
	"['Expired certs = $count_expired'," . $count_expired . "]";

$ssllabsentry = "['A+, exceptional = $count_aplus'," . $count_aplus . "]," .
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

<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">

google.load("visualization", "1", {packages:["corechart"]});
google.setOnLoadCallback(drawChart1);
function drawChart1() {

	var datassllabs = google.visualization.arrayToDataTable([
			['Category','Count'],
			<?php echo $ssllabsentry ?>
	]);
	var optionsssllabs = {
		title: 'All (filtered) SSLLabs scores', is3D: true, colors:['#009933','#33CC33','#66FF66','#CCFF99','#D2D200','#FF6600','#FF5050','#FF0000','#669999'],'chartArea': {'width': '90%', 'height': '80%'},
		sliceVisibilityThreshold: 0.001
	};
	var chartssllabs = new google.visualization.PieChart(document.getElementById('ssllabspiechart'));
	chartssllabs.draw(datassllabs, optionsssllabs);
}

google.load("visualization", "1", {packages:["corechart"]});
google.setOnLoadCallback(drawChart2);
function drawChart2() {

	var datacert = google.visualization.arrayToDataTable([
			['Category','Count'],
			<?php echo $certentry ?>
	]);
	var optionscert = {
		title: 'All (filtered) targets and certificates', is3D: true, colors:['green','orange','red'], 'chartArea': {'width': '90%', 'height': '80%'}
	};
	var chartcert = new google.visualization.PieChart(document.getElementById('certpiechart'));
	chartcert.draw(datacert, optionscert);
}

google.load("visualization", "1", {packages:["corechart"]});
google.setOnLoadCallback(drawChart3);
function drawChart3() {

	var datasslscan = google.visualization.arrayToDataTable([
			['Category','Count'],
			<?php echo $sslscanentry ?>
	]);
	var optionssslscan = {
		title: 'All (filtered) sslscan scores', is3D: true, colors:['#009933','#FF0000'],'chartArea': {'width': '90%', 'height': '80%'}
	};
	var chartsslscan = new google.visualization.PieChart(document.getElementById('sslscanpiechart'));
	chartsslscan.draw(datasslscan, optionssslscan);
}
</script>

</HEAD>

<BODY>

<!-- Get some table statistics -->

<p></p>
<H1>All certificates and warning flags</H1>

<div style="float:left; margin:10px;">

<H2>Totals</H2>



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
echo "<tr><td align=right>Total target IPs = </td><td align=left>" . $row[0] . "</td></tr>";
echo "<tr><td align=right>Total Unique certificates = </td><td align=left>" . $row[1] . "</td></tr>";
echo "<tr><td align=right>IPs with expired certificates = </td><td align=left>" . $row[2] . "</td></tr>";
echo "<tr><td align=right>IPs with expiring certificates = </td><td align=left>" . $row[3] . "</td></tr>";
echo "<tr><td align=right>IPs with weak certificates = </td><td align=left>" . $row[4] . "</td></tr>";
echo "<tr><td align=right>IPs with wildcard certificates = </td><td align=left>" . $row[5] . "</td></tr>";
echo "</table></p>";
?>

<form action="/exportalldata.php">
	<input type="submit" value="Download data as CSV" style="margin-top:5px;box-shadow: 5px 5px 5px #7f7f7f;">
</form>
</div>

<div id="certpiechart" style="width:400px; height:200px; float:left; margin-top:5px; margin-left:5px;"></div>
<div id="ssllabspiechart" style="width:400px; height:200px; float:left; margin-top:5px; margin-left:5px;"></div>
<div id="sslscanpiechart" style="width:400px; height:200px; float:left; margin-top:5px; margin-left:5px;margin-right:5px;"></div>


<div style="margin:10px:float:left;margin:5px;">
<h2>Filters</h2>
<p>User '%' as the wildcard character. Filters are case sensitive.</p>
<form name="subjectfilter" action="alldata.php" method="post">
<table style="width:320px;">
<?php
	echo "<tr><td>Target IP</td><td><input type=\"text\" name=\"targetipfilter\" value=\"" . $targetipfilter . "\"></td></tr>";
	echo "<tr><td>Cert subject</td><td><input type=\"text\" name=\"subjectfilter\" value=\"" . $subjectfilter . "\"></td></tr>";
	echo "<tr><td>Cert organisation</td><td><input type=\"text\" name=\"organisationfilter\" value=\"" . $organisationfilter . "\"></td></tr>";
	echo "<tr><td>Cert issuer</td><td><input type=\"text\" name=\"issuerfilter\" value=\"" . $issuerfilter . "\"></td></tr>";
?>
<tr><td></td><td><input type="submit" value="Apply" style="box-shadow: 5px 5px 5px #7f7f7f;">&nbsp;<A HREF=/alldata.php><input type="button" value="Reset" style="margin-top:5px;box-shadow: 5px 5px 5px #7f7f7f;"></A></td></tr>
</table>
</form>
</div>

<div style="margin:10px;float:left">
<A href=/recheckallips.php onclick="return confirm('Rechecking all IPs can take a few minutes.')"><input type="button" value="Check all target IP certificates" style="margin-top:5px;box-shadow: 5px 5px 5px #7f7f7f;"></A>
<A href=/sslscantestall.php onclick="return confirm('sslscan on all IPs can take an hour or more.')"><input type="button" value="sslscan all target IPs" style="margin-top:5px;box-shadow: 5px 5px 5px #7f7f7f;"></A>
</div>


<div style="margin:10px;clear:left;">
<p>Click on column heading to sort</p>

<table border="1" class="sortable" style="overflow: auto;">
<thead>
<tr bgcolor=#aaaaff>
<th>Expired</th>
<th>Expiring</th>
<th>Weak</th>
<th>Subject DNS</th>
<th>SSLLabs</th>
<th>sslscan</th>
<th>Target IP</th>
<th>Owner</th>
<th>Cert subject</th>
<th>Cert organisation</th>
<th>Cert issuer</th>
<th>Cert start date</th>
<th>Cert end date</th>
<th>Algorithm</th>
<th>Key size</th>
<th>Action user</th>
<th>Last action text</th>
<th>Action date</th>
</tr>
</thead>

<tbody>
<?php
$sqlcerts = "SELECT a.expiredflag, a.expiringflag, a.weakflag, a.missingdnsflag, a.score, c.score, a.targetip, a.owner, a.certsubject, a.organisation, a.issuer, a.startdate, a.enddate, a.algorithm, a.keysize, b.actionuser, b.action, b.datetime FROM v_allcertsandflags a LEFT JOIN v_latestactions b on a.targetip = b.targetip LEFT JOIN t_sslscan c on a.targetip = c.targetip WHERE COALESCE(a.certsubject, '(none)') LIKE '$subjectfilter' AND COALESCE(a.targetip, '(none)') like '$targetipfilter' AND COALESCE(a.organisation, '(none)') LIKE '$organisationfilter' AND COALESCE(a.issuer, '(none)') like '$issuerfilter' order by reverse(a.certsubject)";

$result = pg_query($db, $sqlcerts);

while ($row = pg_fetch_row($result)) { 
	switch($row[4]) {
		case "A+" : $scol = "#009933"; break;
		case "A" : $scol = "#33CC33"; break;
		case "A-" : $scol = "#66FF66"; break;
		case "B" : $scol = "#CCFF99"; break;
		case "C" : $scol = "#D2D200"; break;
		case "D" : $scol = "#FF6600"; break;
		case "E" : $scol = "#FF5050"; break;
		case "F" : $scol = "#FF0000"; break;
		default  : $scol = "#669999"; break;
	}
	printf('<tr bgcolor=white style="text-align:center;">
			<td>%s</td>	
			<td>%s</td>	
			<td>%s</td>
			<td>%s</td>
			<td bgcolor=%s>%s</td>
			<td bgcolor=%s>%s</td>
			<td><A HREF="/certificate.php?targetip=%s ">%s</td>
			<td>%s</td>
			<td>%s</td>
			<td>%s</td>
			<td>%s</td>
			<td>%s</td>
			<td bgcolor=%s>%s</td>
			<td bgcolor=%s>%s</td>
			<td>%s</td>
			<td>%s</td>
			<td>%s</td>
			<td>%s</td>
			</tr>', $row[0], $row[1], $row[2], $row[3], $scol, $row[4], $row[5]=="Pass"?"green":"red", $row[5], $row[6], $row[6], $row[7], $row[8], $row[9], $row[10], $row[11], $row[0]=="EXPIRED"?"red":($row[1]=="EXPIRING"?"orange":"green"), $row[12], $row[2]=="WEAK"?"red":"green", $row[13], $row[14], $row[15], $row[16], $row[17]);
}
?>
</tbody>
</table>
</div>
</BODY>

</HTML>

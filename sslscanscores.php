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

<title>sslscan scores</title>

<?php include("menu.php"); ?>
<?php include("opendb.php"); ?>


<H1>sslscan HTTPS server scores</H1>

<P>Servers fail if they support
<ul>
<li>SSLv2 or SSLv3 protocols (v2/v3)</li>
<li>40 or 56 bit key sizes (40/56)</li>
<li>Export or Anonymous ciphers (Ex/An)</li>
<li>RC4 ciphers only (RC4)</li>
</ul>
</p>

<?php
// sslscan results
$result = pg_query($db, "select 
		(select count(*) from t_sslscan where score = 'Pass') as scorepass,
		(select count(*) from t_sslscan where score = 'Fail') as scorefail");

$row = pg_fetch_array($result);
$sslscanentry = "['sslscan Pass = $row[0]'," . $row[0] . "]," .
	"['sslscan Fail = $row[1]'," . $row[1] . "]";
?>

<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">

google.load("visualization", "1", {packages:["corechart"]});
google.setOnLoadCallback(drawChart);

function drawChart() {

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

</script>
</head>

<body>

<div id="sslscanpiechart" style="width: 700px; height: 300px;"></div>

<div>
<h2>Individual sslscan results</h2>

<table border="1">
<theader>
<tr bgcolor="#AAAAFF">
	<th rowspan="2">Target</th>
	<th rowspan="2">Cert subject</th>
	<th colspan="4">Weaknesses</th>
	<th rowspan="2">Result</th>
	<th rowspan="2">Tested</th>
	<th rowspan="2">Test</th>
	<th rowspan="2">Delete</th>
</tr>
<tr bgcolor="#AAAAFF">
	<th>v2/v3</th>
	<th>40/56</th>
	<th>Ex/An</th>
	<th>RC4</th>
</tr>
</theader>

<?php include("opendb.php"); ?>

<?php
$sqlsslscantable = "SELECT a.targetip, b.certsubject, a.weakversions, a.weakkeys, a.weakciphers, a.weakrc4, a.score, cast(a.testdate as timestamp(0)) as testdate FROM t_sslscan a LEFT JOIN t_certs b ON a.targetip = b.targetip ORDER BY testdate DESC";

$result = pg_query($db, $sqlsslscantable);

while ($row = pg_fetch_row($result)) {
$weakversionscolor = '';
	$weakkeyscolor = '';
	$weakcipherscolor = '';
	$weakrc4color = '';
	$scorecolor = 'green';
	if($row[2] == "t") $weakversionscolor = 'red';
	if($row[3] == "t") $weakkeyscolor = 'red';
	if($row[4] == "t") $weakcipherscolor =' red';
	if($row[5] == "t") $weakrc4color = 'red';
	if($row[6] == 'Fail') $scorecolor = 'red';
        printf('<tr bgcolor=white align=center><td><A HREF="/certificate.php?targetip=%s">%s</td><td>%s</td><td bgcolor=%s>%s</td><td bgcolor=%s>%s</td><td bgcolor=%s>%s</td><td bgcolor=%s>%s</td><td bgcolor=%s>%s</td><td>%s</td><td><A HREF=/sslscantest.php?targetip=%s>Test</A></td><td><A HREF=/deletesslscanscore.php?targetip=%s onclick="return confirm(\'Delete %s?\')">X</A></td></tr>', $row[0], $row[0], $row[1], $weakversionscolor, $row[2], $weakkeyscolor, $row[3], $weakcipherscolor, $row[4], $weakrc4color, $row[5], $scorecolor, $row[6], $row[7], $row[0], $row[0], $row[0]);
}

?>

</table>

</div>

</body>
</html>

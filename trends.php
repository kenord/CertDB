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

<title>Trends</title>

<script type="text/javascript" src="https://www.google.com/jsapi"></script>

<?php include("menu.php"); ?>
<?php include("opendb.php"); ?>


<!-- Construct the line chart data -->

<script type="text/javascript">google.load("visualization", "1", {packages:["corechart"]});

google.setOnLoadCallback(drawChart1);
function drawChart1() {
	var trend1data = google.visualization.arrayToDataTable([
['Age in days', 'Total target IPs', 'Total uniqe certificate subjects']
	<?php
		$result = pg_query($db, "SELECT totaltargetips, totalcertsubjects, extract(day from (now() - testdate)) FROM t_trends");
		// Chart data for total target IPs, cert subjects vs time
		while($row = pg_fetch_row($result)) {
			$totaltargetips = $row[0];
			$totalcertsubjects = $row[1];
			$testdate = $row[2];
			printf(",[%d, %d, %d]", $testdate, $totaltargetips, $totalcertsubjects);
		}
	?>
	]);
	var trend1options = {
		title: 'Total targets and unique certificate subjects',
		curveType: 'function',
		legend: { position: 'bottom' },
		vAxis: {title: "Number", minValue:0},
		hAxis: {title: "Age of value in days"},
		colors: ['green','blue']
	};
	var chart1 = new google.visualization.LineChart(document.getElementById('linechart1'));
	chart1.draw(trend1data, trend1options);
}

google.setOnLoadCallback(drawChart2);
function drawChart2() {
	var trend2data = google.visualization.arrayToDataTable([
['Age in days', 'Live', 'Expired', 'Expiring']
	<?php
		$result = pg_query($db, "SELECT totalvalidcerts, totalexpiredcerts, totalexpiringcerts, extract(day from (now() - testdate)) FROM t_trends");
		// Chart data total valid certs, expired certs, expiring certs vs time
		while($row = pg_fetch_row($result)) {
			$totalvalidcerts = $row[0];
			$totalexpiredcerts = $row[1];
			$totalexpiringcerts = $row[2];
			$testdate = $row[3];
			printf(",[%d, %d, %d, %d]", $testdate, $totalvalidcerts, $totalexpiredcerts, $totalexpiringcerts);
		}
	?>
	]);
	var trend2options = {
		title: 'Total valid, expired and expiring certificates',
		curveType: 'function',
		legend: { position: 'bottom' },
		vAxis: {title: "Number", minValue:0},
		hAxis: {title: "Age of value in days"},
		colors: ['green','red','orange']
	};
	var chart2 = new google.visualization.LineChart(document.getElementById('linechart2'));
	chart2.draw(trend2data, trend2options);
}


google.setOnLoadCallback(drawChart3);
function drawChart3() {
	var trend3data = google.visualization.arrayToDataTable([
['Age in days', 'sslscan pass', 'sslscan fail']
	<?php
		$result = pg_query($db, "SELECT totalsslscanpasses, totalsslscanfails, extract(day from (now() - testdate)) FROM t_trends");
		// Chart data for total sslscan passes, sslscan fails vs time
		while($row = pg_fetch_row($result)) {
			$totalsslscanpasses = $row[0];
			$totalsslscanfails = $row[1];
			$testdate = $row[2];
			printf(",[%d, %d, %d]", $testdate, $totalsslscanpasses, $totalsslscanfails);
		}
	?>
	]);
	var trend3options = {
		title: 'Total sslscan passes and fails',
		curveType: 'function',
		legend: { position: 'bottom' },
		vAxis: {title: "Number", minValue:0},
		hAxis: {title: "Age of value in days"},
		colors: ['green','red']
	};
	var chart3 = new google.visualization.LineChart(document.getElementById('linechart3'));
	chart3.draw(trend3data, trend3options);
}


google.setOnLoadCallback(drawChart4);
function drawChart4() {
	var trend4data = google.visualization.arrayToDataTable([
['Age in days', 'SSLLabs good', 'SSLLabs bad']
	<?php
		$result = pg_query($db, "SELECT totalssllabsgood, totalssllabsbad, extract(day from (now() - testdate)) FROM t_trends");
		// Chart data for total SSLLabs good, SSLLabs bad vs time
		while($row = pg_fetch_row($result)) {
			$totalssllabsgood = $row[0];
			$totalssllabsbad = $row[1];
			$testdate = $row[2];
			printf(",[%d, %d, %d]", $testdate, $totalssllabsgood, $totalssllabsbad);
		}
	?>
	]);
	var trend4options = {
		title: 'Total SSLLabs good and bad',
		curveType: 'function',
		legend: { position: 'bottom' },
		vAxis: {title: "Number", minValue:0},
		hAxis: {title: "Age of value in days"},
		colors: ['green','red']
	};
	var chart4 = new google.visualization.LineChart(document.getElementById('linechart4'));
	chart4.draw(trend4data, trend4options);
}


</script>
</head>

<body>
<H1>Public HTTPS server survey</H1>

<div>
<P>HTTPS/443 servers in UK IP address space</P>
<P><A href=/gettrends.php><input type="button" name="gettrends" value="Generate current trend data" style="margin-top:5px;box-shadow: 5px 5px 5px #7f7f7f;"></A></P>
</div>

<div style="float:left;clean:left;">
	<div id="linechart1" style="width: 600px; height: 350px; margin-bottom:10px"></div>
	<div id="linechart2" style="width: 600px; height: 350px; margin-bottom:10px"></div>
</div>
<div style="float:left;">
	<div id="linechart3" style="width: 600px; height: 350px; margin-bottom:10px; margin-left:10px;"></div>
	<div id="linechart4" style="width: 600px; height: 350px; margin-bottom:10px; margin-left:10px;"></div>
</div>

</body>
</html>

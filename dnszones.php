<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['username'] == '') {
	header("Location:loginform.php");
}		
?>

<!DOCTYPE>

<html>
<head>

<title>Zones</title>
<style type="text/css">
* {
	font-family:Arial;
	font-size:100%;
}
</style>
<script src="/sorttable.js" type="text/javascript">
</script>
</head>

<body>

<?php include("menu.php"); ?>

<h1>Domains</h1>

<div>

<H2>Insert new domain</H2>
<form name="zone" action="insertzone.php" method="POST">
	<input type="text" name="zoneid" />
	<input type="Submit"  style="box-shadow: 5px 5px 5px #7f7f7f;"/>
</form>
</div>


<div style="float:left;clean:left;">

<h2>Click on column heading to sort</h2>

<table border="1" class="sortable">
	<tr bgcolor="#FFAAAA">
		<th>Zone</th>
		<th>Targets</th>
		<th>Delete</th>
	</tr>

<?php include("opendb.php"); ?>

<?php
$sqlskyzones = "select a.domain, count(distinct b.targetip) as targetcount from t_skydomains a left join t_certs b on a.domain <> b.targetip and position(a.domain in b.certsubject) > 0 group by a.domain order by targetcount desc";

//SELECT domain, tablekey FROM t_skydomains ORDER BY domain";

$result = pg_query($db, $sqlskyzones);

while ($row = pg_fetch_row($result)) {
	printf("<tr bgcolor=white>
		<td><A href=/dnszones.php?zone=%s>%s</A></td>
		<td>%s</td>
		<td align=center><A HREF=/deletezone.php?zone=%s onclick=\"return confirm('Delete?')\">X</A></td>
		</tr>", $row[0], $row[0], $row[1], $row[0]);
}

?>
</table>
</div>

<div style="float:left;margin-left:50px;">
<h2>Targets in selected DNS zone</h2>

<table border="1" class="sortable">
	<tr bgcolor="#FFAAAA">
		<th>Target IP</th>
		<th>Cert subject</th>
	</tr>

<?php
if(isset($_GET['zone']))
{
	// Zone selected, get targets
	$zone = $_GET['zone'];
	$sqlzonetargets = "SELECT targetip, certsubject FROM t_certs where certsubject LIKE '%$zone' ORDER BY certsubject";
	$result = pg_query($db, $sqlzonetargets);
	while ($row = pg_fetch_row($result)) {
		printf ("<tr bgcolor=white>
			<td><A href=/certificate.php?targetip=%s>%s</a></td>
			<td>%s</td>
			</tr>", $row[0], $row[0], $row[1]);
	}
}
?>
</table>
</div>

</body>
</html>

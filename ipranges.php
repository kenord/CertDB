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

<h1>IP ranges</h1>

<div>

<H2>Insert IP range</H2>
<form name="iprange" action="insertiprange.php" method="POST">
<input type="text" name="iprangeid" />
<input type="Submit" style="box-shadow: 5px 5px 5px #7f7f7f;"/>
</form>

</div>

<div style="float:left;clean:left;">

<h2>Click on column heading to sort</h2>

<table border="1" class="sortable">
<tr bgcolor="#FFAAAA">
<th>IP range</th>
<th>Targets</th>
<th>Delete</th>
</tr>

<?php include("opendb.php"); ?>

<?php
$sqlskyzones = "select a.iprange, count(distinct b.targetip) as targetcount from t_gbranges a left join t_certs b on a.iprange >> cast(b.targetip as inet) group by a.iprange order by targetcount desc";

$result = pg_query($db, $sqlskyzones);

$i = 0;

while ($row = pg_fetch_row($result)) {
	printf("<tr bgcolor=white>
			<td><A href=/ipranges.php?iprange=%s>%s</a></td>
			<td>%s</td>
			<td align=center><A HREF=/deleteiprange.php?iprange=%s onclick=\"return confirm('Delete?')\">X</A></td>
			</tr>", $row[0], $row[0], $row[1], $row[0]);
}

?>
</table>
</div>

<div style="float:left;margin-left:50px;">
<h2>Targets in selected IP range</h2>

<table border="1" class="sortable">
	<tr bgcolor="#FFAAAA">
		<th>Target IP</th>
		<th>Cert subject</th>
	</tr>

<?php
if(isset($_GET['iprange']))
{
	// Zone selected, get targets
	$iprange = $_GET['iprange'];
	$sqlrangetargets = "SELECT targetip, certsubject FROM t_certs where cast('$iprange' as cidr) >> cast (targetip as inet) ORDER BY certsubject";
	$result = pg_query($db, $sqlrangetargets);
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

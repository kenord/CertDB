<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['username'] == '') {
	header("Location:loginform.php");
}		
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
<meta name="generator" content="HTML Tidy for Linux (vers 25 March 2009), see www.w3.org">

<title>Certificates and warning flags</title>
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

<h1>All expired certificates</h1>

<h2>Click on column heading to sort</h2>

<table border="1" class="sortable">
<tr bgcolor="#FFAAAA">
<th>Target IP</th>
<th>Cert subject</th>
<th>Cert organisation</th>
<th>Cert issuer</th>
<th>Cert start date</th>
<th>Cert end date</th>
<th>Cert algorithm</th>
<th>Cert key size</th>
<th>Action user</th>
<th>Last action text</th>
<th>Action date</th>            
</tr>

<?php include("opendb.php"); ?>

<?php
$sqlcerts = "SELECT a.targetip, a.certsubject, a.organisation, a.issuer, a.startdate, a.enddate, a.algorithm, a.keysize, b.actionuser, b.action, b.datetime FROM v_expiredcerts a left join v_latestactions b on a.targetip = b.targetip order by a.targetip asc";

$result = pg_query($db, $sqlcerts);

$i = 0;

while ($row = pg_fetch_row($result)) {
	$i++;
	$rowcolour = ($i%2 == 0)? "#ffeeee": "#ffeeee";
	printf('<tr bgcolor=%s>
			<td><A HREF="/certificate.php?targetip=%s">%s</td>
			<td>%s</td>
			<td>%s</td>
			<td>%s</td>
			<td>%s</td>
			<td>%s</td>
			<td>%s</td>
			<td>%s</td>
			<td>%s</td>
			<td>%s</td>
			<td>%s</td>
			</tr>', $rowcolour, $row[0], $row[0], $row[1], $row[2], $row[3], $row[4], $row[5], $row[6], $row[7], $row[8], $row[9], $row[10]);
}

?>
</table>
</body>
</html>

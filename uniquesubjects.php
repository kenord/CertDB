<!DOCTYPE html>
<HTML>
<HEAD>
<TITLE>Unique subjects</TITLE>
<STYLE>* { font-family:Arial; font-size:98%; }</STYLE>
<script src="/sorttable.js" type="text/javascript"></script>
</HEAD>
<BODY>

<?php include ("menu.php"); ?>

<H1>Unique subjects</H1>
<H2>Click on column heading to sort</H2>

<table border="1" class="sortable">
<tr bgcolor="#ffccaa">
<th>Target IP</th>
<th>Cert subject</th>
<th>Cert organisation</th>
<th>Cert issuer</th>
<th>Cert start date</th>
<th>Cert end date</th>
<th>Cert algorithm</th>
<th>Cert key size</th>
<th>Action user</th>
<th>Action text</th>
<th>Action date</th>
</tr>

<?php include("opendb.php"); ?>

<?php
$sqlcerts = "SELECT a.targetip, a.certsubject, a.organisation, a.issuer, a.startdate, a.enddate, a.algorithm, a.keysize, b.actionuser, b.action, b.datetime FROM v_expiringcerts a left join v_latestactions b on a.targetip = b.targetip";
$result = pg_query($db, $sqlcerts);
$i = 0;
while ($row = pg_fetch_row($result)) {
	$i++;
	$rowcolour = ($i%2 == 0)? "#fffeee": "#fffeee";
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
</BODY>
</HTML>

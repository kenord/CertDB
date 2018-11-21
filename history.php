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

<TITLE>History</TITLE>
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


</HEAD>

<BODY>

<!-- Get some table statistics -->

<p></p>
<H1>History</H1>

<A HREF=/sethistory.php?overwrite=old onclick="return confirm('Overwrite OLD data with current data?')"><input type="button" value="Overwrite OLD with current data" style="margin-top:5px;box-shadow: 5px 5px 5px #7f7f7f;"></A>

<div style="margin:10px;clear:left;">
<p>Click on column heading to sort</p>

<table border="1" class="sortable" style="overflow: auto;">
<thead>
<tr bgcolor=#aaaaff>
<th>Table</th>
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
</tr>
</thead>

<tbody>
<?php
// Overwrite NEW table with current data
pg_query($db, "DROP TABLE t_alldatanew");
pg_query($db, "CREATE TABLE t_alldatanew as SELECT CAST(a.expiredflag AS text), CAST(a.expiringflag AS text), CAST(a.weakflag AS text), CAST(a.missingdnsflag AS text), a.score AS ssllabsscore, c.score AS sslscanscore, a.targetip, a.owner, a.certsubject, a.organisation, a.issuer, a.startdate, a.enddate, a.algorithm, a.keysize FROM v_allcertsandflags a LEFT JOIN v_latestactions b on a.targetip = b.targetip LEFT JOIN t_sslscan c on a.targetip = c.targetip WHERE COALESCE(a.certsubject, '(none)') LIKE '%' AND COALESCE(a.targetip, '(none)') like '%' AND COALESCE(a.organisation, '(none)') LIKE '%' AND COALESCE(a.issuer, '(none)') like '%' order by reverse(a.certsubject)");


$sqlhistory = "(select 'OLD' as old, * from t_alldataold EXCEPT select 'OLD' as old, * from t_alldatanew) UNION (select 'NEW' as new, * from t_alldatanew EXCEPT select 'NEW' as new, * from t_alldataold);";

$result = pg_query($db, $sqlhistory);

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
			</tr>', $row[0], $row[1], $row[1], $row[3], $row[4], $scol, $row[5], $row[6]=="Pass"?"green":"red", $row[6], $row[7], $row[7], $row[8], $row[9], $row[10], $row[11], $row[12], $row[1]=="EXPIRED"?"red":($row[2]=="EXPIRING"?"orange":"green"), $row[13], $row[3]=="WEAK"?"red":"green", $row[14], $row[15]);
}
?>
</tbody>
</table>
</div>
</BODY>

</HTML>

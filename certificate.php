<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['username'] == '') {
	header("Location:loginform.php");
}		
?>

<!DOCTYPE html>

<html>
<head>
<title>Certificate information</title>
<style type="text/css">
* {
	font-family:Arial;
	font-size:98%;
}
table {
        border-collapse: collapse;
	padding: 1px;
}
tbody tr:nth-child(even) {
	background-color: #e1e1e1;
}
tbody tr:nth-child(odd) {
        background-color: #ffffff;
}

</style>
<script src="/sorttable.js" type="text/javascript"></script>
</head>

<body>

<?php include("menu.php");?>
<?php include("opendb.php");?>

<h1>Certificate information</h1>

<!-- Certificate details -->
<div style="width:400px;float:left;margin:10px;clean:left;">

<h2>Certificate details</h2>

<?php

	$targetip = $_GET["targetip"];

	if (!filter_var($targetip, FILTER_VALIDATE_IP)) {
		header("Location:alldata.php");
	}

	// Get the certificate details for this target
	$sqlcert = "SELECT certsubject, organisation, issuer, startdate, enddate, algorithm, keysize, testdate FROM t_certs WHERE targetip = '$targetip'";

	$result = pg_query($db, $sqlcert);

	$certsubject = pg_fetch_result($result, 0, 'certsubject');
	$organisation = pg_fetch_result($result, 0, 'organisation');
	$issuer = pg_fetch_result($result, 0, 'issuer');
	$startdate = pg_fetch_result($result, 0, 'startdate');
	$enddate = pg_fetch_result($result, 0, 'enddate');
	$algorithm = pg_fetch_result($result, 0, 'algorithm');
	$keysize = pg_fetch_result($result, 0, 'keysize');
	$tested = pg_fetch_result($result, 0, 'testdate');

	echo "<table frame=box style=\"background: white;\">";
	echo "<tr><td align=right>Target IP =</td><td align=left><b>" . $targetip . "</b></td></tr>";
	echo "<tr><td align=right>Subject =</td><td align=left><b>" . $certsubject . "</b></td></tr>";
	echo "<tr><td align=right>Organisation =</td><td align=left>" . $organisation . "</td></tr>";
	echo "<tr><td align=right>Issuer =</td><td align=left>" . $issuer . "</td></tr>";
	echo "<tr><td align=right>Start date =</td><td align=left>" . $startdate . "</td></tr>";
	echo "<tr><td align=right>End date =</td><td align=left>" . $enddate . "</td></tr>";
	echo "<tr><td align=right>Algorithm =</td><td align=left>" . $algorithm . "</td></tr>";
	echo "<tr><td align=right>Key size =</td><td align=left>" . $keysize . "</td></tr>";
	echo "<tr><td align=right>Tested =</td><td align=left>" . $tested . "</td></tr>";
	echo "</table>";

	printf("<A HREF=/recheckip.php?targetip=%s><input type=\"button\" value=\"Check certificate for %s\" style=\"margin-top:5px;box-shadow: 5px 5px 5px #7f7f7f;\"></A>", $targetip ,$targetip);

?>
</div>
<!-- End certificate details -->



<!-- Warnings -->
<div style="float:left;margin:10px;width:400px;">

<?php

	echo "<h2>Warnings</h2>";

	$sqlflags = "SELECT expiredflag, expiringflag, weakflag, missingdnsflag, score FROM v_allcertsandflags WHERE targetip = '$targetip'";

	$result = pg_query($db, $sqlflags);
	$row = pg_fetch_row($result);

	if($row[0] or $row[1] or $row[2] or $row[3] or $row[4]) {
		if ($row[0]) {
			echo "<div style=\"background-color:#FF5555; width: 8cm\"><B>EXPIRED CERTIFICATE</B></div></P>";
		}
		if ($row[1]) {
			echo "<div style=\"background-color:#ffccaa; width: 8cm\"><B>EXPIRING CERTIFICATE</B></div></P>";
		}
		if ($row[2]) {
			echo "<div style=\"background-color:#FF8181; width: 8cm\"><B>WEAK ALGORITHM OR KEY SIZE</B></div></P>";
		}
		if ($row[3]) {
			echo "<div style=\"background-color:#FFA1A1; width: 8cm\"><B>SUBJECT MISSING DNS A RECORD</B></div></P>";
		}
		if ($row[4]) {
			if ($row[4] == "F") {
				echo "<div style=\"background-color:#FFAA00; width: 8cm\"><B>SSLLABS SCORE F</B></div>";
			}
			if ($row[4] == "No DNS") {
				echo "<div style=\"background-color:#FFFF00; width: 8cm\"><B>SSLLABS CANNOT TEST</B></div>";
			}
		}
	}

?>
</div>
<!-- End warnings -->




<!-- SSLLabs and sslscan scores -->
<div style="float:left;margin:10px;">

<h2>SSLLabs</h2>
<div style="overflow-y:scroll;max-height:250px;white-space: nowrap;">

<table border="1" class="sortable">
<thead>
<tr bgcolor="#AAAAFF">
	<th>Target</th>
	<th>Valid DNS A record</th>
	<th>Score</th>
	<th>Tested</th>
	<th>Test</th>
</tr>
</thead>
<tbody>
<?php

	$sqlssllabs = "select a.ipaddr, a.hostname, b.score, cast(b.testdate as timestamp(0)) from v_allhostnamesandips a left join t_ssllabs b on a.ipaddr = b.targetip where a.ipaddr = '$targetip'";

	$result = pg_query($db, $sqlssllabs);

	while ($row = pg_fetch_row($result)) {
		printf('<tr bgcolor=white><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td><A HREF=/ssllabstest.php?targetip=%s&targetname=%s>Test</A></td></tr>', $row[0], $row[1], $row[2], $row[3], $row[0], $row[1]);
	}

?>
</tbody>
</table>
</div>


<h2>sslscan</h2>
<div style="overflow-y:none;max-height:250px;white-space: nowrap;">

<table border="1">
<thead>
<tr bgcolor="#AAAAFF">
	<th rowspan="2">Target</th>
	<th colspan="4">Weaknesses</th>
	<th rowspan="2">Score</th>
	<th rowspan="2">Tested</th>
</tr>
<tr bgcolor="#AAAAFF">
	<th>v2/v3</th>
	<th>40/56</th>
	<th>Ex/An</th>
	<th>RC4</th>
</tr>
</thead>
<tbody>
<?php

	$sqlsslscan = "SELECT targetip, weakversions, weakkeys, weakciphers, weakrc4, score, cast(testdate as timestamp(0)) FROM t_sslscan WHERE targetip = '$targetip'";

	$result = pg_query($db, $sqlsslscan);

	while ($row = pg_fetch_row($result)) {
		$weakversionscolor = '';
		$weakkeyscolor = '';
		$weakcipherscolor = '';
		$weakrc4color = '';
		$scorecolor = 'green';
		if($row[1] == "t") $weakversionscolor = 'red';
		if($row[2] == "t") $weakkeyscolor = 'red';
		if($row[3] == "t") $weakcipherscolor =' red';
		if($row[4] == "t") $weakrc4color = 'red';
		if($row[5] == 'Fail') $scorecolor = 'red';
		printf('<tr bgcolor=white align=center><td>%s</td><td bgcolor=%s>%s</td><td bgcolor=%s>%s</td><td bgcolor=%s>%s</td><td bgcolor=%s>%s</td><td bgcolor=%s>%s</td><td>%s</td></tr>', $row[0], $weakversionscolor, $row[1], $weakkeyscolor, $row[2], $weakcipherscolor, $row[3], $weakrc4color, $row[4], $scorecolor, $row[5], $row[6]);
	}

?>
</tbody>
</table>
<?php

	echo "<A HREF=/sslscantest.php?targetip=$targetip><input type=\"button\" name=\"so_link\" value=\"Test\" style=\"margin-top:5px;box-shadow: 5px 5px 5px #7f7f7f;\"></A>";

?>
</div>


</div>
<!-- End SSLLabs links and score -->



<!-- Target IP PTR table -->
<div style="width:400px;float:left;margin:10px;clear:left;">

<h2>Target IP public DNS PTR records</h2>

<table border="1" class="sortable">
<tr bgcolor="#AAAAFF">
<th>Target IP</th>
<th>Public DNS PTR record</th>
<th>IP Match</th>
<th>Manual</th>
</tr>

<?php

	$sqlptrs = "SELECT targetip, targetptr FROM t_targetptrs WHERE targetip = '$targetip'";

	$result = pg_query($db, $sqlptrs);

	while ($row = pg_fetch_row($result)) {
		printf('<tr bgcolor=white><td>%s</td><td>%s</td><td>%s</td><td><A HREF=/insertipaddrhostname.php?ipaddr=%s&hostname=%s><input type="button" name="so_link" value="Add"></A></td><tr>', $row[0], $row[1], gethostbyname($row[1]) == $row[0]?"Match":"No", $row[0], $row[1]);
	}

?>

</table>

<?php

	printf("<A HREF=/lookuptargetptr.php?targetip=%s><input type=\"button\" value=\"Check DNS PTR for %s\" style=\"margin-top:5px;box-shadow: 5px 5px 5px #7f7f7f;\"></A>", $targetip ,$targetip);

?>

</div>
<!-- End target IP PTR table -->




<!-- Subject table -->
<div style="width:400px;float:left;margin:10px;">

<h2>Subject public DNS A records</h2>

<table border="1" class="sortable">
<thead>
<tr bgcolor="#AAAAFF">
<th>Certificate subject</th>
<th>Public DNS A record</th>
</tr>
</thead>
<tbody>
<?php

	$sqlsubject = "SELECT certsubject, subjectip FROM t_subjectips WHERE certsubject = '$certsubject' ORDER BY subjectip";

	$result = pg_query($db, $sqlsubject);

	while ($row = pg_fetch_row($result)) {
		printf('<tr bgcolor=white><td>%s</td><td>%s</td><tr>', $row[0], $row[1]);
	}

?>
</tbody>
</table>

<?php

	printf('<A HREF=/lookupsubject.php?subject=%s><input type="button" value="Check DNS A for %s" style="margin-top:5px;box-shadow: 5px 5px 5px #7f7f7f;"></A>', $certsubject, $certsubject);

?>

</div>
<!-- End subject table -->


<!-- SAN table --> 
<div style="float:left;margin:10px;">

<h2>Subject Alternative Names</h2>

<div style="overflow-y:scroll;max-height:250px;white-space: nowrap;">

<table border="1" class="sortable">
<thead>
<tr bgcolor="#AAAAFF">
<th>Certificate SAN</th>
<th>Public DNS A record</th>
<th>Lookup DNS A record</th>
</tr>
</thead>
<tbody>
<?php

	// Print SANs and their DNS A records
	$sqlsans = "SELECT sanname,sanip FROM v_allsansandips WHERE targetip = '$targetip' ORDER BY sanname";

	$result = pg_query($db, $sqlsans);

	while ($row = pg_fetch_row($result)) {
		printf('<tr><td>%s</td><td>%s</td><td><A HREF=/lookupsan.php?sanname=%s>DNS A for %s</a></td><tr>', $row[0], $row[1], $row[0], $row[0]);
	}

?>
</tbody>
</table>
</div>
</div>
<!-- End SAN table -->


<!-- Actions table -->
<div style="float:left;margin:10px;clear:left;">

<h2>Actions</h2>

<table border="1" class="sortable">
<thead>
<tr bgcolor="#bbbbbb">
<th>User</th><th>Action</th><th>Date</th><th>Delete</th>
</tr>
</thead>
<tbody>
<?php

	$sqlactions = "SELECT actionuser, action, datetime, tablekey FROM t_actions WHERE targetip = '$targetip' ORDER BY datetime ASC";

	$result = pg_query($db, $sqlactions);

	while ($row = pg_fetch_row($result)) {
		printf('<tr bgcolor=white><td>%s</td><td>%s</td><td>%s</td><td align="center"><A HREF=/deleteaction.php?actionid=%s onclick="return confirm(\'Delete?\')"><input type="button" value="X"></A></td><tr>', $row[0], $row[1], $row[2], $row[3]);
	}

?>
</tbody>
</table>

<h2>Record more actions</h2>

<?php

	echo "<form name=\"actions\" action=\"insertaction.php\" method=\"POST\">";
	echo "User <input type=\"text\" name=\"actionuser\" />";
	echo "Target IP <input type=\"text\" name=\"actionip\" value=\"" . $targetip . "\"/>";
	echo "Action text <input type=\"text\" name=\"actiontext\" />";
	echo "<input type=\"Submit\" style=\"margin-left:5px;box-shadow: 5px 5px 5px #7f7f7f;\"/>";
	echo "</form>";

?>
</div>
<!-- End actions table -->


<!-- Screenshot -->
<div style="margin:10px;clear:left;width:1000px;height:1000px;">

<h2>Web page view</h2>

<?php

	$sqlscreenshot = "SELECT imagelink, testdate FROM t_screenshots WHERE targetip = '$targetip'";
	$result = pg_query($db, $sqlscreenshot);
	
	while ($row = pg_fetch_row($result)) {
			printf ("<p>Screenshot generated on %s</p>", $row[1]);
			printf ("<p><img src=%s></p>", $row[0]);
		}
	printf("<A href=/insertscreenshot.php?targetip=%s>Get web page screenshot</A>", $targetip);

?>

</div>

</body>
</html>

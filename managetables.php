<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['username'] == '') {
	header("Location:loginform.php");
}		
?>

<!DOCTYPE html>

<html>
<head>
<title>Manually manage tables</title>
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

<?php include("menu.php");?>
<?php include("opendb.php"); ?>

<h1>Manually manage tables</h1>

<!-- Add target IP -->
<div style="width:400px;float:left;margin:10px;clean:left;">

<h2>Add a new target IP</h2>

<form name="addtargetip" action="inserttargetip.php" method="post">
Target IP address <input type="text" name="targetip">
<input class="hover-item" type="submit" value="Add" style="box-shadow: 5px 5px 5px #7f7f7f;">
</form>
<p>
The address will be added if it is not already in the DB. You will be taken to the certificate page for the IP address.
</p>

<div style="margin:10px;float:left">
<p><A href=/recheckallips.php onclick="return confirm('Rechecking all IPs can take a few minutes.')"><input type="button" value="Check all target IP certificates" style="margin-top:5px;box-shadow: 5px 5px 5px #7f7f7f;"></A></p>
<p><A href=/sslscantestallprogressbar.php onclick="return confirm('sslscan on all IPs can take an hour or more.')"><input type="button" value="sslscan all target IPs" style="margin-top:5px;box-shadow: 5px 5px 5px #7f7f7f;"></A></p>
</div>

</div>
<!-- End add target IP -->


<!-- Add IP address/hostname entries -->
<div style="float:left;margin:10px;width:400px;">

<h2>Manual IP address/hostname entries</h2>

<form name="addtargetip" action="insertipaddrhostname.php" method="post">
<table>
	<tr>
		<td>IP address</td>
		<td>Hostname</td>
		<td></td>
	</tr>
	<tr>
		<td><input type="text" name="ipaddr"></td>
		<td><input type="text" name="hostname"></td>
		<td><input class="hover-item" type="submit" value="Add" style="box-shadow: 5px 5px 5px #7f7f7f;"></td>
	</tr>
</table>
</form>
<p></p>

<table>
<table border="1" class="sortable">
<tr bgcolor="#AAAAFF">
	<th>IP address</th>
	<th>Hostname</th>
	<th>Delete</th>
</tr>

<?php
$sqlipaddrhostname = "SELECT ipaddr, hostname FROM t_manuallookups";
$result = pg_query($db, $sqlipaddrhostname);
while ($row = pg_fetch_row($result)) {
	printf("<tr bgcolor=white><td>%s</td><td>%s</td><td align=center><a href=/deleteipaddrhostname.php?ipaddr=%s onclick=\"return confirm('Delete?')\">X</a></td></tr>", $row[0], $row[1], $row[0]);
	}
?>
</table>
</div>
<!-- End IP address/hostname entries -->





</body>
</html>

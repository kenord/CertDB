<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
<meta name="generator" content="HTML Tidy for Linux (vers 25 March 2009), see www.w3.org">

<title>Scan management</title>

<body>

<?php include("menu.php"); ?>
<?php include("opendb.php"); ?>

<h1>Scan management</h1>

<H2>GB results</H2>

<?php
$sqlcountgbtable = "SELECT COUNT(*) FROM t_json_gb";
$result = pg_query($db, $sqlcountgbtable);
$row = pg_fetch_row($result);
printf('<P>GB table currently contains <B>%s</B> certificates.</P>', $row[0]); 
?>

<form action="/purgegbtable.php">
<input type="submit" value="Purge GB table">
</form>
<P>Warning: this action will remove all GB certificate information from the database.</P>

<H2>GB range scanning</H2>

<?php
$sqlcountskyzonesandscanned = "SELECT (SELECT COUNT(*) FROM t_gbranges) AS total, (SELECT COUNT(*) FROM t_gbranges WHERE scanned = true) AS scanned";
$result = pg_query($db, $sqlcountskyzonesandscanned);
$row = pg_fetch_row($result);
printf("<P>Scanned <B>%d</B> ranges from a total of <B>%s</B> GB ranges (<B>%.2f%%</B>).</sP>", $row[1], $row[0], (int)$row[1] * 100 / (int)$row[0]);
?>

<form action="/resetrangesscanned.php">
<input type="submit" value="Reset ranges scanned">
</form>


<P>Warning: this action will cause all ranges to be inclued during the next scan.</P>


<H2>Scanning</H2>

<?php
$sqlgetscanstatus = "SELECT running, start FROM t_scanstatus";
$result = pg_query($db, $sqlgetscanstatus);
$row = pg_fetch_row($result);

if($row[0] == "1") {
	printf("<P>Scan is currently <B>RUNNING</B>, started at <B>%s</B></P>", $row[1]);
}
else {
	printf("<P>Scan is currently <B>STOPPED</B>.</P>");
}
?>

<form action="http://vm000277/startscan.php">
<input type="submit" value="Start scan">
</form>

<form action="http://vm000277/stopscan.php">
<input type="submit" value="Stop scan">
</form>



<?php HEADER("Refresh: 5; url='/manage.php'") ?>

</body>
</html>

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

<?php include("opendb.php"); ?>

<h1>Set history</h1>

<?php
if(isset($_GET['overwrite']))
{
	// Zone selected, get targets
	$overwrite = $_GET['overwrite'];

	if (strcmp($overwrite, "old") == 0) {
		pg_query($db, "DROP TABLE t_alldataold");
		pg_query($db, "CREATE TABLE t_alldataold as SELECT CAST(a.expiredflag AS text), CAST(a.expiringflag AS text), CAST(a.weakflag AS text), CAST(a.missingdnsflag AS text), a.score AS ssllabsscore, c.score AS sslscanscore, a.targetip, a.owner, a.certsubject, a.organisation, a.issuer, a.startdate, a.enddate, a.algorithm, a.keysize FROM v_allcertsandflags a LEFT JOIN v_latestactions b on a.targetip = b.targetip LEFT JOIN t_sslscan c on a.targetip = c.targetip WHERE COALESCE(a.certsubject, '(none)') LIKE '%' AND COALESCE(a.targetip, '(none)') like '%' AND COALESCE(a.organisation, '(none)') LIKE '%' AND COALESCE(a.issuer, '(none)') like '%' order by reverse(a.certsubject)"); 
	}
	if (strcmp($overwrite, "new") == 0) {
                pg_query($db, "DROP TABLE t_alldatanew");
                pg_query($db, "CREATE TABLE t_alldatanew as SELECT CAST(a.expiredflag AS text), CAST(a.expiringflag AS text), CAST(a.weakflag AS text), CAST(a.missingdnsflag AS text), a.score AS ssllabsscore, c.score AS sslscanscore, a.targetip, a.owner, a.certsubject, a.organisation, a.issuer, a.startdate, a.enddate, a.algorithm, a.keysize FROM v_allcertsandflags a LEFT JOIN v_latestactions b on a.targetip = b.targetip LEFT JOIN t_sslscan c on a.targetip = c.targetip WHERE COALESCE(a.certsubject, '(none)') LIKE '%' AND COALESCE(a.targetip, '(none)') like '%' AND COALESCE(a.organisation, '(none)') LIKE '%' AND COALESCE(a.issuer, '(none)') like '%' order by reverse(a.certsubject)");
	}
}
header("Location: " . $_SERVER["HTTP_REFERER"]);
?>
</body>
</html>

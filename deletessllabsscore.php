<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>

<title>Delete SSLLabs score</title>
</head>
<body>

<?php include("opendb.php"); ?>

<?php
$targetip = $_GET["targetip"];

// Prepare statement
$sqldeletessllabsscore = "DELETE FROM t_ssllabs WHERE targetip = $1";

// Param statement
$result = pg_query_params($db, $sqldeletessllabsscore, array($targetip));


header("Location: " . $_SERVER["HTTP_REFERER"]);
?>

</body>
</html>




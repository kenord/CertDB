<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Wp Migration</title>
</head>
<style>
.progress_wrapper {
	width:360px;
	border:1px solid #ccc;
	position:absolute;
	top:200px;
	left:50%;
	margin-left:-150px
}

.progress {
	height:20px;
	background-color:#00F
}

.progresspc {
	margin-top:-20px;
	height:20px;
	margin-left:300px;
	width:60px;
	background-color:#FF0;
	color:#000
}
</style>
<body>
<?php

$width 		  = 0;					// starting width
$percentage_num	  = 0;					// starting percentage number
$percentage_bar   = 0;					// starting percentage bar
$total_iterations = 100;				// iterations to perform
$width_per_iteration = 300 / $total_iterations;		// pixels progress div is increased each iteration
$percentage_num_per_iteration = 100 / $total_iterations;		// % in progresspc div increased each iteration

$startime = microtime(true);

ob_start();
header( 'Content-type: text/html; charset=utf-8' );
for ( $i = 0; $i <= $total_iterations; $i++ )
{
	echo '<H1>Time per ' . microtime(true) - $starttime . ' microseconds</H1>';
	echo '<div class="progress_wrapper">';
	echo '	<div class="progress" style="width:' . $width . 'px;"></div>';
	echo '	<div class="progresspc">' . number_format($percentage_num, 0) . '%' . '</div>';
	echo '</div><br>';
	echo str_pad('',4096)."\n";    

//Do each Iteration Work here

	$percentage_num += $percentage_num_per_iteration;
	$width += $width_per_iteration;
	ob_flush();
	flush(); // Both flushes are necessary
	usleep(100000);
}

ob_flush_clean();
?>
</body>

<?php

$db = pg_connect("dbname=certdb host=localhost port=5433 user=web02 password=password123");

if (!$db) {
        echo "An error occurred.\n";
        exit;
}
?>

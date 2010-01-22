<?php

include_once("config.php");

$link = mysql_connect("$dbhost","$dbuser","$dbpass")
    or die('Could not connect: ' . mysql_error());

mysql_select_db("$db") or die('Could not select database');

?>

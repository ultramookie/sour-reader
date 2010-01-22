<?php 
include_once("db.php");
include_once("mooshulib.php");
?>
<html>
<head>
<title><? echo "$sitename"; ?> </title>
<style type="text/css">
    body {
	font-size: small;
        font-family: "Verdana", Sans;
    }

</style>
</head>
<body>
<h2><b><a href="<? echo "$siteurl"; ?>"><? echo "$sitename"; ?></a></b></h2>
<?php
	if(checkCookie()) {
		$username = getUserName();
		echo "<a href=\"usermod.php\">" . $username . "</a> | <a href=\"settings.php\">settings</a> | <a href=\"logout.php\">logout</a>";
	} else {
		echo "<a href=\"login.php\">login</a>";
	}
?>
<hr />

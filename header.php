<?php 
include_once("db.php");
include_once("sourlib.php");

#cache busting because the content changes so much
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); 

?>
<html>
<head>
<title>sour reader</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" media="screen" href="style.css" />
</head>
<body>
<div class="main">
<p class="menu">
<?php
	if(checkCookie()) {
		$username = getUserName();
		echo "<a href=\"index.php\">home</a> | <a href=\"usermod.php\">" . $username . "</a> | <a href=\"addfeeds.php\">subscribe</a> | <a href=\"listfeeds.php\">feeds</a> | <a href=\"listcats.php\">categories</a> | <a href=\"settings.php\">settings</a> | <a href=\"logout.php\">logout</a>";
	} else {
		echo "<a href=\"index.php\">home</a> | <a href=\"login.php\">login</a>";
	}
?>
</p>

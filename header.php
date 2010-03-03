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
<link rel="stylesheet" type="text/css" href="yui/base-min.css" />
<link rel="stylesheet" type="text/css" href="yui/reset-fonts-grids.css" />
<link rel="stylesheet" type="text/css" media="screen" href="style.css" />
</head>
<body>
<div id="doc" class="yui-t7">  
<div id="hd" role="navigation">
<?php
	if(checkCookie()) {
		$username = getUserName();
		echo "<a href=\"index.php\">home</a> | <a href=\"usermod.php\">" . $username . "</a> | <a href=\"addfeeds.php\">subscribe</a> | <a href=\"listfeeds.php\">feeds</a> | <a href=\"listcats.php\">categories</a> | <a href=\"settings.php\">settings</a> | <a href=\"logout.php\">logout</a>";
	} else {
		echo "<a href=\"index.php\">home</a> | <a href=\"login.php\">login</a>";
	}
?>
</div>
<div id="bd" role="main">
<div class="yui-g">

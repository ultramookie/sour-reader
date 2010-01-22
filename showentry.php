<?php
        include_once("header.php");
	include_once("db.php");
	include_once("sourlib.php");

	$id = $_GET['id'];

	$id = mysql_real_escape_string($id);

	if (checkCookie()) {
		markEntryRead($id);
	}

	showEntry($id); 

?>
</body>
</html>

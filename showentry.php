<?php
        include_once("header.php");
	include_once("db.php");
	include_once("sourlib.php");

	$id = $_GET['id'];
	$id = mysql_real_escape_string($id);
	$action = $_GET['action'];
	$action = mysql_real_escape_string($action);
	$catid = $_GET['catid'];
	$catid = mysql_real_escape_string($catid);

	if (checkCookie()) {
		if(preg_match("/^save$/",$action)) {
			saveEntry($id);
		} elseif(preg_match("/^unread$/",$action)) {
			markEntryUnread($id);
		} else {
			markEntryRead($id);
		}

		printEntrybar($id,$catid);
	}

	showEntry($id); 

?>
</body>
</html>

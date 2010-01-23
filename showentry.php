<?php
        include_once("header.php");
	include_once("db.php");
	include_once("sourlib.php");

	$id = $_GET['id'];
	$id = mysql_real_escape_string($id);
	$action = $_GET['action'];
	$action = mysql_real_escape_string($action);

	if (checkCookie()) {
		if(preg_match("/^save$/",$action)) {
			saveEntry($id);
		} elseif(preg_match("/^unread$/",$action)) {
			markEntryUnread($id);
		} else {
			markEntryRead($id);
		}
	
		$feedid = getFeedID($id);
		$feedname = getFeedName($feedid);

		echo "<p class=\"menusecond\"><a href=\"showfeed.php?feedid=$feedid\">&#171; $feedname</a> | <a href=\"showentry.php?action=save&id=$id\">save</a> | <a href=\"showentry.php?action=unread&id=$id\">mark unread</a></p>";
	}

	showEntry($id); 

?>
</body>
</html>

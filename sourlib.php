<?php

// sour reader main library
// steve "mookie" kong
// http://ultramookie.com
//
// licensed under gplv3
// http://www.gnu.org/licenses/gpl-3.0.html

error_reporting(E_ERROR | E_PARSE);

$sitename = getSiteName();
$siteurl = getSiteUrl();

function showUpdateForm() {
	$ua = $_SERVER['HTTP_USER_AGENT'];
        echo "<form action=\"";
        echo $_SERVER['PHP_SELF'];
        echo "\"";
        echo " method=\"post\">";
	echo "<input type=\"text\" name=\"url\" />";
        echo "<input type=\"hidden\" name=\"checksubmit\" value=\"1\">";
        echo "<input type=\"submit\" name=\"submit\" value=\"shorten\" id=\"submitbutton1\">";
        echo "</form>";
}


function showFeedsFrontPage() {

	$query = "select count(*) from main where status='N';";
        $result = mysql_query($query);
	$row = mysql_fetch_array($result);
	$count = $row['count(*)'];

	echo "<a href=\"showfeed.php?feedid=all\"><b>all new ($count)</b></a><br />";

        $query = "select catid, catname from categories order by catname";
        $result = mysql_query($query);

        while ($row = mysql_fetch_array($result)) {
		$catname=$row['catname'];
		$catid=$row['catid'];

		echo "<b>$catname</b>";
		echo "<ul>";
        	$query = "select feedid, feedname from feeds where feedcat='$catid' order by feedname;";
        	$feedresult = mysql_query($query);

		while ($feedrow = mysql_fetch_array($feedresult)) {
			$feedid = $feedrow['feedid'];
			$feedname = $feedrow['feedname'];

        		$query = "select count(*) from main where status='N' and feedid='$feedid';";
        		$indfeedresult = mysql_query($query);
		
			$indfeedrow = mysql_fetch_array($indfeedresult)	;
	
			$feedcount = $indfeedrow['count(*)'];

			if ($feedcount > 0) {
				echo "<li><a href=\"showfeed.php?feedid=$feedid\">$feedname</a> (<b>$feedcount</b>)</li>";
			}
		}
		echo "</ul>";
        }
}

function showFeed($feedid) {

	if ( preg_match("/^all$/",$feedid) ) {
        	$query = "select id, title, date_format(pubDate, '%r') as time, date_format(pubDate, '%m/%d/%y') as date from main where status='N' order by pubDate DESC";
	} else {
        	$query = "select id, title, date_format(pubDate, '%r') as time, date_format(pubDate, '%m/%d/%y') as date from main where feedid='$feedid' and status='N' order by pubDate DESC";
	}
        $result = mysql_query($query);

        while ($row = mysql_fetch_array($result)) {
		$id=$row['id'];
		$title=$row['title'];
		$time=$row['time'];
		$date=$row['date'];

		print "<a href=\"showentry.php?id=$id\">$title</a> - $time<br />";
        }
}

function addEntry($title,$description,$pubDate,$link,$guid,$feed) {
	$title = mysql_real_escape_string($title);
	$description = mysql_real_escape_string($description);
	$link = mysql_real_escape_string($link);
	$guid = mysql_real_escape_string($guid);

	$query = "select guid,feedid from main where guid='$guid' and feedid='$feed'";
	$status = mysql_query($query);
	if (mysql_num_rows($status) >= 1) {
		echo "already have article $title!<br />";
	} else {
		$timestamp = strtotime($pubDate);
		$mypubDate = date('YmdHis',$timestamp);
		$query = "insert into main (title, description, pubDate, link, guid, feedid, status) values ('$title', '$description', '$mypubDate', '$link', '$guid', '$feed', 'N')";
		$result = mysql_query($query);
		print "adding entry $title (from $feed) <br />";
	}

}

function showLoginForm() {
	echo "<form action=\"";
	echo $_SERVER['PHP_SELF'];
	echo "\"";
	echo " method=\"post\">";
	echo "user: <input type=\"text\" name=\"user\"><br />";
	echo "pass: <input type=\"password\" name=\"pass\"><br />";
	echo "<input type=\"hidden\" name=\"checksubmit\" value=\"1\">";
	echo "<input type=\"submit\" name=\"submit\" value=\"login\">";
	echo "</form>";
	echo "<hr />";
	echo "<a href='forgot.php'>forgot password</a>";
}

function getSecret() {
        $query = "select secret from user limit 1";
        $result = mysql_query($query);

        $row = mysql_fetch_array($result);

        return($row['secret']);
}

function getCookie() {
        $query = "select cookie from user limit 1";
        $result = mysql_query($query);

        $row = mysql_fetch_array($result);

        return($row['cookie']);
}

function checkCookie() {
	$secret = getSecret();
	$cookie = $_COOKIE['sourreader'];
	$user = $_COOKIE['user'];
	$storedcookie = getCookie();

	$loggedin = 0;

	$test = sha1($user . $secret);

	if ( (strlen($cookie) > 0) && ($cookie == $storedcookie) && ($cookie == $test) ) {
		$loggedin = 1;
	}

	return $loggedin;
}

function getUserName() {
	if(checkCookie()) {
		$name = $_COOKIE['user'];
	} else {
		$name = "not logged in";
	}
	return $name;
}

function getEmail() {
        $query = "select email from user limit 1";
        $result = mysql_query($query);

        $row = mysql_fetch_array($result);

        return($row['email']);
}

function getUser() {
        $query = "select name from user limit 1";
        $result = mysql_query($query);

        $row = mysql_fetch_array($result);

        return($row['name']);
}

function getSiteName() {
	$query = "select name from site limit 1";
	$result = mysql_query($query);

	$row = mysql_fetch_array($result);

	return($row['name']);
}

function getSiteUrl() {
	$query = "select url from site limit 1";
	$result = mysql_query($query);

	$row = mysql_fetch_array($result);

	return("http://" . $row['url']);
}

function getRawSiteURl() {
	$query = "select url from site limit 1";
	$result = mysql_query($query);

	$row = mysql_fetch_array($result);

	return($row['url']);
}

function setLoginCookie($user) {
		$secret = getSecret();
                $login = sha1($user . $secret);
                $expiry = time()+60*60*24*30;
		setcookie('user',$user,"$expiry");
                setcookie('sourreader',$login,"$expiry");

	        $query = "update user set cookie='$login' where name='$user'";
        	$result = mysql_query($query);
}

function killCookie() {
	if(checkCookie()) {
		$expiry = time() - 4800;
		setcookie('user','',"$expiry");
		setcookie('sourreader','',"$expiry");
	}
}

function checkLogin($user,$pass) {
        $salt = substr("$user",0,2);
        $epass = crypt($pass,$salt);

	$query = "select * from user where name='$user' and pass='$epass'";
	$result = mysql_query($query);

	if (mysql_num_rows($result)==1) {
		return 0;
	} else {
		return 1;
	}
}

function showAddform() {
	echo "<form action=\"";
	echo $_SERVER['PHP_SELF'];
	echo "\"";
	echo " method=\"post\">";
	echo "user: <input type=\"text\" name=\"user\"><br />";
	echo "email: <input type=\"text\" name=\"email\"><br />";
	echo "password: <input type=\"password\" name=\"pass1\"><br />";
	echo "password (again): <input type=\"password\" name=\"pass2\"><br />";
	echo "<input type=\"hidden\" name=\"checksubmit\" value=\"1\">";
	echo "<input type=\"submit\" name=\"submit\" value=\"install\">";
	echo "</form>";
}

function showSettingsform() {
	$query = "select purgedays from site";
	$result = mysql_query($query);
	$row = mysql_fetch_array($result);

	$purgedays = $row['purgedays'];
	
	echo "<p><b>general site settings:</b></p>";
	echo "<form action=\"";
	echo $_SERVER['PHP_SELF'];
	echo "\"";
	echo " method=\"post\">";
	echo "purge after: <input type=\"text\" name=\"purgedays\" value=\"$purgedays\" \"> days<br />";
	echo "<input type=\"hidden\" name=\"checksubmit\" value=\"1\">";
	echo "<input type=\"submit\" name=\"submit\" value=\"update\">";
	echo "</form>";
}

function showFeedsform() {
	echo "<p><b>add feed:</b></p>";
	echo "<form action=\"";
	echo $_SERVER['PHP_SELF'];
	echo "\"";
	echo " method=\"post\">";
	echo "feed name: <input type=\"text\" name=\"site\" \"><br />";
	echo "feed url: <input type=\"text\" name=\"url\" \"><br />";
	echo "<input type=\"hidden\" name=\"checksubmit\" value=\"1\">";
	echo "<input type=\"submit\" name=\"submit\" value=\"update\">";
	echo "</form>";
}

function showCatform() {

	echo "<ul>";
	$query = "select catid,catname from categories order by catname";
        $status = mysql_query($query);

       	while ($row = mysql_fetch_array($status)) {
		$catname = $row['catname'];
		$catid = $row['catid'];
		print "<li><b>$catname</b> [<a href=\"deletecat.php?catid=$catid\">d</a>][<a href=\"editcat.php?catid=$catid\">e</a>]</li>";
	}
	echo "</ul>";
}

function showForgotform() {
        echo "Please enter the following information to reset your password: <br />";
        echo "<form action=\"";
        echo $_SERVER['PHP_SELF'];
        echo "\"";
        echo " method=\"post\">";
        echo "user: <input type=\"text\" name=\"user\"><br />";
        echo "email: <input type=\"text\" name=\"email\"><br />";
        echo "<input type=\"hidden\" name=\"checksubmit\" value=\"1\">";
        echo "<input type=\"submit\" name=\"submit\" value=\"Reset Password\">";
        echo "</form>";
}


function generateCode($length=16) {
	$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPRQSTUVWXYZ0123456789";
        $code = "";
        $clen = strlen($chars) - 1;
        while (strlen($code) < $length) {
		$code .= $chars[mt_rand(0,$clen)];
        }
        return $code;
}

function showPasswordChangeform() {
	$username = getUserName();
	echo "changing password for ";
	echo $username;
	echo "<form action=\"";
	echo $_SERVER['PHP_SELF'];
	echo "\"";
	echo " method=\"post\">";
	echo "old pass: <input type=\"password\" name=\"oldpass\"><br />";
	echo "new pass: <input type=\"password\" name=\"newpass1\"><br />";
	echo "new pass (again): <input type=\"password\" name=\"newpass2\"><br />";
	echo "<input type=\"hidden\" name=\"checksubmit\" value=\"1\">";
	echo "<input type=\"submit\" name=\"submit\">";
	echo "</form>";
}

function purgeOldArticles() {
        $query = "select purgedays from site";
	$result = mysql_query($query);
        
	$row = mysql_fetch_array($result);

	$purgedays = $row['purgedays'];

	$query = "delete from main where date_sub(curdate(), interval $purgedays day) >= pubDate AND status='R'";
	$result = mysql_query($query);
}

function changePass($user,$pass) {
	$email = getEmail();
        $salt = substr("$user",0,2);
        $epass = crypt($pass,$salt);

	$query = "update user set pass='$epass' where name='$user'";
	$result = mysql_query($query);

	echo "password has been updated!";
}

function markEntryRead($id) {

	$query = "update main set status='R' where id='$id'";
	$result = mysql_query($query);
}

function showEntry($id) {

        $query = "select title, date_format(pubDate, '%r') as time, date_format(pubDate, '%m/%d/%y') as date, link, description from main where id='$id'";
	$result = mysql_query($query);
        
	$row = mysql_fetch_array($result);
	$title = $row['title'];
	$time = $row['time'];
	$date = $row['date'];
	$link = $row['link'];
	$description = $row['description'];

	echo "<h3><a href=\"$link\" target=\"_new\" >$title</a></h3>";
	echo "$time ($date)<br /><br />";
	echo "$description";

}	

function deleteCat($catid) {

	$query = "select count(*) from categories";
	$result = mysql_query($query);
	$row = mysql_fetch_array($result);

	$numcats = $row['count(*)'];

	if ( ($numcats > 1) && ($catid != 1) ) {
		$query = "update feeds set feedcat='1' where feedcat='$catid'";
		$result = mysql_query($query);

		$query = "delete from categories where catid='$catid'";
		$result = mysql_query($query);

		echo "category deleted.";
	} elseif ($catid == 1) {
		echo "can't remove the first category!";
	} else {
		echo "can't remove last category!";
	}
}

function deleteFeed($feedid) {
	
	$query = "delete from main where feedid='$feedid'";
	$result = mysql_query($query);
	
	$query = "delete from feeds where feedid='$feedid'";
	$result = mysql_query($query);

	echo "feed removed!";
}

function showCatAddform() {
	echo "<p><b>add a category:</b></p>";
	echo "<form action=\"";
	echo $_SERVER['PHP_SELF'];
	echo "\"";
	echo " method=\"post\">";
	echo "category name: <input type=\"text\" name=\"cat\" \"><br />";
	echo "<input type=\"hidden\" name=\"checksubmit\" value=\"1\">";
	echo "<input type=\"submit\" name=\"submit\" value=\"update\">";
	echo "</form>";
}

function showEditCatform($catid) {

	$query = "select catname from categories where catid='$catid'";
	$result = mysql_query($query);
	$row = mysql_fetch_array($result);

	$catname = $row['catname'];

	echo "<form action=\"";
	echo $_SERVER['PHP_SELF'];
	echo "\"";
	echo " method=\"post\">";
	echo "category name: <input type=\"text\" name=\"catname\" value=\"" . $catname . "\"><br />";
	echo "<input type=\"hidden\" name=\"catidp\" value=\"" . $catid ."\">";
	echo "<input type=\"hidden\" name=\"checksubmit\" value=\"1\">";
	echo "<input type=\"submit\" name=\"submit\" value=\"change\">";
	echo "</form>";
}

function showFeedEditform($feedid) {

	$query = "select feedname, feedurl, feedcat from feeds where feedid='$feedid'";
	$result = mysql_query($query);
	$row = mysql_fetch_array($result);

	$feedname = $row['feedname'];
	$feedurl = $row['feedurl'];
	$feedcat = $row['feedcat'];
	$catname = $row['categories.catname'];

	$query = "select catid, catname from categories order by catname";
	$result = mysql_query($query);

	echo "<form action=\"";
	echo $_SERVER['PHP_SELF'];
	echo "\"";
	echo " method=\"post\">";
	echo "feed name: <input type=\"text\" name=\"feedname\" value=\"" . $feedname . "\"><br />";
	echo "feed url: <input type=\"text\" name=\"feedurl\" value=\"" . $feedurl . "\"><br />";
	echo "feed category: <select name=\"catid\">";
       	while ($row = mysql_fetch_array($result)) {
		$catname = $row['catname'];
		$catid = $row['catid'];

		if ($feedcat == $catid) {
			$selected = " selected ";
		} else {
			$selected = " ";
		}
		echo "<option value=\"$catid\" $selected>$catname</option>";
	}
	echo "</select>";
	echo "<input type=\"hidden\" name=\"feedidp\" value=\"$feedid\">";
	echo "<input type=\"hidden\" name=\"checksubmit\" value=\"1\">";
	echo "<input type=\"submit\" name=\"submit\" value=\"change\">";
	echo "</form>";
}

function changeCatName($catid,$catname) {

	$catid = mysql_real_escape_string($catid);
	$catname = mysql_real_escape_string($catname);

	$query = "update categories set catname='$catname' where catid='$catid'";
	$result = mysql_query($query);

	echo "category name changed to $catname!";
}

function changeFeed($feedidp,$feedname,$feedurl,$catid) {

	$feedidp = mysql_real_escape_string($feedidp);
	$feedname = mysql_real_escape_string($feedname);
	$feedurl = mysql_real_escape_string($feedurl);
	$catid = mysql_real_escape_string($catid);

	$query = "update feeds set feedname='$feedname', feedurl='$feedurl', feedcat='$catid' where feedid='$feedidp'";
	$result = mysql_query($query);

	echo "$feedname updated!";
}

function markFeedRead($feedid) {

	if ( preg_match("/^all$/",$feedid) ) {
		$query = "update main set status='R' where status='N'";
	} else {
		$query = "update main set status='R' where feedid='$feedid' and status='N'";
	}
	$result = mysql_query($query);

}

function changeSettings($purgedays) {

        $purgedays = mysql_real_escape_string($purgedays);

	$query = "update site set purgedays='$purgedays' limit 1";
	$result = mysql_query($query);

	echo "your settings have been updated!";

}

function addFeed($site,$url) {

        $site = mysql_real_escape_string($site);
        $url = mysql_real_escape_string($url);

	$query = "select url from feeds where url='$url'";
	$status = mysql_query($query);
	if (mysql_num_rows($status) >= 1) {
		echo "already subscribed to $url!";
	} else {
		$query = "insert into feeds (feedname,feedurl,feedcat) values ('$site', '$url', '1')";
		$result = mysql_query($query);
		echo "$site has been added!";
	}
}

function addCat($cat) {

        $cat = mysql_real_escape_string($cat);

	$query = "select catid from categories where catname='$cat'";
	$status = mysql_query($query);
	if (mysql_num_rows($status) >= 1) {
		echo "already have a category named $cat!";
	} else {
		$query = "insert into categories (catname) values ('$cat')";
		$result = mysql_query($query);
		echo "$cat has been added!";
	}
}

function addUser($user,$email,$pass) {
        $salt = substr("$user",0,2);
        $epass = crypt($pass,$salt);

	$query = "select * from user";
	$status = mysql_query($query);

	if (mysql_num_rows($status) >= 1) {
		echo "already installed!";
	} else {
		$user = mysql_real_escape_string($user);
		$email = mysql_real_escape_string($email);
		$pass = mysql_real_escape_string($pass);
		
		$query = "create table user ( name varchar(30) NOT NULL, email varchar(30) NOT NULL, pass varchar(30) NOT NULL, secret varchar(6), cookie varchar(300) )";
		$status = mysql_query($query);

		$query = "create table main ( id int NOT NULL AUTO_INCREMENT, title varchar(1024) NOT NULL, description varchar(50000), pubDate DATETIME NOT NULL, link varchar(1024) NOT NULL, guid varchar(1024) NOT NULL, status varchar(2), feedid int NOT NULL, PRIMARY KEY (id)); ";
		$status = mysql_query($query);
		
		$query = "create table feeds ( feedid int NOT NULL AUTO_INCREMENT, feedname varchar(1024) NOT NULL, feedurl varchar(1024) NOT NULL, feedcat int NOT NULL, PRIMARY KEY(feedid) ); ";
		$status = mysql_query($query);
		
		$query = "create table categories ( catid int NOT NULL AUTO_INCREMENT, catname varchar(1024) NOT NULL, PRIMARY KEY(catid)); ";
		$status = mysql_query($query);
	
		$query = "create table site ( purgedays int NOT NULL ); ";
		$status = mysql_query($query);
		
		$query = "insert into site (purgedays) values ('30')";
		$status = mysql_query($query);
		
		$query = "insert into categories (catname) values ('general')";
		$status = mysql_query($query);
		
		$secret = generateCode();
	
		$query = "insert into user (name,email,pass,secret) values ('$user','$email','$epass','$secret')";
		$status = mysql_query($query);
	
		echo "sour reader installed!  thanks!";
	}
}

function sendRandomPass($email,$func) {
	$query = "select user from user where email='$email'";
	$status = mysql_query($query);
        $row = mysql_fetch_array($status);

	$user = $row['status'];
	
        $pass = generateCode();
	$salt = substr("$user",0,2);
	$epass = crypt($pass,$salt);

	$email = mysql_real_escape_string($email);
	
	$to = "$email";
	$from = "From: enter@something.here.or.else.tld";
	$subject = "password";
	$body = "hi, your password is $pass. please login using your email address and the password.  feel free to change your password at anytime.";
	if (mail($to, $subject, $body, $from)) {
		if ((strcmp($func,"new")) == 0) {
			$query = "insert into user (email,pass) values ('$email','$epass')";
			$status = mysql_query($query);
		} else if ((strcmp($func,"lost")) == 0) {
			$query = "update user set pass='$epass' where email='$email'";
			$status = mysql_query($query);
		} else {
			echo "nothing to do!";
		}

		echo "<p>Your new password has been sent!  <a href='login.php'>login</a> after you receive your password.</p>";
	} else {
		echo("<p>Message delivery failed...</p>");
	}
}


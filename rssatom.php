<?php
include_once("db.php");
include_once("sourlib.php");

// For clean errors on xml parsing
libxml_use_internal_errors(true);

// Useragent
$useragent = "SourReaderFeedUpdater/1.0 (http://github.com/ultramookie/sour-reader)";

$ns = array
(
        'content' => 'http://purl.org/rss/1.0/modules/content/'
); 

$query = "select feedid, feedurl from feeds order by feedid";
$status = mysql_query($query);

while($row = mysql_fetch_array($status))
{
	$url = $row['feedurl'];
	$id = $row['feedid'];

        $session = curl_init();
        curl_setopt ( $session, CURLOPT_URL, $url );
        curl_setopt ( $session, CURLOPT_RETURNTRANSFER, TRUE );
        curl_setopt ( $session, CURLOPT_CONNECTTIMEOUT, 2 );
	curl_setopt ( $session, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt ( $session, CURLOPT_USERAGENT, $useragent );
	curl_setopt ( $session, CURLOPT_ENCODING, "gzip" );
        $result = curl_exec ( $session );
        curl_close( $session );
        
	$xml = simplexml_load_string($result, 'SimpleXMLElement', LIBXML_NOCDATA);

	if ($result == FALSE) {
		print "<hr />There was an issue fetching $url<hr />";
	} elseif (!$xml) {
		print "<hr />$url did not return valid xml!<hr />";
		$title = "$url did not return valid xml!";
		$description = "$url did not return valid xml!";
		$content = "$url did not return valid xml!";
		$link = "$url";
		$pubDate = date('c');
		$updateTime = date('c');
		$guid = $link . date('c');
		addEntry($title,$description,$pubDate,$link,$guid,$id,$updateTime);
	} elseif ($xml->channel->item) {
		foreach ($xml->channel->item as $result) {
			$title = $result->title;
			$description = $result->description;
			$pubDate =  $result->pubDate;
			$link =  $result->link;
			$guid = $result->guid;
			$updateTime = date('c');
			$content = $result->children($ns['content']);
			if ($content) {
				$description = $content;
			}
			addEntry($title,$description,$pubDate,$link,$guid,$id,$updateTime);
		}
	} elseif ($xml->entry) {
		foreach ($xml->entry as $result) {
			$title = $result->title;
			$description = $result->summary;
			$pubDate = $result->updated;
			$link = $result->link['href'];
			$guid = $result->id;
			$updateTime = date('c');
			$content = $result->content;
			addEntry($title,$description,$pubDate,$link,$guid,$id,$updateTime);
		}
	} else {
			$title = "$url is not a valid rss or atom feed!";
			$description = "$url is not a valid rss or atom feed!";
			$content = "$url is not a valid rss or atom feed!";
			$link = "$url";
			$pubDate = date('c');
			$updateTime = date('c');
			$guid = $link . date('c');
			addEntry($title,$description,$pubDate,$link,$guid,$id,$updateTime);
	}
		


}

?>

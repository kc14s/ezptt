<?
require_once("init.php");
require_once("reddit_lib.php");
$is_spider = is_spider();
$is_from_search_engine = is_from_search_engine();

$subreddit = $_GET['subreddit'];
$id = (int)$_GET['id'];
$db_conn = conn_reddit_db();
list($domain, $created, $title, $ups, $author, $url, $selftext) = execute_vector("select domain, created, title, ups, author, url, selftext from topic where id = $id");
$html_title = "$title $author";
$lz = $author;
if ($is_spider) {
	$created = date("Y-m-d").substr($created, 10);
}
$articles[] = array($author, $ups, $selftext, $created, $domain, $url);
$result = mysql_query("select author, ups, body, created from reply where tid = $id order by ups desc");
while(list($author, $ups, $body, $created) = mysql_fetch_array($result)) {
	if ($is_spider) {
		$created = date("Y-m-d").substr($created, 10);
	}
	$articles[] = array($author, $ups, $body, $created);
}
if (count($articles) == 1 && $is_spider) {
	$articles[] = $articles[0];
}

$html .= "<div class=\"col-md-8 col-md-offset-2 col-xs-12\"><ol class=\"breadcrumb\"><li><a href=\"/\">reddit</a></li><li>$subreddit</li></ol><h3>$title</h3>";
if (!$is_loyal_user) {
//	$html .= $google_320_100;
//	$html .= $chitika_468_60;
//	$html .= $bloggerads_banner;
//	$html .= $scupio_728_90;
//	$html .= $adcash_popunder;
}
$floor = 1;
foreach ($articles as $article) {
	list($author, $ups, $selftext, $created, $domain, $url) = $article;
	$html .= '<div class="panel panel-info">';
	$html .= '<div class="panel-heading">';
	$html .= $author;
	$html .= " &nbsp; &nbsp; <span class=\"glyphicon glyphicon-thumbs-up\"></span> $ups";
	$html .= "<span class=\"pull-right\">$created</span>";
	$html .= '</div>';
	$html .= '<div class="panel-body">';
	if (isset($domain)) {
		if ($domain == 'i.imgur.com') {
			$img_url = $url;
		}
		else if ($domain == 'imgur.com') {
			if (preg_match("/imgur.com\/([a-zA-Z0-9.]+)$/", $url, $matches)) {
				if (strpos($matches[1], '.') > 0) {
					$img_url = "http://i.imgur.com/".$matches[1];
				}
				else {
					$img_url = "http://i.imgur.com/".$matches[1].'.jpg';
				}
			}
		}
		if (isset($img_url)) {
			$html .= "<img data-original=\"$img_url\" class=\"img-responsive\" />";
		}
		else {
			if (strpos($domain, 'self.') === false && strlen($url) > 5) {
				$html .= "<a href=\"$url\" target=\"_blank\">$url</a><br />";
			}
		}
	}
	else {
		$img_url = get_img_url($selftext, 2);
		if (isset($img_url)) {
			$html .= "<img data-original=\"$img_url\" class=\"img-responsive\" />";
		}
	}
	$html .= $selftext;
	$html .= '</div>';
	$html .= '</div>';
	if (!$is_loyal_user) {
		if ($floor == 1 || $floor == 2) {
//			$html .= $scupio_728_90;
		}
		else if ($floor == 3) {
//			$html .= $digitalpoint_468_60;
//			$html .= $bloggerads_banner;
		}
	}
	++$floor;
}
/*
if (isset($prev_topics)) {
		$html .= '<div class="panel panel-default"><div class="panel-heading">'.i18n('jixuyuedu').'</div>';
		$html .= '<div class="list-group">';
		foreach ($prev_topics as $topic) {
				list($title, $tid1, $tid2) = $topic;
				$html .= "<a href=\"/article/$en_name/$tid1/$tid2\" class=\"list-group-item\">".i18n($title)."</a>";
		}
		$html .= '</div></div>';
}
*/
if (false || $is_spider) {
	$html .= get_rand_reddit_topic_html();
}
if (true || $is_spider) {
#	$html .= get_old_ck101_topic_html();
}
//$html .= '<p><a href="/">PTT</a> <a href="/disp">disp</a></p></div>';
//$html .= '<script type="text/javascript">var zx_aid = 1;var zx_uid = 10799;var zoneid = 11554;</script><script type="text/javascript" charset="utf-8" src="http://click.9cpc.com/view.js"></script>';
$html .= '</div>';

require_once('header.php');
echo $html;
require_once('footer.php');
?>


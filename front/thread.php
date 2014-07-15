<?
require_once("init.php");
require_once("i18n.php");
$is_spider = is_spider();
$is_from_search_engine = is_from_search_engine();
if ($ptt_allow == 0 && !$is_spider && !$is_from_search_engine) {
	header('HTTP/1.1 404 Not Found');
	exit();
}
$en_name = $_GET['en_name'];
$tid1 = (int)$_GET['tid1'];
$tid2 = $_GET['tid2'];
$db_conn = conn_ezptt_db();
$bid = execute_scalar("select id from board where en_name = '$en_name'");
list($author, $pub_time, $title, $content, $attachment) = execute_vector("select author, pub_time, title, content, attachment from topic where tid1 = $tid1 and tid2 = '$tid2'");
$title = i18n($title);
$topic_title = $title;
$html_title = "$title $author";
$lz = $author;
$topic_pub_time = $pub_time;
$attachments = array();
$nick = i18n(execute_scalar("select nick from user where user_id = '$author'"));
if (true || $attachment) {
	$attachments = execute_column("select concat(md5, '.', ext_name) from attachment where bid = $bid and tid1 = $tid1 and tid2 = '$tid2'");
}
$articles[] = array($author, $pub_time, $content, $attachments, $nick, true);
$result = mysql_query("select author, reply_time, content from reply where tid1 = $tid1 and tid2 = '$tid2'");
while(list($author, $reply_time, $content) = mysql_fetch_array($result)) {
	$nick = i18n(execute_scalar("select nick from user where user_id = '$author'"));
	$author_link = execute_scalar("select count(*) from topic where author = '$author'") > 5;
	$articles[] = array($author, $reply_time, $content, array(), $nick, $author_link);
}
$result = mysql_query("select title, tid1, tid2, author from topic where bid = $bid and pub_time <= '$topic_pub_time' and tid1 <> $tid1 order by pub_time desc limit 10");
while (list($prev_title, $prev_tid1, $prev_tid2, $prev_author) = mysql_fetch_array($result)) {
	$prev_topics[] = array($prev_title, $prev_tid1, $prev_tid2, $prev_author);
}

if (false || $is_spider) {
	list($tid_max, $tid_min) = execute_vector('select max(tid1), min(tid1) from topic');
	$result = mysql_query('select bid, title, author, tid1, tid2 from topic where tid1 > '.rand($tid_min, $tid_max).' order by tid1 limit 10');
	while (list($bid, $title, $author, $tid1, $tid2) = mysql_fetch_array($result)) {
		$old_en_name = execute_scalar("select en_name from board where id = $bid");
		$old_topics[] = array($old_en_name, $title, $author, $tid1, $tid2);
	}
}

if (!$is_spider) {
	$html .= $scupio_video_expand;
}
$html .= "<div class=\"col-md-8 col-md-offset-2 col-xs-12\"><ol class=\"breadcrumb\"><li><a href=\"/\">PTT</a></li><li><a href=\"/board/$en_name/\">$en_name</a></li></ol><h3>".i18n($topic_title)."</h3>";
if (!$is_loyal_user) {
//	$html .= $google_320_100;
//	$html .= $chitika_468_60;
//	$html .= $bloggerads_banner;
	$html .= $scupio_728_90;
	$html .= $adcash_popunder;
}
$floor = 1;
foreach ($articles as $article) {
	list($author, $time, $content, $attachments, $nick, $author_link) = $article;
	$html .= '<div class="panel panel-info">';
	$html .= '<div class="panel-heading">';
	if ($author_link) {
		$html .= ($author === $lz ? i18n('louzhu') : i18n('zuozhe')).": <a href=\"/author/$author\">$author</a>".(isset($nick) && strlen($nick) > 0 ? " ($nick)" : '');
	}
	else {
		$html .= ($author === $lz ? i18n('louzhu') : i18n('zuozhe')).": $author".(isset($nick) && strlen($nick) > 0 ? " ($nick)" : '');
	}
	$html .= " &nbsp; <span class=\"pull-right\">$time</span>";
	$html .= '</div>';
	$html .= '<div class="panel-body">';
	$content = i18n(preg_replace("/\n+/", "\n", trim($content)));
	if (strlen($content) < 1000 && !(strpos($content, 'http://') === false)) {
		$content = preg_replace("/(http:\/\/[\w\/\.\_\-]+\.jpg)/", "<br><a href=\"$1\" target=\"_blank\"><img data-original=\"$1\" class=\"img-responsive\" /></a>", $content);
		$content = preg_replace("/(http:\/\/[\w\/\.\_\-]+\.png)/", "<br><a href=\"$1\" target=\"_blank\"><img data-original=\"$1\" class=\"img-responsive\" /></a>", $content);
		$content = preg_replace("/(http:\/\/[\w\/\.\_\-]+\.gif)/", "<br><a href=\"$1\" target=\"_blank\"><img data-original=\"$1\" class=\"img-responsive\" /></a>", $content);
		$content = preg_replace("/(http:\/\/ppt.cc[\w\/\.\_\-]+)/", "<br><a href=\"$1\" target=\"_blank\"><img data-original=\"$1@.jpg\" class=\"img-responsive\" /></a>", $content);
		$content = preg_replace("/http:\/\/(imgur.com[\w\/\.\_\-]+)/", "<br><a href=\"$1\" target=\"_blank\"><img data-original=\"http://i.$1.jpg\" class=\"img-responsive\" /></a>", $content);
		$content = preg_replace("/http:\/\/miupix.cc\/pm\-(\w+)/", "<br><a href=\"http://miupix.cc/dm/$1/uploadFromiPhone.jpg\" target=\"_blank\"><img data-original=\"http://miupix.cc/dm/$1/uploadFromiPhone.jpg\" class=\"img-responsive\" /></a>", $content);
	}
	//$content = preg_replace("/(http:\/\/ppt.cc[\w\/\.\_\-]+)</", "<a href=\"$1\" target=\"_blank\"><img src=\"$1@.jpg\" /></a><", $content);
	$html .= str_replace("\n", '<br>', $content);
	if (isset($attachments) && count($attachments) > 0) {
		foreach ($attachments as $attachment) {
			if (file_exists("att/$attachment")) {
				$html .= "<br><img data-original=\"$static_host/att/$attachment\" class=\"img-responsive\" />";
			}
		}
	}
	$html .= '</div>';
	$html .= '</div>';
	if (!$is_loyal_user) {
		if ($floor == 1 || $floor == 2) {
			$html .= $scupio_728_90;
		}
		else if ($floor == 3) {
			$html .= $digitalpoint_468_60;
//			$html .= $bloggerads_banner;
		}
	}
	++$floor;
}
if (isset($prev_topics)) {
		$html .= '<div class="panel panel-default"><div class="panel-heading">'.i18n('jixuyuedu').'</div>';
		$html .= '<div class="list-group">';
		foreach ($prev_topics as $topic) {
				list($title, $tid1, $tid2, $author) = $topic;
				$html .= "<a href=\"/article/$en_name/$tid1/$tid2\" class=\"list-group-item\">".i18n($title)."<span class=\"pull-right\">$author</span></a>";
		}
		$html .= '</div></div>';
}
if (isset($old_topics)) {
		$html .= '<div class="panel panel-default"><div class="panel-heading">'.i18n('jixuyuedu').'</div>';
		$html .= '<div class="list-group">';
		foreach ($old_topics as $topic) {
				list($en_name, $title, $author, $tid1, $tid2) = $topic;
				$html .= "<a href=\"/article/$en_name/$tid1/$tid2\" class=\"list-group-item\">".i18n($title)." $author</a>";
		}
		$html .= '</div></div>';
}
if (false || $is_spider) {
		$html .= get_rand_reddit_topic_html();
}
if (false || $is_spider) {
	$html .= get_old_ck101_topic_html();
}
$html .= '<p><a href="/">PTT</a> <a href="/disp">disp</a></p></div>';
//$html .= '<script type="text/javascript">var zx_aid = 1;var zx_uid = 10799;var zoneid = 11554;</script><script type="text/javascript" charset="utf-8" src="http://click.9cpc.com/view.js"></script>';

require_once('header.php');
echo $html;
require_once('footer.php');
?>


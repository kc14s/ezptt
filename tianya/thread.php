<?
require_once("init.php");
$is_spider = is_spider();
$is_from_search_engine = is_from_search_engine();
$en_name = $_GET['en_name'];
$tid = (int)$_GET['tid'];
$db_conn = conn_tianya_db();
$cn_name = execute_scalar("select cn_name from tianya.board where en_name = '$en_name'");
list($title, $click, $reply, $pub_time) = execute_vector("select title, click, reply, pub_time from thread where tid = $tid");
$real_pub_time = $pub_time;
if ($is_spider) {
	$pub_time = date("Y-m-d").substr($pub_time, 10);
}
$topic_pub_time = $pub_time;
$result = mysql_query("select reply.uid, user_name, pub_time, content from reply, user where reply.uid = user.uid and en_name = '$en_name' and tid = $tid");
while(list($uid, $user_name, $pub_time, $content) = mysql_fetch_array($result)) {
	if ($is_spider) {
		$reply_time = date("Y-m-d").substr($reply_time, 10);
	}
	$articles[] = array($uid, $user_name, $pub_time, $content);
	if (!isset($lz)) {
		$lz = $user_name;
		$html_title = "$title $lz 天涯";
	}
}

$result = mysql_query("select title, tid, click, reply, user_name from thread, user where thread.uid = user.uid and en_name = '$en_name' and pub_time < '$real_pub_time' order by pub_time desc limit 10");
while (list($prev_title, $prev_tid, $prev_click, $prev_reply, $prev_user_name) = mysql_fetch_array($result)) {
	$prev_topics[] = array($prev_title, $prev_tid, $prev_click, $prev_reply, $prev_user_name);
}

if (false || !$is_spider) {
	$html .= $scupio_video_expand;
}
$html .= "<div class=\"col-md-8 col-md-offset-2 col-xs-12\"><ol class=\"breadcrumb\"><li>天涯</li><li>$cn_name</li></ol><h3>$title</h3>";
if (false || !$is_loyal_user) {
//	$html .= $google_320_100;
//	$html .= $chitika_468_60;
//	$html .= $bloggerads_banner;
	$html .= $scupio_728_90;
	$html .= $adcash_popunder;
}
$floor = 1;
foreach ($articles as $article) {
	list($uid, $user_name, $pub_time, $content) = $article;
	$html .= '<div class="panel panel-info">';
	$html .= '<div class="panel-heading">';
	/*
	if ($author_link) {
		$html .= ($author === $lz ? i18n('louzhu') : i18n('zuozhe')).": <a href=\"/author/$author\">$author</a>".(isset($nick) && strlen($nick) > 0 ? " ($nick)" : '');
	}
	else {
		$html .= ($author === $lz ? i18n('louzhu') : i18n('zuozhe')).": $author".(isset($nick) && strlen($nick) > 0 ? " ($nick)" : '');
	}
	*/
	if ($user_name == $lz) {
		$html .= '楼主：';
	}
	else {
		$html .= '作者：';
	}
	$html .= "$user_name &nbsp; <span class=\"pull-right\">$pub_time</span>";
	$html .= '</div>';
	$html .= '<div class="panel-body">';
	//$content = i18n(preg_replace("/\n+/", "\n", trim($content)));
	//$content = preg_replace("/(http:\/\/ppt.cc[\w\/\.\_\-]+)</", "<a href=\"$1\" target=\"_blank\"><img src=\"$1@.jpg\" /></a><", $content);
	//$html .= str_replace("\n", '<br>', $content);
	$html .= $content;
	$html .= '</div>';
	$html .= '</div>';
	if (!$is_loyal_user) {
		if ($floor == 1 || $floor == 2) {
			$html .= $scupio_728_90;
		}
		else if ($floor == 3) {
			$html .= $digitalpoint_468_60;
			$html .= $bloggerads_banner;
		}
	}
	++$floor;
}
if (true && isset($prev_topics)) {
		$html .= '<div class="panel panel-default"><div class="panel-heading">继续阅读</div>';
		$html .= '<div class="list-group">';
		foreach ($prev_topics as $topic) {
				list($prev_title, $prev_tid, $prev_click, $prev_reply, $prev_user_name) = $topic;
				$html .= "<a href=\"/thread/$en_name/$prev_tid\" class=\"list-group-item\">$prev_title<span class=\"pull-right\">$prev_user_name</span></a>";
		}
		$html .= '</div></div>';
}
if (false || $is_spider) {
		$html .= get_rand_tianya_topic_html();
}

require_once('header.php');
echo $html;
require_once('footer.php');
?>


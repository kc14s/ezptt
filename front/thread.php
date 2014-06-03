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
list($author, $pub_time, $title, $content, $attachment) = execute_vector("select author, pub_time, title, content, attachment from topic where bid = $bid and tid1 = $tid1 and tid2 = '$tid2'");
$title = i18n($title);
$topic_title = $title;
$html_title = "$title $author";
$lz = $author;
$topic_pub_time = $pub_time;
$attachments = array();
$nick = i18n(execute_scalar("select nick from user where user_id = '$author'"));
if ($attachment) {
	$attachments = execute_column("select concat(md5, '.', ext_name) from attachment where bid = $bid and tid1 = $tid1 and tid2 = '$tid2'");
}
$articles[] = array($author, $pub_time, $content, $attachments, $nick);
$result = mysql_query("select author, reply_time, content from reply where bid = $bid and tid1 = $tid1 and tid2 = '$tid2'");
while(list($author, $reply_time, $content) = mysql_fetch_array($result)) {
	$nick = i18n(execute_scalar("select nick from user where user_id = '$author'"));
	$articles[] = array($author, $reply_time, $content, array(), $nick);
}
$result = mysql_query("select title, tid1, tid2 from topic where bid = $bid and pub_time <= '$topic_pub_time' and tid1 <> $tid1 order by pub_time desc limit 10");
while (list($prev_title, $prev_tid1, $prev_tid2) = mysql_fetch_array($result)) {
	$prev_topics[] = array($prev_title, $prev_tid1, $prev_tid2);
}

if ($is_spider) {
	list($tid_max, $tid_min) = execute_vector('select max(tid1), min(tid1) from topic');
	$result = mysql_query('select title, tid1, tid2 from topic where tid1 > '.rand($tid_min, $tid_max).' order by tid1 limit 10');
	while (list($title, $tid1, $tid2) = mysql_fetch_array($result)) {
		$old_topics[] = array($title, $tid1, $tid2);
	}
}

$html = "<div class=\"col-md-8 col-md-offset-2 col-xs-12\"><ol class=\"breadcrumb\"><li><a href=\"/\">PTT</a></li><li><a href=\"/board/$en_name/\">$en_name</a></li></ol><h3>".i18n($topic_title)."</h3>";
foreach ($articles as $article) {
	list($author, $time, $content, $attachments, $nick) = $article;
	$html .= '<div class="panel panel-info">';
	$html .= '<div class="panel-heading">';
	$html .= ($author === $lz ? i18n('louzhu') : i18n('zuozhe')).": $author".(isset($nick) && strlen($nick) > 0 ? " ($nick)" : '');
	$html .= " &nbsp; ".substr($time, 5);
	$html .= '</div>';
	$html .= '<div class="panel-body">';
	$content = i18n(preg_replace("/\n+/", "\n", trim($content)));
	$content = preg_replace("/[^\"](http:\/\/[\w\/\.\_\-]+\.jpg)/", "<br><a href=\"$1\" target=\"_blank\"><img data-original=\"$1\" /></a>", $content);
	$content = preg_replace("/[^\"](http:\/\/[\w\/\.\_\-]+\.png)/", "<br><a href=\"$1\" target=\"_blank\"><img data-original=\"$1\" /></a>", $content);
	$content = preg_replace("/[^\"](http:\/\/[\w\/\.\_\-]+\.gif)/", "<br><a href=\"$1\" target=\"_blank\"><img data-original=\"$1\" /></a>", $content);
	$content = preg_replace("/[^\"](http:\/\/ppt.cc[\w\/\.\_\-]+)/", "<br><a href=\"$1\" target=\"_blank\"><img data-original=\"$1@.jpg\" /></a>", $content);
	//$content = preg_replace("/(http:\/\/ppt.cc[\w\/\.\_\-]+)</", "<a href=\"$1\" target=\"_blank\"><img src=\"$1@.jpg\" /></a><", $content);
	$html .= str_replace("\n", '<br>', $content);
	if (isset($attachments) && count($attachments) > 0) {
		foreach ($attachments as $attachment) {
			if (file_exists("att/$attachment")) {
				$html .= "<br><img data-original=\"$static_host/att/$attachment\">";
			}
		}
	}
	$html .= '</div>';
	$html .= '</div>';
}
if (isset($prev_topics)) {
		$html .= '<div class="panel panel-default"><div class="panel-heading">'.i18n('jixuyuedu').'</div>';
		$html .= '<div class="list-group">';
		foreach ($prev_topics as $topic) {
				list($title, $tid1, $tid2) = $topic;
				$html .= "<a href=\"/thread/$en_name/$tid1/$tid2\" class=\"list-group-item\">".i18n($title)."</a>";
		}
		$html .= '</div></div>';
}
if (isset($old_topics)) {
		$html .= '<div class="panel panel-default"><div class="panel-heading">'.i18n('jixuyuedu').'</div>';
		$html .= '<div class="list-group">';
		foreach ($old_topics as $topic) {
				list($title, $tid1, $tid2) = $topic;
				$html .= "<a href=\"/thread/$en_name/$tid1/$tid2\" class=\"list-group-item\">".i18n($title)."</a>";
		}
		$html .= '</div></div>';
}
$html .= '<p><a href="/">PTT</a> <a href="/disp">disp</a></p></div>';
//$html .= '<script type="text/javascript">var zx_aid = 1;var zx_uid = 10799;var zoneid = 11554;</script><script type="text/javascript" charset="utf-8" src="http://click.9cpc.com/view.js"></script>';

require_once('header.php');
echo $html;
require_once('footer.php');
?>


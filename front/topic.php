<?
//disp topic
require_once("init.php");
$db_conn = conn_db();
require_once("i18n.php");
$is_spider = is_spider();
$is_from_search_engine = is_from_search_engine();
if ($ptt_allow == 0 && !$is_spider && !$is_from_search_engine) {
	header('HTTP/1.1 404 Not Found');
	exit();
}
$bid = (int)$_GET['bid'];
$tid = $_GET['tid'];
$en_name = execute_scalar("select en_name from board where id = $bid");
list($author, $pub_time, $title, $content, $attachment) = execute_vector("select author, pub_time, title, content, attachment from topic where bid = $bid and tid = '$tid'");
$title = i18n($title);
$topic_title = $title;
$html_title = "$title $author";
$lz = $author;
$topic_pub_time = $pub_time;
$attachments = array();
if ($attachment) {
	$attachments = execute_column("select file_name from attachment where bid = $bid and tid = '$tid'");
}
$articles[] = array($author, $pub_time, $content, $attachments);
$result = mysql_query("select author, reply_time, content from reply where bid = $bid and tid = '$tid'");
while(list($author, $reply_time, $content) = mysql_fetch_array($result)) {
	$articles[] = array($author, $reply_time, $content);
}
$result = mysql_query("select title, tid from topic where bid = $bid and pub_time <= '$topic_pub_time' and tid <> '$tid' order by pub_time desc limit 10");
while (list($prev_title, $prev_tid) = mysql_fetch_array($result)) {
	$prev_topics[] = array($prev_title, $prev_tid);
}

$html = "<div class=\"col-md-8 col-md-offset-2 col-xs-12\"><a href=\"/disp\">Disp</a> &gt; $en_name<h3>".i18n($topic_title)."</h3>";
$floor = 1;
foreach ($articles as $article) {
	list($author, $time, $content, $attachments) = $article;
	$html .= '<div class="panel panel-info">';
	$html .= '<div class="panel-heading">';
	$html .= ($author === $lz ? i18n('louzhu') : i18n('zuozhe')).": $author (".i18n(execute_scalar("select nick from user where user_id = '$author'")).")";
	$html .= " &nbsp; $time";
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
			if (file_exists("att_ori/$bid.$tid.$attachment")) {
				$html .= "<br><img data-original=\"$static_host/att_ori/$bid.$tid.$attachment\">";
			}
		}
	}
	if ($floor == 1) {
		$html .= '<div class="addthis_sharing_toolbox"></div>';
	}
	$html .= '</div>';
	$html .= '</div>';
	++$floor;
}
if (isset($prev_topics)) {
		$html .= '<div class="panel panel-default"><div class="panel-heading">'.i18n('jixuyuedu').'</div>';
		$html .= '<div class="list-group">';
		foreach ($prev_topics as $topic) {
				list($title, $tid) = $topic;
				$html .= "<a href=\"/topic/$bid/$tid\" class=\"list-group-item\">".i18n($title)."</a>";
		}
		$html .= '</div></div>';
}
$html .= '<p><a href="/">PTT</a> <a href="/disp">disp</a></p></div>';

require_once('header.php');
echo $html;
require_once('footer.php');
?>


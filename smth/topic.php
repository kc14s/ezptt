<?
require_once("init.php");
require_once("i18n.php");
require_once("smth_lib.php");

$board_en_name = $_GET['board'];
$gid = $_GET['gid'];
$db_conn = conn_db();
mysql_select_db('btsm', $db_conn);
mysql_query('set names utf8');

list($bid, $board_cn_name) = execute_vector("select bid, cn_name from board where en_name = '$board_en_name'");
$articles = execute_dataset("select author, nick, content, pub_time, attachments, aid from snapshot where bid = $bid and gid = $gid order by aid");
if (count($articles) == 0) {
	header('HTTP/1.1 301 Moved Permanently');
	header('Location: /404');
}
for ($i = 0; $i < count($articles); ++$i) {
	$articles[$i][1] = i18n($articles[$i][1]);
	$articles[$i][2] = i18n($articles[$i][2]);
}
$title = i18n(execute_scalar("select title from topic where bid = $bid and gid = $gid"));
$lz = $articles[0][1];
$html_title = "$title $lz";

$html = "<div class=\"row\"><div class=\"col-md-8 col-md-offset-2 col-xs-12\"><ol class=\"breadcrumb\"><li><a href=\"/\">".i18n('水木社区').'</a></li><li><a href="/board/'.$board_en_name.'/1">'.i18n($board_cn_name).'</a></li>';
$html .= "</ol><h3>$title</h3>";
$html .= '</div>';	//end of row

$width = true || $is_loyal_user ? 8 : 6;
$offset = true || $is_loyal_user ? 2 : 2;
$html .= '<div class="row">';
$html .= "<div class=\"col-md-$width col-md-offset-$offset col-xs-12\">";

$floor = 1;
foreach ($articles as $article) {
	list($author, $nick, $content, $pub_time, $attachments, $aid) = $article;
	$html .= '<div class="panel panel-info">';
	$html .= '<div class="panel-heading">';
	$html .= "<a href=\"/author/$author/1\">$author</a>";
	if ($nick != '') {
		$html .= " &nbsp; ($nick)";
	}
	$html .= "<span class=\"pull-right\">$pub_time</span>";
	$html .= '</div>';

	if ($content <> '' || $attachments <> '()') {
		$html .= '<div class="panel-body">';
		$content = str2html($content);
		$html .= $content;
		$attachment_set = execute_dataset("select att_id, file_name from attachment where bid = $bid and aid = $aid");
		foreach ($attachment_set as $attachment) {
			list($att_id, $file_name) = $attachment;
			$img_url = "$smth_static_host/nForum/att/$board_en_name/$aid/$att_id/large";
			$html .= "<br>$file_name<br><img data-original=\"$img_url\" class=\"img-responsive\"><br>";
		}
		$html .= '</div>';
	}
	$html .= '</div>';
	++$floor;
}

$older_topics = execute_dataset("select gid, author, title from topic where bid = $bid and gid < $gid order by gid desc limit 10");
if (count($older_topics) > 0) {
	$html .= '<div class="panel panel-default"><div class="panel-heading">'.i18n('继续阅读').'</div>';
	$html .= '<div class="list-group">';
	foreach ($older_topics as $older_topic) {
		list($older_gid, $older_author, $older_title) = $older_topic;
		$html .= "<a href=\"/topic/$board_en_name/$older_gid\" class=\"list-group-item\">$older_title <span class=\"pull-right\">$older_author</span></a>";
	}
	$html .= '</div></div>';
}
$html .= '</div>';
if (false && !$is_loyal_user) {
	$html .= get_rand_dmm_column_html();
}
//$html .= '</div>';	//end of row


if (false || $is_spider) {
//	$html .= get_rand_zhihu_topic_html();
}
if (true || $is_spider) {
#	$html .= get_old_ck101_topic_html();
}
//$html .= '<p><a href="/">PTT</a> <a href="/disp">disp</a></p></div>';
//$html .= '<script type="text/javascript">var zx_aid = 1;var zx_uid = 10799;var zoneid = 11554;</script><script type="text/javascript" charset="utf-8" src="http://click.9cpc.com/view.js"></script>';
$html .= '</div></div>';

require_once('header.php');
echo $html;
//echo '<'.$_SERVER["HTTP_ACCEPT_LANGUAGE"].' />';
require_once('footer.php');
?>


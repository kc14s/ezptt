<?
require_once("init.php");
$db_conn = conn_douban_db();
require_once("i18n.php");
$is_spider = is_spider();
$is_from_search_engine = is_from_search_engine();
if ($ptt_allow == 0 && !$is_spider && !$is_from_search_engine) {
	header('HTTP/1.1 404 Not Found');
	exit();
}
$uid= urldecode($_GET['uid']);
$page = 1;
if (isset($_GET['page'])) {
	$page = $_GET['page'];
}
list($uname, $nick) = execute_vector("select uname, nick from user where uid = '$uid'");
$uname = i18n($uname);
$nick = i18n($nick);
$result = mysql_query("select bname, title, tid, pub_time from board, topic where uid = '$uid' and topic.bid = board.bid order by pub_time desc limit ".(($page - 1) * $page_size).", $page_size");
$result_num = mysql_num_rows($result);
while (list($bname, $title, $tid, $pub_time) = mysql_fetch_array($result)) {
	$topics[] = array($bname, $title, $tid, $pub_time);
}

$html_title = "$uname ($nick) ".i18n('defawen');
$html = "<div class=\"col-md-8 col-md-offset-2 col-xs-12\"><h3>$html_title</h3>";
//$html .= $google_320_100;
if ($result_num > 0) {
	$html .= '<div class="list-group">';
	foreach ($topics as $topic) {
		list($bname, $title, $tid, $pub_time) = $topic;
		$title = i18n($title);
		$html .="<a class=\"list-group-item\" href=\"/douban_topic/$tid\">[$bname] $title<span class=\"pull-right\">$pub_time</span></a>";
	}
	$html .= '</div>';
	$page_up_disabled = $page == 1 ? 'disabled' : '';
	$page_down_disabled = $result_num == $page_size ? '' : 'disabled';
	$html .= '<ul class="pager"><li class="previous '.$page_up_disabled.'"><a href="/douban_user/'.$uname.'/'.($page - 1).'">&larr; Newer</a></li><li class="next '.$page_down_disabled.'"><a href="/douban_user/'.$uname.'/'.($page + 1).'">Older &rarr;</a></li></ul>';
}
else {
	header('HTTP/1.1 404 Not Found');
	$html .= '<div class="alert alert-danger">'.i18n('meizhaodaozuozhe').'</div>';
}
$html .= '<p><a href="/">PTT</a> <a href="/disp">disp</a></p></div>';
$html .= '</div>';
if (!$is_spider) {
	$html .= $scupio_video_expand;
}
if (!$is_loyal_user) {
//	$html .= $adcash_popunder;
}

require_once('header.php');
echo $html;
require_once('footer.php');
?>


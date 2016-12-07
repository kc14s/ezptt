<?
require_once("init.php");
require_once("i18n.php");
$db_conn = conn_db();
mysql_select_db('btsm', $db_conn);
mysql_query('set names utf8');
$is_spider = is_spider();
$is_from_search_engine = is_from_search_engine();
$author = $_GET['author'];
$page = 1;
$page_size = 30;
if (isset($_GET['page'])) {
	$page = $_GET['page'];
}
list($latest_bid, $latest_gid) = execute_vector("select bid, gid from topic where author = '$author' order by pub_time limit 1");
if (!isset($latest_bid)) {
	header('HTTP/1.1 301 Moved Permanently');
	header('Location: /404');
}
$nick = execute_scalar("select nick from snapshot where snapshot.bid = $latest_bid and snapshot.gid = $latest_gid and snapshot.aid = $latest_gid");
$nick = i18n($nick);
$result = mysql_query("select bid, gid, title, pub_time from topic where author = '$author' order by pub_time desc limit ".(($page - 1) * $page_size).", $page_size");
$result_num = mysql_num_rows($result);
while (list($bid, $gid, $title, $pub_time) = mysql_fetch_array($result)) {
	$topics[] = array($bid, $gid, $title, $pub_time);
}

$html_title = "$author ($nick) ".i18n('defawen');
$html = "<div class=\"col-md-8 col-md-offset-2 col-xs-12\"><h3>$html_title</h3>";
if ($result_num > 0) {
	$html .= '<div class="list-group">';
	foreach ($topics as $topic) {
		list($bid, $gid, $title, $pub_time) = $topic;
		$title = i18n($title);
		list($en_name, $cn_name) = execute_vector("select en_name, cn_name from board where bid = $bid");
		$html .="<a class=\"list-group-item\" href=\"/topic/$en_name/$gid\">[$cn_name] $title<span class=\"pull-right\">$author $pub_time</span></a>";
	}
	$html .= '</div>';
	if (!$is_spider) {
		$page_up_disabled = $page == 1 ? 'disabled' : '';
		$page_down_disabled = $result_num == $page_size ? '' : 'disabled';
		$html .= '<ul class="pager"><li class="previous '.$page_up_disabled.'"><a href="/author/'.$author.'/'.($page - 1).'">&larr; Newer</a></li><li class="next '.$page_down_disabled.'"><a href="/author/'.$author.'/'.($page + 1).'">Older &rarr;</a></li></ul>';
	}
}
else {
	header('HTTP/1.1 404 Not Found');
	$html .= '<div class="alert alert-danger">'.i18n('meizhaodaozuozhe').'</div>';
}
//$html .= '<p><a href="/">PTT</a> <a href="/disp">disp</a></p></div>';
$html .= '</div>';
if (!$is_spider) {
}
if (!$is_loyal_user) {
//	$html .= $adcash_popunder;
}

require_once('header.php');
echo $html;
require_once('footer.php');
?>


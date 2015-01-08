<?
require_once("init.php");
$db_conn = conn_ezptt_db();
require_once("i18n.php");
$is_from_search_engine = is_from_search_engine();
if ($ptt_allow == 0 && !$is_spider && !$is_from_search_engine) {
	header('HTTP/1.1 404 Not Found');
	exit();
}
$en_name = $_GET['en_name'];
$page = (int)$_GET['page'];
list($bid, $cn_name) = execute_vector("select id, cn_name from board where en_name = '$en_name'");
if (!isset($bid)) {
	header('HTTP/1.1 404 Not Found');
	exit();
}
$result = mysql_query("select tid1, tid2, title, author, attachment from topic where bid = $bid order by pub_time desc limit ".(($page - 1) * $page_size).", $page_size");
while (list($tid1, $tid2, $title, $author, $attachment) = mysql_fetch_array($result)) {
	$topic = array($tid1, $tid2, $title, $author);
	if ($attachment) {
		$attachments = execute_column("select concat(md5, '.', ext_name) from attachment where bid = $bid and tid1 = $tid1 and tid2 = '$tid2'");
		foreach ($attachments as $file_name) {
			if (file_exists("att/$file_name")) {
				$topic[] = $file_name;
			}
		}
	}
	$topics[] = $topic;
}

$html = "<h3 align=\"center\">[$en_name] ".i18n($cn_name)."</h3>";
$html .= "<div class=\"col-md-8 col-md-offset-2 col-xs-12\">";
$html .= '<div class="list-group">';
foreach ($topics as $topic) {
	list($tid1, $tid2, $title, $author, $attachment1, $attachment2) = $topic;
	$title = i18n($title);
	$html .="<a class=\"list-group-item\" href=\"/article/$en_name/$tid1/$tid2\">$title<span class=\"pull-right\">$author</span>";
	if (isset($attachment1)) {
		$html .= '<br>';
		$html .= "<img data-original=\"$static_host/att/$attachment1\" height=\"200\" />";
	}
	if (isset($attachment2)) {
		$html .= "<img data-original=\"$static_host/att/$attachment2\" height=\"200\" />";
	}
	$html .= '</a>';
}
$html .= '</div>';

if (!$is_spider) {
	$page_up_disabled = $page == 1 ? 'disabled' : '';
	$html .= '<ul class="pager"><li class="previous '.$page_up_disabled.'"><a href="/board/'.$en_name.'/'.($page - 1).'">&larr; Newer</a></li><li class="next"><a href="/board/'.$en_name.'/'.($page + 1).'">Older &rarr;</a></li></ul>';
}
$html .= '</div>';


$html_title = "$en_name $cn_name";
require_once('header.php');
echo $html;
require_once('footer.php');
?>


<?
require_once("init.php");
$db_conn = conn_db();
require_once("i18n.php");
$is_from_search_engine = is_from_search_engine();
$en_name = $_GET['en_name'];
$page = (int)$_GET['page'];
$page_size = 30;
list($bid, $cn_name) = execute_vector("select bid, cn_name from board where en_name = '$en_name'");
if (!isset($bid)) {
	http_response_code(404);
	header('HTTP/1.1 404 Not Found');
	exit();
}
$result = mysql_query("select gid, title, author from topic where bid = $bid order by gid desc limit ".(($page - 1) * $page_size).", $page_size");
while (list($gid, $title, $author) = mysql_fetch_array($result)) {
	$topic = array($gid, $title, $author);
	$topics[] = $topic;
}

$html = "<h3 align=\"center\">[$en_name] ".i18n($cn_name)."</h3>";
$html .= "<div class=\"col-md-8 col-md-offset-2 col-xs-12\">";
$html .= '<div class="list-group">';
foreach ($topics as $topic) {
	list($gid, $title, $author) = $topic;
	$title = i18n($title);
	$html .="<a class=\"list-group-item\" href=\"/topic/$en_name/$gid\">$title<span class=\"pull-right\">$author</span>";
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


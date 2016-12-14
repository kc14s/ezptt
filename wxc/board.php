<?
require_once("init.php");
$db_conn = conn_db();
mysql_query('use wxc');
require_once("i18n.php");
$is_from_search_engine = is_from_search_engine();
$en_name = $_GET['en_name'];
$page = (int)$_GET['page'];
$page_size = 30;
$cn_name = execute_scalar("select cn_name from board where en_name = '$en_name'");
if (!isset($cn_name)) {
	http_response_code(404);
	header('HTTP/1.1 404 Not Found');
	exit();
}
$cn_name = i18n($cn_name);
$result = mysql_query("select tid, title, author from topic where board_en_name = '$en_name' order by tid desc limit ".(($page - 1) * $page_size).", $page_size");
while (list($tid, $title, $author) = mysql_fetch_array($result)) {
	$topic = array($tid, $title, i18n($author));
	$topics[] = $topic;
}

$html = "<h1 align=\"center\">[<a href=\"/board/$en_name/1\">$en_name</a>] <a href=\"/board/$en_name/1\">$cn_name</a></h1>";
$html .= "<div class=\"col-md-8 col-md-offset-2 col-xs-12\">";
$html .= '<div class="list-group">';
foreach ($topics as $topic) {
	list($tid, $title, $author) = $topic;
	$title = i18n($title);
	$html .="<a class=\"list-group-item\" href=\"/topic/$en_name/$tid\">$title<span class=\"pull-right\">$author</span>";
	$html .= '</a>';
}
$html .= '</div>';

if (!$is_spider) {
	$page_up_disabled = $page == 1 ? 'disabled' : '';
	$html .= '<ul class="pager">';
	if ($page > 1) {
		$html .= '<li class="previous '.$page_up_disabled.'"><a href="/board/'.$en_name.'/'.($page - 1).'">&larr; Newer</a></li>';
	}
	$html .= '<li class="next"><a href="/board/'.$en_name.'/'.($page + 1).'">Older &rarr;</a></li></ul>';
}
$html .= '</div>';


$html_title = "$en_name $cn_name";
require_once('header.php');
echo $html;
require_once('footer.php');
?>


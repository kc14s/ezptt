<?
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
/*
$result = mysql_query("select id, category, en_name, cn_name from board");
while (list($bid, $category, $en_name, $cn_name) = mysql_fetch_array($result)) {
	list($tid, $title) = execute_vector("select tid, title from topic where bid = $bid order by pub_time desc limit 1");
	if (!isset($tid)) continue;
	$articles[] = array($bid, $en_name, $tid, $title);
}
*/
$dataset = get_dataset("data/index");

$html = '<h3 align="center">PTT BBS</h3>';
$html .= "<div class=\"col-md-6 col-md-offset-2 col-xs-12\">";
//$html .= $google_320_100;
$html .= '<div class="list-group">';
foreach ($dataset as $article) {
	list($category, $en_name, $cn_name, $bid, $tid, $title, $author, $att[0], $att[1]) = $article;
	$title = i18n($title);
	$html .= "<a class=\"list-group-item\" href=\"/topic/$bid/$tid\">[$en_name] $title";
	if (isset($att[0])) {
		$html .= "<br><img data-original=\"$static_host/att_ori/$bid.$tid.".$att[0]."\" height=\"200\" />";
	}
	if (isset($att[1])) {
		$html .= "<img data-original=\"$static_host/att_ori/$bid.$tid.".$att[1]."\" height=\"200\" />";
	}
	$html .= '</a>';
}
$html .= '</div></div>';

$html .= '<div class="col-md-2 hidden-xs">';

$dataset = get_dataset("data/beauty");
$beauty_indexes = array();
while (count($beauty_indexes) < 10) {
		$index = rand(0, count($dataset));
		if (isset($beauty_indexes[$index])) continue;
		$beauty_indexes[$index] = 0;
		list($en_name, $tid1, $tid2, $title, $file_name) = $dataset[$index];
		$html .= '<div class="row">';
		$html .= '<div class="thumbnail"><a href="'."/article/$en_name/$tid1/$tid2".'"><img src="'.$static_host.'/att/'.$file_name.'" width="300" /></a><div class="caption"><p><a href="'."/article/$en_name/$tid1/$tid2".'">'.i18n($title).'</a></p></div></div></div>';
}
$html .= '</div>';

require_once('header.php');
echo $html;
require_once('footer.php');
?>


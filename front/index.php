<?
require_once("init.php");
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
$db_conn = conn_db();
$result = mysql_query("select id, category, en_name, cn_name from board");
while (list($bid, $category, $en_name, $cn_name) = mysql_fetch_array($result)) {
	list($tid, $title) = execute_vector("select tid, title from topic where bid = $bid order by pub_time desc limit 1");
	if (!isset($tid)) continue;
	$articles[] = array($bid, $en_name, $tid, $title);
}
*/
$json = json_decode(file_get_contents('data/ptt_index'));
$categories = array('活動中心', '生活娛樂館', '戰略高手', '臺灣大學', '國家研究院', '卡漫夢工廠', '視聽劇場', '國家體育場', '青蘋果樹', '政治大學');
//print_r($json);

$html = '<h3 align="center">PTT BBS</h3>';
$html .= "<div class=\"col-md-6 col-md-offset-2 col-xs-12\">";
$html .= $google_320_100;
foreach ($categories as $category) {
	$topics = $json->$category;
	if (!isset($topics) || count($topics) == 0) continue;
	$html .= '<div class="panel panel-default">';
	$html .= '<div class="panel-heading">'.i18n($category).'</div>';
//	$html .= '<div class="panel-body">';
	$html .= '<div class="list-group">';
	foreach ($topics as $topic) {
		list($en_name, $bid, $tid1, $tid2, $title, $author, $attachments) = $topic;
		$title = i18n($title);
		$html .="<a class=\"list-group-item\" href=\"/article/$en_name/$tid1/$tid2\">[$en_name] $title<span class=\"pull-right\">$author</span>";
		if (isset($attachments) && strlen($attachments) > 0) {
			$html .= '<br>';
			$file_names = explode("\t", $attachments);
			foreach ($file_names as $attachment) {
				$html .= "<img data-original=\"$static_host/att/$attachment\" height=\"200\" />";
			}
		}
		$html .= '</a>';
	}
//	$html .= '</div></div></div>';
	$html .= '</div></div>';
}
$html .= '</div>';

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


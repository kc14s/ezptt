<?
require_once("init.php");
require_once("i18n.php");
if (!$is_loyal_user) {
}

$en_name = $_GET['en_name'];
$tid = $_GET['tid'];
$db_conn = conn_db();
mysql_select_db('wxc', $db_conn);
mysql_query('set names utf8');
list($title, $author, $content, $pub_time) = execute_vector("select title, author, content, pub_time from topic where board_en_name = '$en_name' and tid = $tid");
if (!isset($title) || !isset($author)) {
	header('HTTP/1.1 301 Moved Permanently');
	header('Location: /404');
}
$lz = $author;
$lz_original = $lz;
$cn_name = i18n(execute_scalar("select cn_name from board where en_name = '$en_name'"));
$title = i18n($title);
$html_title = "$title $author";
$content = close_div($content);
$articles[] = array($author, $content, $pub_time);
$topic_pub_time = $pub_time;
$result = mysql_query("select author, title, pub_time from reply where board_en_name = '$en_name' and tid = $tid order by pub_time");
while(list($author, $reply_title, $pub_time) = mysql_fetch_array($result)) {
	$author = i18n($author);
	$articles[] = array($author, $reply_title, $pub_time);
}
if (count($articles) == 1 && $is_spider) {
	$articles[] = $articles[0];
}
$prev_topics = execute_dataset("select board_en_name, tid, author, title, pub_time from topic where board_en_name = '$en_name' and tid < $tid order by tid desc limit 10");
$author_topics = execute_dataset("select board_en_name, tid, author, title, pub_time from topic where author = '$lz_original' order by pub_time desc limit 10");

list($id_min, $id_max) = execute_vector("select min(id), max(id) from topic");
$id_rand = rand($id_min, $id_max);
$rand_topics = execute_dataset("select board_en_name, tid, author, title, pub_time from topic where id < $id_rand order by id desc limit 10");

$html = "<div class=\"row\"><div class=\"col-md-8 col-md-offset-2 col-xs-12\"><ol class=\"breadcrumb\"><li><a href=\"/\">".i18n('wxc')."</a></li><li><a href=\"/board/$en_name/1\">[$en_name] $cn_name</a></li>";
$html .= "</ol><div class=\"page-header\"><h1>$title <small>$lz<span class=\"pull-right\">$topic_pub_time</span></small></h1></div>";

if (false && !$is_loyal_user) {
//	$html .= $google_320_100;
//	$html .= $chitika_468_60;
//	$html .= $bloggerads_banner;
	$html .= $propellerads;
	$html .= $scupio_video_expand;
	$html .= $scupio_728_90;
	$html .= $adcash_popunder;
//	$html .= $gg91_popup;
//	$html .= $gg91_richmedia;
	$html .= $ads360_320_270_float_left;
	$html .= $ads360_320_270_float_right;
	$html .= $ads360_doublet;
	$html .= $ads360_320_270;
	$html .= $ads360_popup;
	$html .= $ads360_float;
	$html .= $wz02_popup;
	$html .= $wz02_richmedia;
//	$html .= $v3_popup_right_bottom_320_270;
	$html .= $zy825_popup;
//	$html .= $iiad_phone_cpm_640_200;
//	$html .= $iiad_popup;
	$html .= $popads_duanzhihu;
//	$html .= $adsterra_duanzhihu_popunder;
//	$html .= $eroadvertising_dzh_popup;	//	low rev
	$html .= $juicyads_duanzhihu_popunder;
}
//$html .= $propellerads_dzh_interstitial;
$floor = 1;
foreach ($articles as $article) {
	list($author, $content, $pub_time) = $article;
	$content = i18n($content);
	$html .= '<div class="panel panel-info">';
	if (true || $floor > 1) {
		$html .= '<div class="panel-heading">';
		$html .= "<a href=\"/author/".urlencode($author)."/1\">$author</a>";
		$html .= "<span class=\"pull-right\">$pub_time</span>";
		$html .= '</div>';
	}
	$html .= '<div class="panel-body">';
	$html .= $content;
	$html .= '</div>';
	$html .= '</div>';
	++$floor;
}

if (isset($prev_topics)) {
	$html .= output_topics($prev_topics, 'jixuyuedu');
}
if (isset($author_topics)) {
	$html .= output_topics($author_topics, 'tongzuozhe');
}
if (false || $is_spider) {
	if (isset($rand_topics)) {
		$html .= output_topics($rand_topics, 'jixuyuedu');
	}
}

function output_topics($topics, $label) {
	$html = '<div class="panel panel-default"><div class="panel-heading">'.i18n($label).'</div>';
	$html .= '<div class="list-group">';
	foreach ($topics as $topic) {
		list($board_en_name, $tid, $author, $title, $pub_time) = $topic;
		$author = i18n($author);
		$html .= "<a href=\"/topic/$board_en_name/$tid\" class=\"list-group-item\">".i18n($title)."<span class=\"pull-right\">$author &nbsp; $pub_time</span></a>";
	}
	$html .= '</div></div>';
	return $html;
}
#	$html .= get_old_ck101_topic_html();
//$html .= '<p><a href="/">PTT</a> <a href="/disp">disp</a></p></div>';
//$html .= '<script type="text/javascript">var zx_aid = 1;var zx_uid = 10799;var zoneid = 11554;</script><script type="text/javascript" charset="utf-8" src="http://click.9cpc.com/view.js"></script>';
$html .= '</div></div>';

require_once('header.php');
echo $html;
require_once('footer.php');
?>


<?
require_once('init.php');
require_once('i18n.php');
require_once('dmm_lib.php');
$db_conn = conn_dmm_db();

$sort_by = $_GET['sort_by'];
$page = $_GET['page'];
$page_size = 48;

if ($sort_by == 'rank') {
	$order_by = 'rank';
	$group_name_key = 'hottest';
}
else if ($sort_by == 'release_date') {
	$order_by = 'release_date desc';
	$group_name_key = 'latest';
}
else if ($sort_by == 'popularity') {
	$order_by = 'seed_popularity desc';
	$group_name_key = 'popularity';
}
if (isset($_GET['star_id'])) {
	$result = mysql_query("select title, video.sn, sn_normalized, channel from video, star, star_info where star_info.id = ".$_GET['star_id']." and star_info.name = star.star and star.sn = video.sn order by video.seed_popularity desc limit ".(($page - 1) * $page_size).", $page_size");
	$label = execute_scalar('select name from star_info where id = '.$_GET['star_id']).i18n('star_all_video');
}
else if (isset($_GET['release_year'])) {
	$release_year = $_GET['release_year'];
	$current_year = date('Y');
	$result = mysql_query("select title, sn, sn_normalized, channel from video where release_year = $release_year order by seed_popularity desc limit ".(($page - 1) * $page_size).", $page_size");
}
else {
	$result = mysql_query("select title, sn, sn_normalized, channel from video order by $order_by limit ".(($page - 1) * $page_size).", $page_size");
	$label = i18n($group_name_key);
}
while (list($title, $sn, $snn, $channel) = mysql_fetch_array($result)) {
	$snn = snn_add_hyphen($snn);
	$video = array($title, $sn, $snn, $channel);
	$videos[] = $video;
}
$html = '<div class="row"><div class="col-md-10 col-md-offset-1 col-xs-12">';
$html .= '<div class="panel panel-info">';
if (isset($release_year)) {
	$html .= '<div class="panel-heading">';
	$html .= '<h3 align="center">'.$release_year.' '.i18n('best_seller').'</h3>';
	$html .= '</div>';
}
else {
	$html .= '<div class="panel-heading"><h3 align="center">'.$label.'</h3></div>';
}
$html .= '<div class="panel-body">';
$column = 0;
foreach ($videos as $video) {
	list($title, $sn, $snn, $channel) = $video;
	$url = "/video/$sn";
	if ($column % 4 == 0) $html .= '<div class="row"><div class="col-md-12">';
	$html .= "<div class=\"col-xs-6 col-md-3\"><div class=\"thumbnail\"><a href=\"/video/$sn\"><img data-original=\"".get_cover_img_url($sn, $channel)."\"><br>$title $snn</a></div></div>";
	//	$html .= '<div class="media"><a class="pull-left" href="'.$url.'"><img class="media-object" data-original="'.get_cover_img_url($sn, $channel).'" /></a>';
	//	$html .= '<div class="media-body"><h4 class="media-heading"><a href="'.$url.'">'.$title.'</a></h4></div></div>';
	if ($column % 4 == 3) $html .= '</div></div>';
	++$column;
}
if (!$is_spider) {
	$html .= '<div class="row"><div class="col-md-12"><ul class="pager">';
	if (isset($_GET['star_id'])) {
		$page_up_enabled = $page > 1;
		if ($page > 1) {
			$html .= '<li class="previous"><a href="/star/'.$_GET['star_id'].'/'.($page - 1).'" target="_self">&larr; '.i18n('page_up').'</a></li>';
		}
		if ($column == $page_size) {
			$html .= '<li class="next"><a href="/star/'.$_GET['star_id'].'/'.($page + 1).'" target="_self">&rarr; '.i18n('page_down').'</a></li>';
		}
	}
	else if (isset($release_year)) {
		if ($release_year < $current_year) {
			$html .= '<li class="previous"><a href="/best_seller/'.($release_year + 1).'/1" target="_self">'.($release_year + 1).' '.i18n('best_seller').'</a></li>';
		}
		if ($page > 1) {
			$html .= '<li class="previous"><a href="/best_seller/'.$release_year.'/'.($page - 1).'" target="_self">&larr; '.i18n('page_up').'</a></li>';
		}
		$html .= '<li class="next"><a href="/best_seller/'.($release_year - 1).'/1" target="_self">'.($release_year - 1).' '.i18n('best_seller').'</a></li>';
		$html .= '<li class="next"><a href="/best_seller/'.$release_year.'/'.($page + 1).'" target="_self">&rarr; '.i18n('page_down').'</a></li>';
	}
	else {
		if ($page > 1) {
			$html .= '<li class="previous"><a href="/list/'.$sort_by.'/'.($page - 1).'" target="_self">&larr; '.i18n('page_up').'</a></li>';
		}
		$html .= '<li class="next"><a href="/list/'.$sort_by.'/'.($page + 1).'" target="_self">&rarr; '.i18n('page_down').'</a></li>';
	}
	$html .= '</ul></div></div>';
}
$html .= '</div></div></div></div>';

$target = '_blank';
$html_title = 'Japan Porn Database';
require_once('header.php');
echo $html;
require_once('footer.php');

function output_group($videos, $group_name, $sort_by) {
	return $html;
}

?>

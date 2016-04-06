<?
require_once('init.php');
require_once('i18n.php');
require_once('dmm_lib.php');
$db_conn = conn_dmm_db();

$result = mysql_query("select title, sn, sn_normalized, channel from video order by rank limit 20");
while (list($title, $sn, $snn, $channel) = mysql_fetch_array($result)) {
	$snn = snn_add_hyphen($snn);
	$video = array($title, $sn, $snn, $channel);
	$videos[] = $video;
}
$html .= output_group($videos, i18n('hottest'), 'rank');

$star_infos = execute_dataset("select id, name, pic_name from star_info where rank < 10000 order by rank limit 24");
$html .= '<div class="row"><div class="col-md-10 col-md-offset-1 col-xs-12">';
$html .= '<div class="panel panel-info">';
$html .= '<div class="panel-heading">'.i18n('top_stars').' &nbsp; <a href="/stars/1">'.i18n('more').'</a></div>';
$html .= '<div class="panel-body">';
$column = 0;
foreach ($star_infos as $star_info) {
	list($star_id, $star_name, $star_pic_name) = $star_info;
	if ($column % 6 == 0) $html .= '<div class="row"><div class="col-md-12">';
	$html .= "<div class=\"col-xs-6 col-md-2\"><div class=\"thumbnail\"><a href=\"/star/$star_id/1\"><img data-original=\"".get_thumb_url($star_pic_name)."\"><div class=\"caption\"><h4 align=\"center\">$star_name</h3></div></a></div></div>";
	if ($column % 6 == 5) $html .= '</div></div>';
	++$column;
}
$html .= '<div class="row"><div class="col-md-12"><p class="pull-right"><a href="/stars/1">'.i18n('more').'</a></p></div></div>';
$html .= '</div></div></div></div>';

$videos = array();
$result = mysql_query("select title, sn, sn_normalized, channel from video order by seed_popularity desc limit 20");
while (list($title, $sn, $snn, $channel) = mysql_fetch_array($result)) {
	$snn = snn_add_hyphen($snn);
	$video = array($title, $sn, $snn, $channel);
	$videos[] = $video;
}
$html .= output_group($videos, i18n('popularity'), 'popularity');

$target = '_blank';
$html_title = 'Japan Porn Database';
require_once('header.php');
echo $html;
require_once('footer.php');

function output_group($videos, $group_name, $sort_by) {
	$html = '<div class="row"><div class="col-md-10 col-md-offset-1 col-xs-12">';
	$html .= '<div class="panel panel-info">';
	$html .= '<div class="panel-heading">'.$group_name.' &nbsp; <a href="/list/'.$sort_by.'/1">'.i18n('more').'</a></div>';
	$html .= '<div class="panel-body">';
	$column = 0;
	foreach ($videos as $video) {
		list($title, $sn, $snn, $channel) = $video;
		$url = "/video/$sn";
		if ($column % 4 == 0) $html .= '<div class="row"><div class="col-md-12">';
		$html .= "<div class=\"col-xs-6 col-md-3\"><div class=\"thumbnail\"><a href=\"/video/$sn\"><img data-original=\"".get_cover_img_url($sn, $channel)."\"><br>$title $snn</a></div></div>";
		if ($column % 4 == 3) $html .= '</div></div>';
		++$column;
	}
	$html .= '<div class="row"><div class="col-md-12"><p class="pull-right"><a href="/list/'.$sort_by.'/1">'.i18n('more').'</a></p></div></div>';
	$html .= '</div></div></div></div>';
	return $html;
}

?>

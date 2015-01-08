<?
require_once('init.php');
require_once('i18n.php');
require_once('dmm_lib.php');
$db_conn = conn_dmm_db();

$result = mysql_query("select title, sn, channel from video order by rank limit 20");
while (list($title, $sn, $channel) = mysql_fetch_array($result)) {
	$video = array($title, $sn, $channel);
	$videos[] = $video;
}
$html .= output_group($videos, i18n('hottest'), 'rank');

$videos = array();
$result = mysql_query("select title, sn, channel from video order by release_date desc limit 20");
while (list($title, $sn, $channel) = mysql_fetch_array($result)) {
	$video = array($title, $sn, $channel);
	$videos[] = $video;
}
$html .= output_group($videos, i18n('latest'), 'release_date');

$target = '_blank';
$html_title = 'Japan Porn Database';
require_once('header.php');
echo $html;
require_once('footer.php');

function output_group($videos, $group_name, $sort_by) {
	$html = '<div class="row"><div class="col-md-8 col-md-offset-2 col-xs-12">';
	$html .= '<div class="panel panel-info">';
	$html .= '<div class="panel-heading">'.$group_name.' &nbsp; <a href="/list/'.$sort_by.'/1">'.i18n('more').'</a></div>';
	$html .= '<div class="panel-body">';
	$column = 0;
	foreach ($videos as $video) {
		list($title, $sn, $channel) = $video;
		$url = "/video/$sn";
		if ($column % 4 == 0) $html .= '<div class="row"><div class="col-md-12">';
		$html .= "<div class=\"col-xs-6 col-md-3\"><div class=\"thumbnail\"><a href=\"/video/$sn\"><img data-original=\"".get_cover_img_url($sn, $channel)."\"><br>$title</a></div></div>";
		//	$html .= '<div class="media"><a class="pull-left" href="'.$url.'"><img class="media-object" data-original="'.get_cover_img_url($sn, $channel).'" /></a>';
		//	$html .= '<div class="media-body"><h4 class="media-heading"><a href="'.$url.'">'.$title.'</a></h4></div></div>';
		if ($column % 4 == 3) $html .= '</div></div>';
		++$column;
	}
	$html .= '<div class="row"><div class="col-md-12"><p class="pull-right"><a href="/list/'.$sort_by.'/1">'.i18n('more').'</a></p></div></div>';
	$html .= '</div></div></div></div>';
	return $html;
}

?>

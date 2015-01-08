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
$result = mysql_query("select title, sn, channel from video order by $order_by limit ".(($page - 1) * $page_size).", $page_size");
while (list($title, $sn, $channel) = mysql_fetch_array($result)) {
	$video = array($title, $sn, $channel);
	$videos[] = $video;
}
$html = '<div class="row"><div class="col-md-8 col-md-offset-2 col-xs-10">';
$html .= '<div class="panel panel-info">';
$html .= '<div class="panel-heading">'.i18n($group_name_key).'</div>';
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
if (!$is_spider) {
	$html .= '<div class="row"><div class="col-md-12"><ul class="pager">';
	$html .= '<li class="previous'.($page == 1 ? ' disabled' : '').'"><a href="/list/'.$sort_by.'/'.($page - 1).'" target="_self">&larr; '.i18n('page_up').'</a></li>';
	$html .= '<li class="next"><a href="/list/'.$sort_by.'/'.($page + 1).'" target="_self">&rarr; '.i18n('page_down').'</a></li>';
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

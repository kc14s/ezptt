<?
require_once('init.php');
require_once('i18n.php');
require_once('dmm_lib.php');
$db_conn = conn_dmm_db();

$page = $_GET['page'];
$page_size = 12;

$html = '<div class="h1 text-center">'.i18n('series').'</div>';
for ($series_rank = $page_size * ($page - 1) + 1; $series_rank <= $page_size * $page; ++$series_rank) {
	list($series_id, $series_name) = execute_vector("select id, name from series where rank = $series_rank");
	$series_set = execute_dataset("select sn, sn_normalized, title, channel from video where series_id = $series_id order by release_date desc limit 4");
	$html .= '<div class="row"><div class="col-md-10 col-md-offset-1 col-xs-12">';
	$html .= '<div class="panel panel-info">';
	$html .= '<div class="panel-heading"><a href="/series/'.$series_id.'/1">'.$series_name.'</a> &nbsp; <a href="/series/'.$series_id.'/1">'.i18n('more').'</a></div>';
	$html .= '<div class="panel-body">';
	foreach ($series_set as $series_video) {
		list($series_sn, $series_snn, $series_title, $series_channel) = $series_video;
		$html .= "<div class=\"col-xs-6 col-md-3\"><div class=\"thumbnail\"><a href=\"/video/$series_sn\"><img data-original=\"".get_cover_img_url($series_sn, $series_channel)."\"><br>$series_title $series_snn</a></div></div>";
	}
	$html .= '</div></div></div></div>';
}

$html .= '<div class="row"><div class="col-md-10 col-md-offset-1 col-xs-12"><ul class="pager">';
if ($page > 1) {
	$html .= '<li class="previous"><a href="/series_list/'.($page - 1).'" target="_self">&larr; '.i18n('page_up').'</a></li>';
}
if ($page <= execute_scalar("select count(*) from series") / $page_size) {
	$html .= '<li class="next"><a href="/series_list/'.($page + 1).'"target="_self">&rarr; '.i18n('page_down').'</a></li>';
}
$html .= '</ul></div></div>';
$target = '_blank';
$html_title = 'Japan Porn Database';
require_once('header.php');
echo $html;
require_once('footer.php');

?>

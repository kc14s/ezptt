<?
require_once('init.php');
require_once('i18n.php');
require_once('dmm_lib.php');
$db_conn = conn_dmm_db();

$page = $_GET['page'];
$page_size = 48;

$star_infos = execute_dataset("select id, name, pic_name from star_info where rank < 10000 order by rank limit ".(($page - 1) * $page_size).", $page_size");
$html = '<div class="row"><div class="col-md-10 col-md-offset-1 col-xs-12">';
$html .= '<div class="panel panel-info">';
$html .= '<div class="panel-heading">'.i18n('top_stars').'</div>';
$html .= '<div class="panel-body">';
$column = 0;
foreach ($star_infos as $star_info) {
	list($star_id, $star_name, $star_pic_name) = $star_info;
	if ($column % 6 == 0) $html .= '<div class="row"><div class="col-md-12">';
	$html .= "<div class=\"col-xs-6 col-md-2\"><div class=\"thumbnail\"><a href=\"/star/$star_id/1\"><img data-original=\"".get_thumb_url($star_pic_name)."\"><div class=\"caption\"><h4 align=\"center\">$star_name</h3></div></a></div></div>";
	if ($column % 6 == 5) $html .= '</div></div>';
	++$column;
}
if (!$is_spider) {
	$html .= '<div class="row"><div class="col-md-12"><ul class="pager">';
	$html .= '<li class="previous'.($page == 1 ? ' disabled' : '').'"><a href="/stars/'.($page - 1).'" target="_self">&larr; '.i18n('page_up').'</a></li>';
	$html .= '<li class="next'.($page == 3 ? ' disabled' : '').'"><a href="/stars/'.($page + 1).'" target="_self">&rarr; '.i18n('page_down').'</a></li>';
	$html .= '</ul></div></div>';
}
$html .= '</div></div></div></div>';

$target = '_blank';
$html_title = 'Japan Porn Database';
require_once('header.php');
echo $html;
require_once('footer.php');

?>

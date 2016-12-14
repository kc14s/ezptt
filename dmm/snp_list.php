<?
require_once('init.php');
require_once('i18n.php');
require_once('dmm_lib.php');
$db_conn = conn_dmm_db();

$page = $_GET['page'];
$page_size = 48 * 3;

$snps = execute_column("select snp from snp order by seed_popularity desc limit ".(($page - 1) * $page_size).", $page_size");
$html = '<div class="row"><div class="col-md-10 col-md-offset-1 col-xs-12">';
$html .= '<div class="panel panel-info">';
$html .= '<div class="panel-heading">'.i18n('sn').i18n('series').'</div>';
$html .= '<div class="panel-body">';
$column = 0;
foreach ($snps as $snp) {
	if ($column % 6 == 0) $html .= '<div class="row"><div class="col-md-12">';
	$html .= "<div class=\"col-xs-6 col-md-2\"><a href=\"/snp/$snp/1\">".strtoupper($snp)."</a></div>";
	if ($column % 6 == 5) $html .= '</div></div>';
	++$column;
}
if (!$is_spider) {
	$html .= '<div class="row"><div class="col-md-12"><ul class="pager">';
	if ($page > 1) $html .= '<li class="previous'.($page == 1 ? ' disabled' : '').'"><a href="/snp_list/'.($page - 1).'" target="_self">&larr; '.i18n('page_up').'</a></li>';
	$html .= '<li class="next'.($page == 100 ? ' disabled' : '').'"><a href="/snp_list/'.($page + 1).'" target="_self">&rarr; '.i18n('page_down').'</a></li>';
	$html .= '</ul></div></div>';
}
$html .= '</div></div>';
$html .= duoshuo_html('jporndb', 'stars', 'stars', "snp_list/1");
$html .= '</div></div>';

$target = '_blank';
$html_title = 'JAV321';
require_once('header.php');
echo $html;
require_once('footer.php');

?>

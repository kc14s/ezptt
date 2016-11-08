<?
require_once('init.php');
require_once('i18n.php');
require_once('dmm_lib.php');
$db_conn = conn_dmm_db();

$page = $_GET['page'];
$page_size = 12;

$categories = array('censored', 'amateur', 'uncensored');

$html = '<div class="h1 text-center">'.i18n('company').'</div>';
for ($source = 0; $source < 3; ++$source) {
	$companies = execute_dataset("select name, logo from company where source = $source");
	if ($source == 2) {
		$companies[] = array('Tokyo Hot', '');
		$companies[] = array('一本道', '');
	}
	$html .= '<div class="row"><div class="col-md-10 col-md-offset-1 col-xs-12">';
	$html .= '<div class="panel panel-info">';
	$html .= '<div class="panel-heading">'.i18n($categories[$source]).'</div>';
	$html .= '<div class="panel-body">';
	for ($col = 0; $col < count($companies); ++$col) {
		list($name, $logo) = $companies[$col];
		if ($col % 3 == 0) {
			$html .= '<div class="row">';
		}
		$html .= '<div class="col-md-4"><a href="/company/'.urlencode($name).'/1"><div class="thumbnail">'.get_company_icon_html($source, $logo).'<p class="text-center">'.i18n($name).'</p></div></a></div>';
		if ($col % 3 == 2 || $col == count($companies) - 1) {
			$html .= '</div>';
		}
	}
	$html .= '</div></div></div></div>';
}

$target = '_blank';
$html_title = 'Japan Porn Database';
require_once('header.php');
echo $html;
require_once('footer.php');

?>

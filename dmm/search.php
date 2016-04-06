<?
require_once('init.php');
require_once('i18n.php');
require_once('dmm_lib.php');
$db_conn = conn_dmm_db();

$input = strtolower($_POST['sn']);
preg_match_all('/([a-z0-9]+)/', $input, $matches, PREG_SET_ORDER);
$sn_normalized = '';
foreach ($matches as $match) {
	$sn_normalized .= $match[1];
}

$sn = execute_scalar("select sn from video where sn_normalized = '$sn_normalized'");
if (isset($sn)) {
	header('Location: /video/'.$sn, TRUE, 301);
	exit;
}
$html = '<div class="row"><div class="col-md-10 col-md-offset-1 col-xs-10">';
$html .= '<div class="alert alert-danger" role="alert">'.i18n('video_not_found').'</div>';
$html .= '</div></div>';

$html_title = 'Japan Porn Database';
require_once('header.php');
echo $html;
require_once('footer.php');
?>

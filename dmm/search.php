<?
require_once('init.php');
require_once('i18n.php');
require_once('dmm_lib.php');
$db_conn = conn_dmm_db();

$input = strtolower($_POST['sn']);
preg_match_all('/([A-Za-z0-9]+)/', $input, $matches, PREG_SET_ORDER);
preg_match_all('/([A-Za-z]+|[0-9]+)/', $input, $matches, PREG_SET_ORDER);
$sn_normalized = '';
$match_count = 0;
foreach ($matches as $match) {
	if (is_numeric($match[1])) {
		if (strpos($input, '1pondo') === 0) {
			if (strlen($match[1]) == 3) {
				$sn_normalized .= '_'.$match[1];
			}
			else {
				$sn_normalized .= $match[1];
			}
		}
		else {
			$sn_normalized .= sprintf("%03s", $match[1]);
		}
	}
	else {
		$sn_normalized .= $match[1];
	}
	++$match_count;
}

if ($match_count == 1) {
	if (execute_scalar("select count(*) from snp where snp = '$sn_normalized'") == 1) {
		header('Location: /snp/'.$sn_normalized.'/1', TRUE, 301);
		exit;
	}
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

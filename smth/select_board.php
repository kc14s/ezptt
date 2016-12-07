<?
require_once("init.php");
$en_name = $_POST['en_name'];
$found = false;
if (preg_match("/^[\w\-]+$/", $en_name)) {
	$db_conn = conn_db();
	if (execute_scalar("select count(*) from board where en_name = '$en_name'") == 1) {
		$found = true;
	}
}

if ($found) {
	http_response_code(301);
	header("Location: /board/$en_name/1", TRUE, 301);
	exit();
}
else {
	http_response_code(404);
	require_once('i18n.php');
	$html = '<div class="alert alert-danger">'.i18n('meizhaodaokanban').'</div>';
	require_once('header.php');
	echo $html;
	require_once('footer.php');
}
?>

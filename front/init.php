<?
foreach (array_values($_REQUEST) as $v) {
	if (strpos($v, ' ') !== false || strpos($v, '/') !== false || strpos($v, '*') !== false || strpos($v, "\t") !== false) {
		header('HTTP/1.0 403 Forbidden');
		exit;
	}
}
require_once("functions.php");
require_once("data.php");
$is_spider = is_spider();
$is_google_spider = is_google_spider();
$is_loyal_user = is_loyal_user();
if (!$is_loyal_user) {
	error_log('not loyal');
}
?>

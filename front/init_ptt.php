<?
if ($is_spider && !$is_google_spider && !isset($_SERVER['HTTPS'])) {
	header('Location: https://www.ucptt.com'.$_SERVER['REQUEST_URI'], TRUE, 301);
}

if ($is_google_spider && (isset($_SERVER['HTTPS']) || $_SERVER['HTTP_HOST'] != 'www.ucptt.com')) {
	header('Location: http://www.ucptt.com'.$_SERVER['REQUEST_URI'], TRUE, 301);
}
?>

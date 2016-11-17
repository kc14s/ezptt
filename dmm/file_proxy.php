<?
$root = '/tmp/www/';
$path = $_GET['path'];
//print_r($_GET);

if (strpos($path, '..') === false) {}
else {exit;}

$local_path = "$root$path";

$suffix = substr($path, strrpos($path, '.') + 1);
if ($suffix == 'jpg') $suffix = 'jpeg';
header("Content-Type: image/$suffix");
//header('Cache-Control: max-age=31536000');
//header('Cache-Control: no-cache, must-revalidate');
//header('Expires: Thu, 31 Dec 2099 23:59:59 GMT');
//header('Pragma: max-age=31536000');

if (file_exists($local_path)) {
	$file_size = filesize($local_path);
	if ($file_size > 100) {
		header("Content-Length: $filesize");
		echo file_get_contents($local_path);
		exit;
	}
}
if (start_with($path, 'new') || start_with($path, 'archive')) {
	$remote = "http://imgs.aventertainments.com/$path";
}
else if (start_with($path, 'digital') || start_with($path, 'mono')) {
	$remote = "http://pics.dmm.co.jp/$path";
}
else if (start_with($path, 'images')) {
	$remote = "http://spimg2.mgstage.com/$path";
}
else if (start_with($path, 'media')) {
	$remote = "http://my.cdn.tokyo-hot.com/$path";
}
else if (start_with($path, 'assets')) {
	$remote = "http://www.1pondo.tv/$path";
}
else if (start_with($path, 'sample')) {
	$remote = "http://smovie.1pondo.tv/$path";
}
else if (start_with($path, 'img/pc')) {
	$remote = "http://www.mgstage.com/$path";
}
else if (start_with($path, 'img/studio_ic')) {
	$remote = "http://www.aventertainments.com/$path";
}
else if (start_with($path, 'p/maker_logo')) {
	$remote = "http://p.dmm.co.jp/$path";
}
else {
	header('HTTP/1.1 404 Not Found');
	exit;
}

$file = file_get_contents($remote);
error_log("get url $remote from ".$_SERVER['HTTP_REFERER'].' by '.$_SERVER['HTTP_USER_AGENT']);
/*

$dir = substr($local_path, 0, strrpos($local_path, '/'));
if (is_dir($dir)) {}
else {
	mkdir($dir, 0777, true);
}
file_put_contents($local_path, $file);
*/
echo $file;

function start_with($s1, $s2) {
	return strpos($s1, $s2) === 0;
}
?>

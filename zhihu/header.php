<?php header("Content-type: text/html; charset=UTF-8");
?>
<!DOCTYPE HTML>
<html lang="en">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
<title>
<?php
if (isset($html_title)) {
	echo $html_title;
}
echo ' 短知乎';
?>
</title>
<link rel="stylesheet" href="http://cdn.bootcss.com/twitter-bootstrap/3.0.3/css/bootstrap.min.css">
<script src="http://cdn.bootcss.com/jquery/1.10.2/jquery.min.js"></script>
<script src="<?echo $static_host;?>/js/jquery.lazyload.min.js"></script>
<?
if (isset($target)) {
	echo "<base target=\"$target\" />";
}
$width = 6;
if (strpos($_SERVER['SCRIPT_NAME'], 'answer.php') > 0 || strpos($_SERVER['SCRIPT_NAME'], 'board.php') > 0) $width = 8;
$offset = (12 - $width) / 2;
$short_active = '';
$hot_active = '';
if (strpos($_SERVER['SCRIPT_NAME'], 'index.php') > 0) {
	if (isset($_GET['hot'])) {
		$hot_active = 'class="active"';
	}
	else if (isset($_GET['reply'])) {
		$reply_active = 'class="active"';
	}
	else {
		$short_active = 'class="active"';
	}
}
?>
<script>
var _hmt = _hmt || [];
(function() {
 var hm = document.createElement("script");
 hm.src = "//hm.baidu.com/hm.js?fe94799350cc1355f1b147e6ff91dcbe";
 var s = document.getElementsByTagName("script")[0]; 
 s.parentNode.insertBefore(hm, s);
 })();
</script>
</head>
<body>
<div class="row"><div class="col-sm-<?echo $width;?> col-sm-offset-<?echo $offset;?> col-xs-12">
<nav class="navbar navbar-default" role="navigation">
<div class="container-fluid">
<!-- Brand and toggle get grouped for better mobile display -->
<div class="navbar-header">
<a class="navbar-brand" href="/" target="_self">短知乎</a>
<a class="navbar-brand" href="/hot/" target="_self">热知乎</a>
<a class="navbar-brand" href="/comment/" target="_self">神回复</a>
</div>
</div><!-- /.container-fluid -->
</nav>
</div></div>
<div class="row">

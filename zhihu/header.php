<?php header("Content-type: text/html; charset=UTF-8");
require_once('i18n.php');
//require_once('init_zhihu.php');
?>
<!DOCTYPE HTML>
<html lang="<?echo get_html_lang();?>">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
<meta name="applicable-device" content="pc,mobile" />
<meta name="baidu-site-verification" content="EviUseUxzL" />
<meta name="propeller" content="44a0e10d0b8967e6ecc809d269aa6c7f" />
<meta name="trafficjunky-site-verification" content="5t77475t0" />
<meta name="juicyads-site-verification" content="a5bd88ca6d0ec397805dbc0cc472896d">
<meta name="ero_verify" content="a0217e0cdd91adac836158a2226d4cf6" />
<link rel="icon" type="image/png" href="data:;base64,iVBORw0KGgo=">
<link rel="shortcut icon" href="data:image/x-icon;," type="image/x-icon">
<title>
<?php
if (isset($html_title)) {
	echo $html_title;
}
echo ' '.i18n('duanzhihu');
?>
</title>
<link rel="stylesheet" href="//cdn.bootcss.com/twitter-bootstrap/3.3.4/css/bootstrap.min.css">
<link rel="stylesheet" href="//static.zhihu.com/static/revved/-/css/z.d4f4b4f3.css">
<script src="//cdn.bootcss.com/jquery/1.10.2/jquery.min.js"></script>
<script src="//cdn.bootcss.com/jquery.lazyload/1.9.1/jquery.lazyload.min.js"></script>
<?
if (isset($target)) {
	echo "<base target=\"$target\" />";
}
if (!$is_loyal_user) {
	echo $popads_duanzhihu;
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
<style>
body { overflow-x: hidden;}
</style>
</head>
<body>
<script>
(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
 (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
 m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
 })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
ga('create', 'UA-17088225-7', 'auto');
ga('send', 'pageview');
</script>
<div class="container">
<div class="row"><div class="col-sm-<?echo $width;?> col-sm-offset-<?echo $offset;?> col-xs-12">
<nav class="navbar navbar-default" role="navigation">
<div class="container-fluid">
<!-- Brand and toggle get grouped for better mobile display -->
<div class="navbar-header">
<a class="navbar-brand" href="/" target="_self"><?echo i18n('duanzhihu')?></a>
<a class="navbar-brand" href="/hot/" target="_self"><?echo i18n('rezhihu')?></a>
<a class="navbar-brand" href="/comment/" target="_self"><?echo i18n('shenhuifu')?></a>
<a class="navbar-brand" href="/pic/" target="_self"><?echo i18n('youzhenxiang')?></a>
</div>
</div><!-- /.container-fluid -->
</nav>
</div></div>

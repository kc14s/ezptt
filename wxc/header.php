<?php header("Content-type: text/html; charset=UTF-8");
require_once('i18n.php');
?>
<!DOCTYPE HTML>
<html lang="<?echo get_html_lang()?>">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
<meta name="applicable-device" content="pc,mobile" />
<link rel="icon" href="data:;base64,iVBORw0KGgo=">
<title>
<?php
if (isset($html_title)) {
	echo $html_title;
}
//else {
//	echo i18n('水木社区');
//}
$wxc = i18n('文学城');
echo ' '.$wxc;
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
$width = 8;
$offset = (12 - $width) / 2;
echo get_hreflang();
?>
<script>
var _hmt = _hmt || [];
(function() {
 var hm = document.createElement("script");
 hm.src = "https://hm.baidu.com/hm.js?53b3915a37653857f6ff7e94832758f2";
 var s = document.getElementsByTagName("script")[0]; 
 s.parentNode.insertBefore(hm, s);
 })();
</script>

<style>
body { overflow-x: hidden;}
</style>
<script>
(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
 (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
 m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
 })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
ga('create', 'UA-17088225-9', 'auto');
ga('send', 'pageview');
</script>
</head>
<body>
<div class="container">
	<div class="row"><div class="col-sm-<?echo $width;?> col-sm-offset-<?echo $offset;?> col-xs-12">
	<nav class="navbar navbar-default" role="navigation">
			<ul class="nav navbar-nav hidden-xs"><li><a href="/"><? echo $wxc ?></a></li></ul>
			<form class="navbar-form navbar-left" role="search" action="/select_board" method="POST">
				<div class="form-group input-group">
					<input type="text" id="select_board" name="en_name" class="form-control" placeholder="<? echo i18n('xuanzebanmian') ?>">
					<span class="input-group-btn"><button type="submit" class="btn btn-default">Submit</button></span>
				</div>
			</form>
			<form class="navbar-form navbar-left hidden-xs" role="search" action="/query_author" method="POST">
				<div class="form-group input-group">
					<input type="text" name="author" class="form-control" placeholder="<? echo i18n('chaxunzuozhe') ?>">
				<span class="input-group-btn"><button type="submit" class="btn btn-default">Submit</button></span>
				</div>
			</form>
	</nav>
	</div></div>

<?php header("Content-type: text/html; charset=UTF-8");
require_once('i18n.php');
?>
<!DOCTYPE HTML>
<html lang="<?echo get_lang_short();?>">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<meta name="applicable-device" content="pc,mobile">
<link rel="icon" type="image/png" href="data:;base64,iVBORw0KGgo=">
<link rel="icon" href="data:;base64,=">
<link rel="shortcut icon" href="data:image/x-icon;," type="image/x-icon">
<meta name="trafficjunky-site-verification" content="5t6vrynq8" />
<meta name="propeller" content="8c982135933fddb9bda37f7341f8d456" />
<meta name="ero_verify" content="2872ecd468361b6ebb461a9801270b07" />
<title><?php
if (isset($html_title)) {
	echo $html_title;
}
?> dmm</title>
<link rel="stylesheet" href="//cdn.bootcss.com/twitter-bootstrap/3.3.4/css/bootstrap.min.css">
<script src="//cdn.bootcss.com/jquery/1.10.2/jquery.min.js"></script>
<script src="//cdn.bootcss.com/twitter-bootstrap/3.0.3/js/bootstrap.min.js"></script>
<script src="//cdn.bootcss.com/jquery.lazyload/1.9.1/jquery.lazyload.min.js"></script>
<meta name="baidu-site-verification" content="c3FkX097v5" />
<meta name="juicyads-site-verification" content="261f9ab1f219661ed9038571027b3f7c">
<?
if (isset($target)) {
	echo "<base target=\"$target\" />";
}
?>
<script>var _hmt = _hmt || [];(function() {  var hm = document.createElement("script");  hm.src = "//hm.baidu.com/hm.js?0b4a6c1a6eedf10ee1f1702eced53914";  var s = document.getElementsByTagName("script")[0];   s.parentNode.insertBefore(hm, s);})();</script>
</head>
<body>
<script>
(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
 (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
 m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
 })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
ga('create', 'UA-17088225-8', 'auto');
ga('send', 'pageview');
</script>
<div class="row"><div class="col-md-10 col-md-offset-1 col-xs-12">
<nav class="navbar navbar-default" role="navigation">
	<div class="container-fluid">
		<div class="navbar-header hidden-xs">
			<a class="navbar-brand" href="/">JAV321</a>
		</div>
		<ul class="nav navbar-nav hidden-xs">
			<li><a href="/play_list/1"><?echo i18n('play_list');?></a></li>
			<li><a href="/best_seller/2016/1"><?echo i18n('best_seller');?></a></li>
			<li><a href="/series_title_list/1"><?echo i18n('series');?></a></li>
			<li><a href="/genre_list"><?echo i18n('genre');?></a></li>
		</ul>
		<form class="navbar-form navbar-left" role="search" action="/search" method="POST">
			<div class="form-group input-group">
				<input type="text" name="sn" class="form-control" placeholder="<?echo i18n('sn');?>" size="10">
				<span class="input-group-btn"><button type="submit" class="btn btn-default">Search</button></span>
			</div>
		</form>
		<form class="navbar-form navbar-left hidden-xs" role="search" action="/emule" method="POST">
			<div class="form-group input-group">
				<input type="text" name="kw" class="form-control" placeholder="" size="10">
			<span class="input-group-btn"><button type="submit" class="btn btn-default"><?echo i18n('seed_download_emule')?></button></span>
			</div>
		</form>
		<ul class="nav navbar-nav"><li><a href="/discuz/forum.php"><?echo i18n('forum');?></a></li></ul>
		<div class="btn-group navbar-right hidden-xs">
			<button type="button" class="btn btn-default dropdown-toggle navbar-btn" data-toggle="dropdown">
				<? echo i18n('select_language')?><span class="caret"></span>
			</button>
			<ul class="dropdown-menu" role="menu">
				<li><a href="//en.jav321.com<? echo $_SERVER['REQUEST_URI']; ?>" target="_self">English</a></li>
				<li><a href="//jp.jav321.com<? echo $_SERVER['REQUEST_URI']; ?>" target="_self">日本語</a></li>
				<li><a href="//tw.jav321.com<? echo $_SERVER['REQUEST_URI']; ?>" target="_self">正體中文</a></li>
				<li><a href="//cn.jav321.com<? echo $_SERVER['REQUEST_URI']; ?>" target="_self">简体中文</a></li>
			</ul>
		</div>
	</div>
</nav>
</div></div>

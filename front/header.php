<?php header("Content-type: text/html; charset=UTF-8");
require("hm.php");
$_hmt = new _HMT("3e35fb8628ca01f87b121531ca7e1371");
$_hmtPixel = $_hmt->trackPageView();
?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta http-equiv="Content-Type" content="text/html;charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>
<?php
if (isset($html_title)) {
	echo $html_title;
}
if (strpos($_SERVER['REQUEST_URI'], 'ck101') > 0 || strpos($_SERVER['REQUEST_URI'], 'user') > 0) {
	echo i18n(' ck101 卡提諾論壇');
}
else {
	echo i18n(' PTT批踢踢實業坊');
}
?>
</title>
<link rel="stylesheet" href="http://cdn.bootcss.com/twitter-bootstrap/3.0.3/css/bootstrap.min.css">
<link rel="stylesheet" href="<?echo $static_host;?>/css/jquery-ui.css">
<script src="<?echo $static_host;?>/js/jquery.min.js"></script>
<script src="http://cdn.bootcss.com/twitter-bootstrap/3.0.3/js/bootstrap.min.js"></script>
<script src="<?echo $static_host;?>/js/jquery-ui.js"></script>
<script src="<?echo $static_host;?>/js/jquery.lazyload.min.js"></script>
<script src="<?echo $static_host;?>/js/auto_complete.js?v=2"></script>
<meta name="baidu-site-verification" content="uo47eIda6W" />
<meta property="qc:admins" content="2746676521624354063757" />
<style>
body,button, input, select, textarea,h1 ,h2, h3, h4, h5, h6 { font-family: 'Microsoft YaHei','宋体' , Tahoma, Helvetica, Arial, sans-serif;}
</style>
<?
if ($_SERVER['SCRIPT_NAME'] == '/index.php') {
	echo '<base target="_blank" />';
}
?>
</head>
<body>
<script>  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');  ga('create', 'UA-17088225-4', 'ezptt.com');  ga('send', 'pageview');</script>
<div class="row">
<div class="col-md-8 col-md-offset-2 col-xs-12">
<nav class="navbar navbar-default" role="navigation">
<div class="collapse navbar-collapse">
<ul class="nav navbar-nav">
<li <?if ($_SERVER['REQUEST_URI'] == '/' || strpos($_SERVER['REQUEST_URI'], '/board') === 0 || strpos($_SERVER['REQUEST_URI'], '/article') === 0 || strpos($_SERVER['REQUEST_URI'], '/thread') === 0) echo 'class="active"'?>><a href="/">PTT</a></li>
<li <?if (strpos($_SERVER['REQUEST_URI'], '/disp') === 0 || strpos($_SERVER['REQUEST_URI'], '/topic') === 0) echo 'class="active"'?>><a href="/disp">Disp</a></li>
</ul>
<form class="navbar-form navbar-left" role="search" action="/select_board" method="POST">
<div class="form-group">
<input type="text" id="select_board" name="en_name" class="form-control" placeholder="<? echo i18n('xuanzekanban') ?>">
</div>
<button type="submit" class="btn btn-default">Submit</button>
</form>
<form class="navbar-form navbar-left" role="search" action="/query_author" method="POST">
<div class="form-group">
<input type="text" name="author" class="form-control" placeholder="<? echo i18n('chaxunzuozhe') ?>">
</div>
<button type="submit" class="btn btn-default">Submit</button>
</form>
<div class="btn-group navbar-right">
<button type="button" class="btn btn-default dropdown-toggle navbar-btn" data-toggle="dropdown">
<? echo i18n('xuanzeyuyan')?><span class="caret"></span>
</button>
<ul class="dropdown-menu" role="menu">
<li><a href="http://www.ucptt.com<? echo $_SERVER['REQUEST_URI']; ?>">正體中文</a></li>
<li><a href="http://cn.ucptt.com<? echo $_SERVER['REQUEST_URI']; ?>">简体中文</a></li>
</ul>
</div>
</div>
</nav>
</div>
</div>
<div class="row">
<div class="col-md-1 col-md-offset-9 col-xs-1 col-xs-offset-9">
</div></div>
<div class="row">

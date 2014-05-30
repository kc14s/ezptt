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
echo i18n(' PTT批踢踢實業坊');
?>
</title>
<link rel="stylesheet" href="http://cdn.bootcss.com/twitter-bootstrap/3.0.3/css/bootstrap.min.css">
<script src="http://cdn.bootcss.com/jquery/1.10.2/jquery.min.js"></script>
<script src="http://cdn.bootcss.com/twitter-bootstrap/3.0.3/js/bootstrap.min.js"></script>
<script src="<?echo $static_host;?>/js/jquery.lazyload.min.js"></script>
<meta property="qc:admins" content="2746676521624354063757" />
<style>
body,button, input, select, textarea,h1 ,h2, h3, h4, h5, h6 { font-family: 'Microsoft YaHei','宋体' , Tahoma, Helvetica, Arial, sans-serif;}
</style>
</head>
<body>
<script>  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');  ga('create', 'UA-17088225-4', 'ezptt.com');  ga('send', 'pageview');</script>
<div class="row"><div class="col-md-1 col-md-offset-9 col-xs-1 col-xs-offset-9">
<div class="btn-group">
<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
<? echo i18n('xuanzeyuyan')?><span class="caret"></span>
</button>
<ul class="dropdown-menu" role="menu">
<li><a href="http://www.ezptt.com<? echo $_SERVER['REQUEST_URI']; ?>">正體中文</a></li>
<li><a href="http://cn.ezptt.com<? echo $_SERVER['REQUEST_URI']; ?>">简体中文</a></li>
</ul>
</div>
</div></div>
<div class="row">

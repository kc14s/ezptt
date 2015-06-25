<?php header("Content-type: text/html; charset=UTF-8");
?>
<!DOCTYPE HTML>
<html lang="en">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="applicable-device" content="pc,mobile">
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>
<?php
if (isset($html_title)) {
	echo $html_title;
}
echo ' reddit';
?>
</title>
<link rel="stylesheet" href="http://cdn.bootcss.com/twitter-bootstrap/3.3.4/css/bootstrap.min.css">
<script src="http://libs.useso.com/js/jquery/2.1.1/jquery.min.js"></script>
<script src="http://libs.useso.com/js/jquery.lazyload/1.9.1/jquery.lazyload.min.js"></script>
<meta name="baidu-site-verification" content="frFxVvLfy9" />
<?
if (isset($target)) {
	echo "<base target=\"$target\" />";
}
?>
<script>
var _hmt = _hmt || [];
(function() {
 var hm = document.createElement("script");
 hm.src = "//hm.baidu.com/hm.js?eac39b37184c5d0dae2c0922ccb938a2";
 var s = document.getElementsByTagName("script")[0]; 
 s.parentNode.insertBefore(hm, s);
 })();
</script>
<style>
body { overflow-x: hidden;}
</style>
</head>
<body>
<div class="container"><div class="row">

<?php header("Content-type: text/html; charset=UTF-8");
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
echo ' 天涯';
?>
</title>
<link rel="stylesheet" href="http://cdn.bootcss.com/twitter-bootstrap/3.0.3/css/bootstrap.min.css">
<style>
body,button, input, select, textarea,h1 ,h2, h3, h4, h5, h6 { font-family: 'Microsoft YaHei','宋体' , 'Tahoma', 'Helvetica', 'Arial', 'sans-serif';}
</style>
</head>
<body>
<div class="row">

<?php header("Content-type: text/html; charset=UTF-8");
?>
<!DOCTYPE HTML>
<html lang="zh">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>
<?php
if (isset($html_title)) {
	echo $html_title;
}
?>
</title>
<link rel="stylesheet" href="http://cdn.bootcss.com/twitter-bootstrap/3.0.3/css/bootstrap.min.css">
</head>
<body>

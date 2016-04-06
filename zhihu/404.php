<?
$html = '<p align="center"><img src="https://encrypted-tbn3.gstatic.com/images?q=tbn:ANd9GcRlhHTTVE17vIV2GSj6T8G7i3h6mGi_7wGtCSgARKzFnFw9PZW6" /></p>';

header('HTTP/1.1 404 Not Found');
require_once('header.php');
echo $html;
require_once('footer.php');
?>

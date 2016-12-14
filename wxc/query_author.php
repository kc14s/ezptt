<?
$author = $_POST['author'];
$author = trim($author);
header("Location: /author/".urlencode($author)."/1", TRUE, 301);
?>

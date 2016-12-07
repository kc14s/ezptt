<?
$author = $_POST['author'];
$author = trim($author);
header("Location: /author/$author/1", TRUE, 301);
?>

<?
$author = $_POST['author'];
$author = trim($author);
header("Location: /author/$author", TRUE, 301);
?>

<?
require_once("init.php");

$db_conn = conn_db();
mysql_select_db('zhihu', $db_conn);
list($id_min, $id_max) = execute_vector("select min(id), max(id) from answer");
$id = rand($id_min, $id_max);
$pub_time = execute_scalar("select pub_time from answer where id < $id order by id desc limit 1");
$ts = strtotime($pub_time);
$char = 'h';
if (isset($_GET['reply']) && $_GET['reply'] == 1) $char = 'r';
else if (isset($_GET['good']) && $_GET['good'] == 1) $char = 's';
else if (isset($_GET['hot']) && $_GET['hot'] == 1) $char = 'h';
header("Location: /${char}before/$ts", true, 302);
?>


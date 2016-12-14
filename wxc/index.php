<?
require_once("init.php");
require_once("i18n.php");
$db_conn = conn_db();
mysql_select_db('wxc', $db_conn);

$target = '_blank';
$sql = 'select board_en_name, tid, title from topic where pub_time > now() - interval 1 day order by hot desc limit 500';
$sql = 'select board_en_name, tid, title from topic order by reply_num desc limit 500';
$sql = 'select board_en_name, tid, title from topic order by pub_time desc limit 500';
$result = mysql_query($sql);
$bid_count = array();
$groups = array();
$pic_topics = array();
//$group_names = array('知性感性', '休闲娱乐', '社会信息', '游戏天地', '体育健身', '电脑技术', '文化人文', '学术科学', '国内院校');
while (list($en_name, $tid, $title) = mysql_fetch_array($result)) {
	if (!isset($board_count[$en_name])) {
		$board_count[$en_name] = 1;
	}
	else {
		++$board_count[$en_name];
	}
	if ($board_count[$en_name] > 3) continue;
	list($group, $cn_name) = execute_vector("select group_name, cn_name from board where en_name = '$en_name'");
	$groups[$group][] = array($en_name, $cn_name, $tid, $title);
	if (!in_array($group, $group_names)) {
		$group_names[] = $group;
	}
}

$html .= "<div class=\"row\"><div class=\"col-md-8 col-md-offset-2 col-xs-12\">";
foreach ($group_names as $group_name) {
	if (!isset($groups[$group_name])) continue;
	$topics = $groups[$group_name];
	if (!isset($topics) || count($topics) == 0) continue;
	$html .= '<div class="panel panel-info">';
	$html .= '<div class="panel-heading">'.i18n($group_name).'</div>';
//	$html .= '<div class="panel-body">';
	$html .= '<div class="list-group">';
	foreach ($topics as $topic) {
		list($en_name, $cn_name, $tid, $title) = $topic;
		$title = i18n($title);
		$title = htmlspecialchars($title);
		$html .="<a class=\"list-group-item\" href=\"/topic/$en_name/$tid\">$title<span class=\"pull-right\">$cn_name</span>";
		$html .= '</a>';
	}
//	$html .= '</div></div></div>';
	$html .= '</div></div>';
}
$html .= '</div>';

$html .= '</div>';
require_once('header.php');
echo $html;
require_once('footer.php');
?>


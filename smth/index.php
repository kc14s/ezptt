<?
require_once("init.php");
$db_conn = conn_db();
require_once("i18n.php");

$target = '_blank';
$sql = 'select bid, gid, title from topic where pub_time > now() - interval 1 day order by reply_num desc limit 500';
$result = mysql_query($sql);
$bid_count = array();
$groups = array();
$pic_topics = array();
$group_names = array('知性感性', '休闲娱乐', '社会信息', '游戏天地', '体育健身', '电脑技术', '文化人文', '学术科学', '国内院校');
while (list($bid, $gid, $title) = mysql_fetch_array($result)) {
	if (!isset($bid_count[$bid])) {
		$bid_count[$bid] = 1;
	}
	else {
		++$bid_count[$bid];
	}
	if ($bid_count[$bid] > 3) continue;
	list($group, $en_name, $cn_name) = execute_vector("select `group`, en_name, cn_name from board where bid = $bid");
	$groups[$group][] = array($en_name, $cn_name, $gid, $title);
	$att_id = execute_scalar("select att_id from attachment where bid = $bid and aid = $gid order by att_id limit 1");
	if (isset($att_id)) {
		if (count($pic_topics) < 10 || $bid == 1349 || $bid == 872) {
			$pic_topics[] = array($en_name, $cn_name, $gid, $title, $att_id);
		}
	}
}

$html .= "<div class=\"row\"><div class=\"col-md-6 col-md-offset-2 col-xs-12\">";
//$html .= $google_320_100;
foreach ($group_names as $group_name) {
	if (!isset($groups[$group_name])) continue;
	$topics = $groups[$group_name];
	if (!isset($topics) || count($topics) == 0) continue;
	$html .= '<div class="panel panel-info">';
	$html .= '<div class="panel-heading">'.i18n($group_name).'</div>';
//	$html .= '<div class="panel-body">';
	$html .= '<div class="list-group">';
	foreach ($topics as $topic) {
		list($en_name, $cn_name, $gid, $title) = $topic;
		$title = i18n($title);
		$title = htmlspecialchars($title);
		$html .="<a class=\"list-group-item\" href=\"/topic/$en_name/$gid\">$title<span class=\"pull-right\">$cn_name</span>";
		$html .= '</a>';
	}
//	$html .= '</div></div></div>';
	$html .= '</div></div>';
}
$html .= '</div>';

$html .= '<div class="col-md-2 hidden-xs hidden-sm">';
foreach ($pic_topics as $topic) {
	list($en_name, $cn_name, $gid, $title, $att_id) = $topic;
	$html .= '<div class="row">';
	$img_url = "$smth_static_host/nForum/att/$en_name/$gid/$att_id";
	$html .= '<div class="thumbnail"><a href="'."/topic/$en_name/$gid".'"><img src="'.$img_url.'" class="img-responsive" /></a><div class="caption"><p><a href="'."/topic/$en_name/$gid".'">['.i18n($cn_name).'] '.i18n($title).'</a></p></div></div></div>';
}
$html .= '</div></div>';
require_once('header.php');
echo $html;
require_once('footer.php');
?>


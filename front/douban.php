<?
require_once("init.php");
$db_conn = conn_douban_db();
require_once("i18n.php");
//require_once("../Mobile-Detect/Mobile_Detect.php");
global $lang, $i18n, $is_default_tw;
$is_spider = is_spider();
$is_from_search_engine = is_from_search_engine();
//$detect = new Mobile_Detect;
//$baidu_ad = $detect->isMobile() && !$detect->isTablet() ? $baidu_ucptt_mobile_6_5 : $baidu_ucptt_pc_960_90;
$tid = (int)$_GET['tid'];
list($bid, $uid, $title, $ups, $pub_time, $content) = execute_vector("select bid, uid, title, ups, pub_time, content from topic where tid = $tid");
$board_cn_name = execute_scalar("select bname from board where bid = '$bid'");
$board_cn_name = i18n($board_cn_name);
list($uname, $unick, $uicon) = execute_vector("select uname, nick, uicon from user where uid = '$uid'");

if (!isset($title)) {
	header('HTTP/1.1 404 Not Found');
	exit();
}
$title = i18n($title);
$topic_title = $title;
$html_title = "$title $author";
$html = '';
$lz = $uid;
$topic_pub_time = isset($pub_time) ? $pub_time : '';
$articles[] = array($uname, $pub_time, $content, $uid);
#$result = mysql_query("select comment.uid, uname, pub_time, content from comment, user where tid = $tid and comment.uid = user.uid order by pub_time");
$result = mysql_query("select uid, pub_time, content from comment where tid = $tid order by pub_time");
while(list($uid, $pub_time, $content) = mysql_fetch_array($result)) {
	$uname = execute_scalar("select uname from user where uid = '$uid'");
	$articles[] = array($uname, $pub_time, $content, $uid);
}

/*
if (false || $is_spider) {
	list($tid_max, $tid_min) = execute_vector('select max(tid), min(tid) from topic');
	$result = mysql_query('select bid, title, author, tid from topic where tid > '.rand($tid_min, $tid_max).' order by tid limit 10');
	while (list($bid, $title, $author, $tid) = mysql_fetch_array($result)) {
		$old_topics[] = array($title, $author, $bid, $tid);
	}
}
//*/

if (!$is_spider) {
	$html .= $scupio_video_expand;
}
$html .= "<div class=\"col-md-8 col-md-offset-2 col-xs-12\"><ol class=\"breadcrumb\"><li>豆瓣</li><li>$board_cn_name</li></ol><h3>".i18n($topic_title)."</h3>";
if (!$is_loyal_user) {
//	$html .= $google_320_100;
//	$html .= $chitika_468_60;
//	$html .= $bloggerads_banner;
//	$html .= $digitalpoint_468_60;
//	$html .= $adcash_popunder;
//	$html .= $baidu_ad;
}
$floor = 1;
if (isset($articles)) {
	foreach ($articles as $article) {
		list($uname, $pub_time, $content, $uid) = $article;
		$html .= '<div class="panel panel-info">';
		$html .= '<div class="panel-heading">';
		//$html .= ($uname === $lz ? i18n('louzhu') : i18n('zuozhe')).': ';
		if (true && ($uid == $lz || execute_scalar("select count(*) from topic where uid = '$uid'") > 0)) {
			$html .= "<a href=\"/douban_user/$uid/1\">$uname</a>";
		}
		else {
			$html .= $uname;
		}
		$html .= "<span class=\"pull-right\">$pub_time</span>";
		$html .= '</div>';
		$html .= '<div class="panel-body">';
		if ($floor == 1) {
			$html .= $adsense_ucptt;
		}
		$content = i18n($content);
		if (false && strlen($content) < 1000 && !(strpos($content, 'http://') === false)) {
			$content = preg_replace("/(http:\/\/[\w\/\.\_\-]+\.jpg)/", "<br><a href=\"$1\" target=\"_blank\"><img data-original=\"$1\" /></a>", $content);
			$content = preg_replace("/(http:\/\/[\w\/\.\_\-]+\.png)/", "<br><a href=\"$1\" target=\"_blank\"><img data-original=\"$1\" /></a>", $content);
			$content = preg_replace("/(http:\/\/[\w\/\.\_\-]+\.gif)/", "<br><a href=\"$1\" target=\"_blank\"><img data-original=\"$1\" /></a>", $content);
			$content = preg_replace("/(http:\/\/ppt.cc[\w\/\.\_\-]+)/", "<br><a href=\"$1\" target=\"_blank\"><img data-original=\"$1@.jpg\" /></a>", $content);
			$content = preg_replace("/http:\/\/(imgur.com[\w\/\.\_\-]+)/", "<br><a href=\"$1\" target=\"_blank\"><img data-original=\"http://i.$1.jpg\" /></a>", $content);
			$content = preg_replace("/http:\/\/miupix.cc\/pm\-(\w+)/", "<br><a href=\"http://miupix.cc/dm/$1/uploadFromiPhone.jpg\" target=\"_blank\"><img data-original=\"http://miupix.cc/dm/$1/uploadFromiPhone.jpg\" /></a>", $content);
		}
		$html .= $content;
		if ($floor == 1) {
			$html .= $adsense_ucptt;
			$html .= '<div class="addthis_sharing_toolbox"></div>';
		}
		$html .= '</div></div>';
		if (!$is_loyal_user) {
			if (true || $floor == 1 || $floor == 2) {
				$html .= $scupio_728_90;
//				$html .= $baidu_ad;
			}
			else if ($floor == 3) {
//				$html .= $bloggerads_banner;
				$html .= $gg91_click;
			}
			else {
				$html .= $ads360_960_90;
			}
		}
		++$floor;
	}
}
/*
$result = mysql_query("select tid, title, author from topic where bid = $bid and tid < $tid order by tid desc limit 20");
while (list($prev_tid, $title, $author) = mysql_fetch_array($result)) {
	$prev_topics[] = array($prev_tid, $title, $author);
}
$html .= '<div class="panel panel-default"><div class="panel-heading">'.i18n('jixuyuedu').'</div>';
$html .= '<div class="list-group">';
foreach ($prev_topics as $prev_topic) {
	list($prev_tid, $title, $author) = $prev_topic;
	$html .= "<a href=\"/ck101/$bid/$prev_tid\" class=\"list-group-item\">".i18n($title)."<span class=\"pull-right\">$author</span></a>";
}
$html .= '</div></div>';
*/
if (false || $is_spider) {
	$html .= get_old_ck101_topic_html();
	$html .= get_rand_douban_topic_html();
}
$html .= '</div>';
//$html .= '<script type="text/javascript">var zx_aid = 1;var zx_uid = 10799;var zoneid = 11554;</script><script type="text/javascript" charset="utf-8" src="http://click.9cpc.com/view.js"></script>';

require_once('header.php');
echo $html;
require_once('footer.php');
//echo $_SERVER['SCRIPT_FILENAME'];
//echo $_SERVER['SCRIPT_NAME'];
?>


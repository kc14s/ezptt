<?
//ptt
require_once("init.php");
$db_conn = conn_ezptt_db();
require_once("i18n.php");
//require_once("../Mobile-Detect/Mobile_Detect.php");
$is_spider = is_spider();
$is_from_search_engine = is_from_search_engine();
if (0 && $ptt_allow == 0 && !$is_spider && !$is_from_search_engine) {
	header('HTTP/1.1 404 Not Found');
	exit();
}
$html = '';
$en_name = $_GET['en_name'];
$tid1 = (int)$_GET['tid1'];
$tid2 = $_GET['tid2'];
$bid = execute_scalar("select id from board where en_name = '$en_name'");
list($author, $pub_time, $title, $content, $attachment) = execute_vector("select author, pub_time, title, content, attachment from topic where tid1 = $tid1 and tid2 = '$tid2'");
if (!isset($title)) {
    header('HTTP/1.1 301 Moved Permanently');
	header('Location: /404');
	exit();
}
$title = i18n($title);
$topic_title = $title;
$html_title = "$title $author";
$lz = $author;
if ($is_spider) {
	$pub_time = date("Y-m-d").substr($pub_time, 10);
}
$topic_pub_time = $pub_time;
$attachments = array();
$nick = i18n(execute_scalar("select nick from user where user_id = '$author'"));
if (true || $attachment) {
	$attachments = execute_column("select concat(md5, '.', ext_name) from attachment where bid = $bid and tid1 = $tid1 and tid2 = '$tid2'");
}
$articles[] = array($author, $pub_time, $content, $attachments, $nick, true);
$result = mysql_query("select author, reply_time, content from reply where tid1 = $tid1 and tid2 = '$tid2'");
while(list($author, $reply_time, $content) = mysql_fetch_array($result)) {
	$nick = i18n(execute_scalar("select nick from user where user_id = '$author'"));
	$author_link = execute_scalar("select count(*) from topic where author = '$author'") > 5;
	if ($is_spider) {
		$reply_time = date("Y-m-d").substr($reply_time, 10);
	}
	$articles[] = array($author, $reply_time, $content, array(), $nick, $author_link);
}
if ($topic_pub_time == '') {
	$topic_pub_time = date("Y-m-d");
}
$result = mysql_query("select title, tid1, tid2, author from topic where bid = $bid and pub_time <= '$topic_pub_time' and tid1 <> $tid1 order by pub_time desc limit 10");
while (list($prev_title, $prev_tid1, $prev_tid2, $prev_author) = mysql_fetch_array($result)) {
	$prev_topics[] = array($prev_title, $prev_tid1, $prev_tid2, $prev_author);
}

if (false || $is_spider) {
	list($tid_max, $tid_min) = execute_vector('select max(tid1), min(tid1) from topic');
	$result = mysql_query('select bid, title, author, tid1, tid2 from topic where tid1 > '.rand($tid_min, $tid_max).' order by tid1 limit 10');
	while (list($bid, $title, $author, $tid1, $tid2) = mysql_fetch_array($result)) {
		$old_en_name = execute_scalar("select en_name from board where id = $bid");
		$old_topics[] = array($old_en_name, $title, $author, $tid1, $tid2);
	}
}

if (!$is_loyal_user && !$is_spider) {
	$html .= $scupio_video_expand;
}
$html .= "<div class=\"col-md-8 col-md-offset-2 col-xs-12\"><ol class=\"breadcrumb\"><li><a href=\"/\">PTT</a></li><li><a href=\"/board/$en_name/\">$en_name</a></li></ol><h3>".i18n($topic_title)."</h3></div>";
//$detect = new Mobile_Detect;
//$baidu_ad = $detect->isMobile() && !$detect->isTablet() ? $baidu_ucptt_mobile_6_5 : $baidu_ucptt_pc_960_90;
$html .= "<div class=\"col-md-6 col-md-offset-2 col-xs-12\">";
if (false || !$is_loyal_user) {
//	$html .= $google_320_100;
//	$html .= $chitika_468_60;
//	$html .= $bloggerads_banner;
	$html .= $scupio_728_90;
//	$html .= "<p>$qadabra_728_90</p>";
//	$html .= $infolinks;
//	$html .= $qadabra_800_440_lightbox;
//	$html .= $qadabra_160_600_left_slider;
//	$html .= $qadabra_160_600_right_slider;
//	$html .= $revenuehits_popunder;
//	$html .= $baidu_ad;
}
$floor = 1;
foreach ($articles as $article) {
	list($author, $time, $content, $attachments, $nick, $author_link) = $article;
	$html .= '<div class="panel panel-info">';
	$html .= '<div class="panel-heading">';
	if ($author_link) {
		$html .= ($author === $lz ? i18n('louzhu') : i18n('zuozhe')).": <a href=\"/author/$author\">$author</a>".(isset($nick) && strlen($nick) > 0 ? " ($nick)" : '');
	}
	else {
		$html .= ($author === $lz ? i18n('louzhu') : i18n('zuozhe')).": $author".(isset($nick) && strlen($nick) > 0 ? " ($nick)" : '');
	}
	$html .= " &nbsp; <span class=\"pull-right\">$time</span>";
	$html .= '</div>';
	$html .= '<div class="panel-body">';
	$content = i18n(preg_replace("/\n+/", "\n", trim($content)));
	if (true && $is_loyal_user && !$is_spider && !(strpos($content, 'http://') === false)) {
		$content = preg_replace("/(http:\/\/[\w\/\.\_\-]+\.jpg)/", "<br><a href=\"$1\" target=\"_blank\"><img data-original=\"$1\" class=\"img-responsive\" /></a>", $content);
		$content = preg_replace("/(http:\/\/[\w\/\.\_\-]+\.png)/", "<br><a href=\"$1\" target=\"_blank\"><img data-original=\"$1\" class=\"img-responsive\" /></a>", $content);
		$content = preg_replace("/(http:\/\/[\w\/\.\_\-]+\.gif)/", "<br><a href=\"$1\" target=\"_blank\"><img data-original=\"$1\" class=\"img-responsive\" /></a>", $content);
		$content = preg_replace("/(http:\/\/ppt.cc[\w\/\.\_\-\~]+)/", "<br><a href=\"$1\" target=\"_blank\"><img data-original=\"$1@.jpg\" class=\"img-responsive\" /></a>", $content);
		$content = preg_replace("/http:\/\/(imgur.com[\w\/\.\_\-]+)/", "<br><img data-original=\"http://i.$1.jpg\" class=\"img-responsive\" />", $content);
		$content = preg_replace("/http:\/\/miupix.cc\/pm\-(\w+)/", "<br><a href=\"http://miupix.cc/dm/$1/uploadFromiPhone.jpg\" target=\"_blank\"><img data-original=\"http://miupix.cc/dm/$1/uploadFromiPhone.jpg\" class=\"img-responsive\" /></a>", $content);
	}
	//$content = preg_replace("/(http:\/\/ppt.cc[\w\/\.\_\-]+)</", "<a href=\"$1\" target=\"_blank\"><img src=\"$1@.jpg\" /></a><", $content);
	$html .= str_replace("\n", '<br>', $content);
	if (isset($attachments) && count($attachments) > 0) {
		foreach ($attachments as $attachment) {
			if (file_exists("att/$attachment")) {
				$html .= "<br><img data-original=\"$static_host/att/$attachment\" class=\"img-responsive\" />";
			}
		}
	}
	if ($floor == 1) {
		$html .= '<div class="addthis_sharing_toolbox"></div>';
		if (!$is_loyal_user) {
			$html .= $juicyads_banner_ucptt;
		}
	}
	$html .= '</div>';
	$html .= '</div>';
	if (!$is_loyal_user) {
		if (false || $floor == 1 || $floor == 2 || $floor == 3) {
//			$html .= $baidu_ad;
			$html .= $v3_960_130;
		}
		else if ($floor >= 4 && $floor <= 5) {
			$html .= $scupio_728_90;
			//$html .= $sogou_760_90;
			//$html .= $av_show_468_60_1;
			//$html .= $digitalpoint_468_60;
			//$html .= $bloggerads_banner;
		}
		else if ($floor >= 6) {
//			$html .= $lianmeng9_cpc_950_90;
//			$html .= $gg91_click;
//			$html .= $ads360_960_90;	//not support
		}
		else if ($floor >= 7 && $floor <= 9){
//			$html .= $lianmeng9_cpv_950_90;
//			$html .= $xu9_980_90;
		}
	}
	++$floor;
}
if (isset($prev_topics)) {
		$html .= '<div class="panel panel-default"><div class="panel-heading">'.i18n('jixuyuedu').'</div>';
		$html .= '<div class="list-group">';
		foreach ($prev_topics as $topic) {
				list($title, $tid1, $tid2, $author) = $topic;
				$html .= "<a href=\"/article/$en_name/$tid1/$tid2\" class=\"list-group-item\">".i18n($title)."<span class=\"pull-right\">$author</span></a>";
		}
		$html .= '</div></div>';
}
if (isset($old_topics)) {
		$html .= '<div class="panel panel-default"><div class="panel-heading">'.i18n('jixuyuedu').'</div>';
		$html .= '<div class="list-group">';
		foreach ($old_topics as $topic) {
				list($en_name, $title, $author, $tid1, $tid2) = $topic;
				$html .= "<a href=\"/article/$en_name/$tid1/$tid2\" class=\"list-group-item\">".i18n($title)." $author</a>";
		}
		$html .= '</div></div>';
}
/*
$html .= "<script>if (window.location.href.indexOf('ucptt.cor') == -1) {
        window.location = 'http://www.ucptt.com/';
		}'
		</script>";
*/
if (false || $is_spider) {
//		$html .= get_rand_reddit_topic_html();
}
if (false || $is_spider) {
	$html .= get_old_ck101_topic_html();
	$html .= get_rand_douban_topic_html();
}
$html .= '</div>';
if ($en_name == 'japanavgirls') {
	require_once('../dmm/dmm_lib.php');
	$dmm_db = conn_dmm_db();
	$result = mysql_query("select title, sn, channel from video where rank >= ".rand(0, 100)." order by rank limit 5");
	while (list($title, $sn, $channel) = mysql_fetch_array($result)) {
		$video = array($title, $sn, $channel);
		$videos[] = $video;
	}
	$html .= '<div class="col-md-2 hidden-xs hidden-sm">';
	$dmm_domain = 'www';
	if ($lang == 'zh_CN') $dmm_domain = 'www';
	else if ($lang = 'zh_TW') $dmm_domain = 'tw';
	foreach ($videos as $video) {
		list($title, $sn, $channel) = $video;
		$html .= '<div class="row">';
		$html .= "<div class=\"thumbnail\"><a href=\"https://$dmm_domain.jporndb.com/video/$sn\" target=\"_blank\"><img data-original=".get_cover_img_url($sn, $channel)."><br>$title</a></div></div>";
	}
	$html .= '</div>';
}
else if (!$is_loyal_user) {
	$html .= get_rand_dmm_column_html();
/*
	$html .= '<div class="col-md-2 hidden-xs hidden-sm">';
	//$jandan_pics = get_jandan_pics(rand(0, 1), $floor);
	$jandan_pics = get_jandan_pics(0, $floor > 10 ? 10 : $floor);
	foreach ($jandan_pics as $jandan_pic) {
		list($id, $url, $width, $height) = $jandan_pic;
		$html .= '<div class="row"><div class="thumbnail"><a href="'."/jd/0/$id".'"><img data-original="'.$url.'" class="img-responsive" /></a></div></div>';
	}
	$html .= '</div>';
*/
}
//$html .= '<p><a href="/">PTT</a> <a href="/disp">disp</a></p></div>';
//$html .= '<script type="text/javascript">var zx_aid = 1;var zx_uid = 10799;var zoneid = 11554;</script><script type="text/javascript" charset="utf-8" src="http://click.9cpc.com/view.js"></script>';

require_once('header.php');
echo $html;
require_once('footer.php');
?>


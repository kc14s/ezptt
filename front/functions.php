<?
require_once("config.php");
require_once("data.php");
function is_spider() {
	if (!isset($_SERVER['HTTP_USER_AGENT'])) return false;
	$ua = $_SERVER['HTTP_USER_AGENT'];
	if (strpos($ua, 'Baiduspider') === false && strpos($ua, 'Googlebot') === false && strpos($ua, 'baidu Transcoder') === false && strpos($ua, 'msnbot') === false && strpos($ua, 'Sogou') === false && strpos($ua, 'Sosospider') === false && strpos($ua, 'Yahoo!') === false && strpos($ua, 'Kmspider') === false && strpos($ua, 'Mediapartners-Google') === false && strpos($ua, 'YoudaoBot') === false && strpos($ua, '360Spider') === false && strpos($ua, 'bingbot') === false && strpos($ua, 'JikeSpider') === false && strpos($ua, 'EasouSpider') === false && strpos($ua, 'addthis') === false && strpos($ua, 'yandex.com') === false) {
		return false;
	}
	else {
		return true;
	}
}

function is_google_spider() {
	if (!isset($_SERVER['HTTP_USER_AGENT'])) return false;
	$ua = $_SERVER['HTTP_USER_AGENT'];
	if (strpos($ua, 'Googlebot') === false && strpos($ua, 'Mediapartners-Google') === false) {
		return false;
	}
	else {
		return true;
	}
}

function is_baidu_spider() {
	if (!isset($_SERVER['HTTP_USER_AGENT'])) return false;
	$ua = $_SERVER['HTTP_USER_AGENT'];
	if (strpos($ua, 'Baiduspider') === false) {
		return false;
	}
	else {
		return true;
	}
}

function is_windows_user() {
	if (!isset($_SERVER['HTTP_USER_AGENT'])) return false;
	if (strpos($_SERVER['HTTP_USER_AGENT'], "Windows NT") === false) {
		return false;
	}
	else {
		return true;
	}
}

function is_image($file_name) {
	if (strpos($file_name, ".")) {
		$suffix = substr($file_name, strrpos($file_name, ".") + 1);
	}
	else {
		$suffix = $file_name;
	}
	$suffix = strtolower($suffix);
	if ($suffix == "jpg" || $suffix == "gif" || $suffix == "jpeg" || $suffix == "png" || $suffix == 'tif' || $suffix == 'tiff' || $suffix == 'bmp') {
		return true;
	}
	else {
		return false;
	}
}

function get_file_size($file_path) {
	if (file_exists($file_path)) {
		return abs(filesize($file_path));
	}
	return -1;
}

function parse_query_string($query_string) {
	$parsed = array();
	if (strpos($query_string, '?') > 0) $query_string = substr($query_string, strpos($query_string, '?') + 1);
//	if (strpos($query_string, '&') === false) return $parsed;
	$arr = explode('&', $query_string);
	foreach ($arr as $pair) {
		$key_value = explode('=', $pair);
		if (count($key_value) == 2) {
			$parsed[$key_value[0]] = $key_value[1];
		}
	}
	return $parsed;
}

function get_search_engine_query() {
	$search_engine_requests = parse_query_string($_SERVER['HTTP_REFERER']);
	$query = '';
	$query_indicators = array('wd', 'q', 'word', 'query', 'search');
	foreach ($query_indicators as $query_indicator) {
		if (isset($search_engine_requests[$query_indicator])) {
			$query = urldecode($search_engine_requests[$query_indicator]);
			if (true || strpos($_SERVER['HTTP_REFERER'], 'google') > 0) {
				if (!isset($search_engine_requests['ie']) || strpos(strtolower($search_engine_requests['ie']), 'gb') === false) {
					$query = iconv("UTF-8", "GBK//TRANSLIT", $query);
				}
			}
			break;
		}
	}
#	error_log('query '.$query);
	return $query;
}

function get_search_engine_terms() {
	$terms = array();
	$term_set = array();
	if (!isset($_SERVER['HTTP_REFERER'])) {
		return $terms;
	}
	$query = get_search_engine_query();
	if ($query == '') return $terms;
	$terms = explode(' ', $query);
	if (count($terms) > 0) {
		foreach ($terms as $term) {
			if ($term != '') {
				$term_set[strtolower($term)] = 0;
			}
		}
	}
	return $term_set;
}

function get_customized_boards() {
	if (isset($_SESSION['customized_boards'])) {
		return $_SESSION['customized_boards'];
	}
	if (date('U') - $_SESSION['login_smth_tick'] > 60 * 10) {	// smth session expired
		login_newsmth($_SESSION['user'], $_SESSION['password']);
	}
	list($response, $header) = http_request('http://www.newsmth.net/bbsfav.php?select=0', 'GET', array(), $_SESSION['smth_cookie']);
	preg_match_all('/(\w+),1,(\d+),\d+,\'\[[\d\D]+?\]\',\'(\w+)\',/', $response, $matches);
	$boards = array();
	for ($i = 0; $i < count($matches[0]); ++$i) {
		if ($matches[1][$i] == 'true') continue;
		//$board = array($matches[2][$i], $matches[3][$i]);
		$boards[$matches[3][$i]] = 0;
		//array_push($boards, $board);
	}
	$_SESSION['customized_boards'] = $boards;
	return $boards;
}

function str_contain($str, $pattern) {
	return !(strstr($str, $pattern) === false);
}

function str_seperate_contain($str, $s1, $s2) {
	if (strstr($str, $s1) === false) return false;
	if (strstr($str, $s2) === false) return false;
	return true;
}

function execute_scalar($sql ) {
	$result = mysql_query($sql);
	if($result == null){
		return null;
	}
	else{
		while($row = mysql_fetch_array($result)) {
			return $row[0];
		}
	}
}

function execute_vector($sql) {
	$result = mysql_query($sql);
	while($row = mysql_fetch_array($result)) {
		return $row;
	}
	return array();
}

function get_ptt_latest_topics() {
	$ptt_board_num = execute_scalar('select count(*) from ptt_board');
	$rand = rand(0, $ptt_board_num - 10);
	$result = mysql_query("select id from ptt_board limit $rand, 10");
	while (list($bid) = mysql_fetch_array($result)) {
		$topic = execute_vector("select bid, tid, title from ptt_topic where bid = $bid order by pub_time desc limit 1");
		if (count($topic) == 6) {
			$topics[] = $topic;
		}
	}
	return $topics;
}

function execute_dataset($sql) {
	$dataset = array();
	$result = mysql_query($sql);
	while ($row = mysql_fetch_array($result)) {
		$dataset[] = $row;
	}
	return $dataset;
}

function execute_column($sql) {
	$column = array();
	$result = mysql_query($sql);
	while (list($data) = mysql_fetch_array($result)) {
		$column[] = $data;
	}
	return $column;
}

function is_loyal_user() {
	$is_loyal = 0;
	global $is_spider;
	global $is_from_search_engine;
	$loyal_user_uris = array(
	'/' => 1,
	'/hot/' => 1,
	'/pic/' => 1,
	'/comment/' => 1,
	'/author.php' => 1,
	'/user.php' => 1,
	'/index.php' => 1,
	'/disp.php' => 1
	);
	if ($is_from_search_engine) {}
	else if ($is_spider) {
		$is_loyal = 1;
	}
	else {
		if (!isset($_COOKIE['is_loyal']) || $_COOKIE['is_loyal'] == 0) {
			$is_loyal = isset($loyal_user_uris[$_SERVER['REQUEST_URI']]) || isset($loyal_user_uris[$_SERVER['SCRIPT_NAME']]) ? 1 : 0;
			if ($is_loyal) {
				setcookie('is_loyal', $is_loyal, time() + 3600 * 24 * 365, '/');
			}
		}
		else {
			$is_loyal = 1;
		}
	}
	if (!$is_loyal) {
//		error_log('not loyal');
	}
	return $is_loyal;
}

function set_loyal_user() {
	setcookie('is_loyal', $is_loyal, time() + 3600 * 24 * 365, '/');
}

function get_old_ck101_topic_html() {
	require_once('i18n.php');
	list($tid_max, $tid_min) = execute_vector('select max(tid), min(tid) from ck101.topic');
	$result = mysql_query('select bid, title, author, tid from ck101.topic where tid > '.rand($tid_min, $tid_max).' order by tid limit 10');
	while (list($bid, $title, $author, $tid) = mysql_fetch_array($result)) {
		$old_topics[] = array($title, $author, $bid, $tid);
	}
	$html = '<div class="panel panel-default"><div class="panel-heading">'.i18n('jixuyuedu').'</div>';
	$html .= '<div class="list-group">';
	foreach ($old_topics as $topic) {
		list($title, $author, $bid, $tid) = $topic;
		$html .= "<a href=\"http://www.ucptt.com/ck101/$bid/$tid\" class=\"list-group-item\">".i18n($title.' '.$author)." </a>";
	}
	$html .= '</div></div>';
	return $html;
}

function get_rand_douban_topic_html() {
	require_once('i18n.php');
	list($tid_max, $tid_min) = execute_vector('select max(tid), min(tid) from douban.topic');
	//$result = mysql_query('select title, uname, tid from douban.topic force index tid_index, douban.user where douban.topic.uid = douban.user.uid and tid > '.rand($tid_min, $tid_max).' order by tid limit 10');
	$result = mysql_query('select title, tid, uid from douban.topic where tid > '.rand($tid_min, $tid_max).' order by tid limit 10');
	while (list($title, $tid, $uid) = mysql_fetch_array($result)) {
		$uname = execute_scalar("select uname from douban.user where uid = '$uid'");
		$old_topics[] = array($title, $uname, $tid);
	}
	$html = '<div class="panel panel-default"><div class="panel-heading">'.i18n('jixuyuedu').'</div>';
	$html .= '<div class="list-group">';
	foreach ($old_topics as $topic) {
		list($title, $author,  $tid) = $topic;
		$html .= "<a href=\"http://www.ucptt.com/douban/$tid\" class=\"list-group-item\">".i18n($title.' '.$uname)." </a>";
	}
	$html .= '</div></div>';
	return $html;
}

function get_rand_reddit_topic_html() {
	list($id_max, $id_min) = execute_vector('select max(sid), min(sid) from reddit.topic');
	$result = mysql_query('select subreddit, title, author, id from reddit.topic where sid > '.rand($id_min, $id_max).' order by sid limit 10');
	while (list($subreddit, $title, $author, $id) = mysql_fetch_array($result)) {
		$rand_topics[] = array($subreddit, $title, $author, $id);
	}
	$html = '<div class="panel panel-default"><div class="panel-heading">Recommended Topics</div>';
	$html .= '<div class="list-group">';
	foreach ($rand_topics as $topic) {
		list($subreddit, $title, $author, $id) = $topic;
		$html .= "<a href=\"http://www.redditfun.com/reddit/$subreddit/$id/".str_to_url($title)."\" class=\"list-group-item\">$title<span class=\"pull-right\">$author</span></a>";
	}
	$html .= '</div></div>';
	return $html;
}

function get_rand_dmm_topic_html() {
	list($id_max, $id_min) = execute_vector('select max(id), min(id) from dmm.video');
	$result = mysql_query('select title, sn, sn_normalized from dmm.video where id > '.rand($id_min, $id_max).' order by id limit 20');
	while (list($title, $sn, $snn) = mysql_fetch_array($result)) {
		$rand_videos[] = array($title, $sn, $snn);
	}
	$html = '<div class="panel panel-default"><div class="panel-heading">Recommended Video</div>';
	$html .= '<div class="list-group">';
	foreach ($rand_videos as $video) {
		list($title, $sn, $snn) = $video;
		$html .= "<a href=\"/video/$sn\" class=\"list-group-item\">$title $snn</a>";
	}
	$html .= '</div></div>';
	return $html;
}

function get_rand_dmm_column_html() {
	require_once('../dmm/dmm_lib.php');
//	$dmm_db = conn_dmm_db();
	$result = mysql_query("select title, sn, channel from dmm.video where rank >= ".rand(0, 100)." order by rank limit 10");
	while (list($title, $sn, $channel) = mysql_fetch_array($result)) {
		$video = array($title, $sn, $channel);
		$videos[] = $video;
	}
	$html = '<div class="col-md-2 hidden-xs hidden-sm">';
	$dmm_domain = 'www';
	global $lang;
	if ($lang == 'zh_CN') $dmm_domain = 'www';
	else if ($lang = 'zh_TW') $dmm_domain = 'tw';
	foreach ($videos as $video) {
		list($title, $sn, $channel) = $video;
		$html .= '<div class="row">';
		$img_url = get_cover_img_url($sn, $channel);
		$html .= "<div class=\"thumbnail\"><a href=\"https://$dmm_domain.jav321.com/video/$sn\" target=\"_blank\">".get_img_tag($img_url)."<br>$title</a></div></div>";
	}
	$html .= '</div>';
	return $html;
}

function get_rand_zhihu_topic_html() {
	list($id_min, $id_max) = execute_vector('select min(id), max(id) from zhihu.answer');
	$result = mysql_query('select aid, title, author, nick from zhihu.question, zhihu.answer where question.qid = answer.qid and id > '.rand($id_min, $id_max).' order by id limit 20');
	while (list($aid, $title, $author, $nick) = mysql_fetch_array($result)) {
		$rand_topics[] = array($aid, $title, $author, $nick);
	}
	$html = '<div class="panel panel-default"><div class="panel-heading">知乎随机推荐</div>';
	$html .= '<div class="list-group">';
	foreach ($rand_topics as $topic) {
		list($aid, $title, $author, $nick) = $topic;
		#$html .= "<a href=\"/answer/$aid\" class=\"list-group-item\">$title $author $nick</a>";
		$html .= "<a href=\"/answer/$aid\" class=\"list-group-item\">$title $author $nick</a>";
	}
	$html .= '</div></div>';
	return $html;
}

function str_to_url($str) {
	$len = strlen($str);
	$url = '';
	$flag = false;
	for ($i = 0; $i < $len; ++$i) {
		$char = substr($str, $i, 1);
		if (ctype_alpha($char) || ctype_digit($char)) {
			$url .= $char;
			$flag = false;
		}
		else {
			if (!$flag) {
				$url .= '-';
				$flag = true;
			}
		}
	}
	$url = trim($url, '-');
	return $url;
}

function get_percentage($num) {
	return round($num * 100, 2).'%';
}

$domain_suffixes = array(
'com' => 0,
'net' => 0,
'org' => 0,
'cn' => 0,
'edu' => 0,
'cc' => 0,
'hk' => 0,
'tw' => 0,
'co' => 0,
'biz' => 0,
'info' => 0,
'tv' => 0,
'me' => 0,
'jp' => 0,
'edu' => 0,
'' => 0,
'' => 0,
'' => 0
);
function get_domain($url) {
	global $domain_suffixes;
	$url = str_replace('http://', '', $url);
	preg_match('/[\w\.\-]+/', $url, $matches);
	$site = $matches[0];
	$slices = explode('.', $site);
	if ($slices[0] == 'www' || $slices[0] == 'm') {
		array_shift($slices);
	}
	//echo $domain_suffixes[$slices[count($slices) - 1]];
	while (count($slices) > 1 && isset($domain_suffixes[$slices[count($slices) - 1]])) {
		array_pop($slices);
		//print_r($slices);
	}
	return $slices[count($slices) - 1];
}

function valid_digit_1($num) {
	$ret = substr($num, 0, 1);
	for ($i = 1; ; ++$i) {
		if (!ctype_digit(substr($num, $i, 1))) {
			break;
		}
		else {
			$ret .= '0';
		}
	}
	return $ret;
}

function get_ck101_board_random_topic($bid) {
	global $sub_domain;
	list($tid_min, $tid_max) = execute_vector("select min(tid), max(tid) from ck101.topic where bid = $bid");
	$random_tid = rand($tid_min, $tid_max);
	$result = mysql_query("select tid, title, author from ck101.topic where bid = $bid and tid <= $random_tid order by tid desc limit 6");
	$html = '<div class="panel panel-default"><div class="panel-heading">'.i18n('chengrenwenxue').'</div>';
	$html .= '<div class="list-group">';
	while (list($tid, $title, $author) = mysql_fetch_array($result)) {
		$title = i18n($title);
		//$author = i18n($author);
		$lowcase_title = strtolower($title);
		if (strpos($lowcase_title, 'line') > 0 || strpos($lowcase_title, 'wechat') > 0 || strpos($lowcase_title, 'qq') > 0 || strpos($lowcase_title, '茶') > 0) {
			continue;
		}
//		$html .= "<a href=\"https://$sub_domain.ucptt.com/ck101/$bid/$tid\" class=\"list-group-item\" target=\"_blank\">$title<span class=\"pull-right\">$author</span></a>";
		$html .= "<a href=\"https://www.jav321.com/ck101/$bid/$tid\" class=\"list-group-item\" target=\"_blank\">$title<span class=\"pull-right\">$author</span></a>";
	}
	$html .= '</div></div>';
	return $html;
}

function is_from_china() {
//	return false;
	$ip = $_SERVER['REMOTE_ADDR'];
	$nums = explode('.', $ip);
	$sum = 0;
	foreach ($nums as $num) {
		$sum *= 256;
		$sum += $num;
	}
	list($begin, $end) = execute_vector("select begin, end from ezptt.ip_china where begin <= $sum order by begin desc limit 1");
//	error_log("china $sum $begin $end");
	return $sum <= $end;
}

function get_jandan_pics($type, $size = 20) {
		$types = array('jandan_beauty', 'jandan_funny');
		//$counts = array(23564, 63592);
		$ret = array();
		while (count($ret) < $size) {
				list($max, $min) = execute_vector('select max(id), min(id) from '.$types[$type]);
				$rand = rand($min, $max);
				$result = mysql_query("select id, url, enabled, width, height from ".$types[$type]." where id >= $rand limit ".($size * 2));
				while ($row = mysql_fetch_array($result)) {
						if (count($ret) >= $size) break;
						if (!isset($row['enabled']) || $row['enabled'] == 0 || $row['height'] > 400) continue;
						$ret[$row['id']] = array($row['id'], $row['url'], $row['width'], $row['height']);
				}
		}
		//*/
		return $ret;
}
function human_filesize($bytes, $decimals = 2) {
	$size = array('B','kB','MB','GB','TB','PB','EB','ZB','YB');
	$factor = floor((strlen($bytes) - 1) / 3);
	return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
}

function show_error($message) {
	$html = '<div class="row"><div class="col-md-10 col-md-offset-1 col-xs-10">';
	$html .= '<div class="alert alert-danger" role="alert">'.$message.'</div>';
	$html .= '</div></div>';
	return $html;
}

function duoshuo_html($site_id, $thread_id, $title, $url) {
	$site_id = 'jav321';
	$html = "<div class=\"ds-thread\" data-thread-key=\"$thread_id\" data-title=\"$title\" data-url=\"https://www.jav321.com/$url\"></div>
	<script type=\"text/javascript\">
	var duoshuoQuery = {short_name:\"$site_id\"};
		(function() {
		 var ds = document.createElement('script');
		 ds.type = 'text/javascript';ds.async = true;
		 ds.src = (document.location.protocol == 'https:' ? 'https:' : 'http:') + '//static.duoshuo.com/embed.js';
		 ds.charset = 'UTF-8';
		 (document.getElementsByTagName('head')[0] 
		  || document.getElementsByTagName('body')[0]).appendChild(ds);
		 })();
	</script>'";
	return $html;
}

function start_with($s1, $s2) {
	return strpos($s1, $s2) === 0;
}

function get_inter_link() {
	return '<p class="text-center"><a href="https://www.ezsmth.com/">水木清华社区</a> <a href="http://www.ucptt.com/">ptt</a> <a href="https://www.jav321.com/">jav321</a> <a href="https://www.duanzh.com/">短知乎</a> <a href="https://www.ezwxc.com/">文学城</a></p>';
}

function get_popup_script($url) {
	return '<script type="text/javascript">
//			window.onclick = function() {
					window.open("'.$url.'");
//			}
	</script>';
}

function close_div($content) {
	$div_open_count = substr_count($content, '<div');
	$div_close_count = substr_count($content, '</div>');
	for ($i = $div_close_count; $i < $div_open_count; ++$i) {
		$content .= '</div>';
	}
	return $content;
}
?>

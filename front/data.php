<?
$img_board['Picture'] = "贴图";
$img_board['贴图'] = "Picture";
$img_board['MyPhoto'] = "个人Show";
$img_board['个人Show'] = "MyPhoto";
require_once("config.php");
require_once('functions.php');

function conn_db() {
	global $db_server, $db_user, $db_password, $database;
	$db_conn = mysql_pconnect($db_server, $db_user, $db_password);
	mysql_select_db($database, $db_conn);
	mysql_query("set names utf8");
	mysql_query("SET time_zone = '+8:00'");
	return $db_conn;
}

function conn_ezptt_db() {
	global $db_server, $db_user, $db_password, $ezptt_database;
	$db_conn = mysql_pconnect($db_server, $db_user, $db_password);
	mysql_select_db($ezptt_database, $db_conn);
	mysql_query("set names utf8");
	mysql_query("SET time_zone = '+8:00'");
	return $db_conn;
}

function conn_ck101_db() {
	global $db_server, $db_user, $db_password, $ck101_database;
	$db_conn = mysql_pconnect($db_server, $db_user, $db_password);
	mysql_select_db($ck101_database, $db_conn);
	mysql_query("set names utf8");
	mysql_query("SET time_zone = '+8:00'");
	return $db_conn;
}

function conn_reddit_db() {
	global $db_server, $db_user, $db_password, $ck101_database;
	$db_conn = mysql_pconnect($db_server, $db_user, $db_password);
	mysql_select_db('reddit', $db_conn);
	mysql_query("set names utf8");
	return $db_conn;
}

function conn_dmm_db() {
	global $db_server, $db_user, $db_password;
	$db_conn = mysql_pconnect($db_server, $db_user, $db_password);
	mysql_select_db('dmm', $db_conn);
	mysql_query("set names utf8");
	return $db_conn;
}

function conn_ads_db() {
	global $db_server, $db_user, $db_password;
	$db_conn = mysql_pconnect($db_server, $db_user, $db_password);
	mysql_select_db('ads', $db_conn);
	mysql_query("set names utf8");
	return $db_conn;
}

function get_attachments($bid, $aid, $author, $pub_time) {
	$db_conn = conn_db();
//	$sql = "select att_id, file_name from attachment where bid = $bid and aid = $aid and author = '$author' and $pub_time <= ts and date_add('$pub_time', interval 1, day) > ts";
	$sql = "select att_id, file_name from attachment where bid = $bid and aid = $aid and author = '$author'";
	$result = mysql_query($sql);
	$attachments = array();
	while($row = mysql_fetch_array($result)) {
		$suffix = substr($row['file_name'], strrpos($row['file_name'], ".") + 1);
		$attachment = array($row['att_id'], $row['file_name'], "$bid.$aid.".$row['att_id'].".$suffix");
		array_push($attachments, $attachment);
	}
#	mysql_close($db_conn);
	return $attachments;
}

function update_user_info($user, $password) {
	$db_conn = conn_db();
	$sql = "select count(*) from `user` where uid = '$user'";
	$result = mysql_query($sql);
	$row = mysql_fetch_array($result);
	if ($row[0] == 0) {
		$sql = "insert into `user` (uid, password) values('$user', '$password')";
		$result = mysql_query($sql);
	}
	else {
		$sql = "update `user` set password = '$password' where uid = '$user'";
		$result = mysql_query($sql);
	}
	mysql_close($db_conn);
}

function get_dataset($file_name) {
/*
	$memcache = new memcache();
	global $memcache_ip, $memcache_port;
//	if ($memcache->addServer($memcache_ip, $memcache_port)) {
	if ($memcache->connect('127.0.0.1', 11211)) {
//		$begin_tick = get_msec();
		$dataset = $memcache->get($file_name);
//		error_log("get $file_name ".(get_msec() - $begin_tick));
		if ($dataset !== false) {
			$memcache->close();
			return $dataset;
		}
	}
	else {
		$memcache = false;
	}
*/
	if (!file_exists($file_name)) return array();
	$fp_dataset = fopen($file_name,"r");
	if (!$fp_dataset) return array();
	$dataset = array();
	while (!feof($fp_dataset)) {
		$line = chop(fgets($fp_dataset));
		if (strlen($line) < 2) continue;
		$arr = explode("\t", $line);
		$dataset[] = $arr;
	}
/*
	if (strpos($file_name, 'all_topics') === false && count($dataset) > 0) {
		$memcache->set($file_name, $dataset);
	}
	$memcache->close();
*/
	return $dataset;
}

function guestbook_insert($user, $email, $content) {
	$user = addslashes($user);
	$email = addslashes($email);
	$content = addslashes($content);
	$ip = $_SERVER['REMOTE_ADDR'];
	$sql = "insert into guestbook(`uid`, email, content, ip) values('".addslashes($user)."', '".addslashes($email)."', '".addslashes($content)."', '$ip')";
	$db_conn = conn_db();
	$result = mysql_query($sql);
	if (!$result) {
		echo mysql_error()."<br>";
	}
	mysql_close($db_conn);
}

function get_board_list() {
/*
	$memcache = new memcache;
	if ($memcache->connect('127.0.0.1', 11211)) {
		$boards = $memcache->get('board_list');
		if ($boards !== false) {
			$memcache->close();
			return $boards;
		}
	}
	else {
		$memcache = false;
	}
*/
	$file_name = "snippets/热门话题.all";
	$fp_articles = fopen($file_name,"r") or exit("Unable to open $file_name");
	$boards = array();
	while (!feof($fp_articles)) {
		$line = fgets($fp_articles);
		chop($line);
		if (strlen($line) < 2) continue;
		$arr = explode("\t", $line);
		if (count($arr) < 2) continue;
		$boards[$arr[0]] = $arr[1];
	}
/*
	if ($memcache) {
		$memcache->set('board_list', $boards);
		$memcache->close();
	}
*/
	return $boards;
}

function get_select_post_board_html() {
	$boards = get_board_list();
	$html = '选择讨论区<select name="post_board_name" id="post_board_name" onchange="select_post_board();">';
	$selected_board_en_name = "";
	$selected_board_cn_name = "";
	while (list($board_en_name, $board_cn_name) = each($boards)) {
		if ($selected_board_en_name == "") {
			$selected_board_en_name = $board_en_name;
			$selected_board_cn_name = $board_cn_name;
		}
		$selected = "";
		if (isset($_GET['board_cn_name']) && $board_cn_name == $_GET['board_cn_name']) {
			$selected = ' selected="selected"';
			$selected_board_en_name = $board_en_name;
			$selected_board_cn_name = $board_cn_name;
		}
		$html .= "<option value=\"$board_en_name ".$board_cn_name."\"$selected>".substr($board_en_name, 0, 1)." $board_cn_name</option>";
	}
	$html .= "</select>";
	return array($html, $selected_board_cn_name, $selected_board_en_name);
}

function get_select_board_html() {
/*
	$boards = get_board_list();
	ksort($boards);
	$html = '<table><tr><td><form id="select_board" action="select_board.php" method="GET" target="_self"><select name="board_name" id="btsmth_board_name" onchange="document.getElementById(\'select_board\').submit()"><option>选择讨论区</option>';
	while (list($board_en_name, $board_cn_name) = each($boards)) {
		$selected = '';
		$html .= "<option value=\"$board_en_name\"$selected>".substr($board_en_name, 0, 1)." $board_cn_name</option>";
	}
	$html .= "</select>";
	$html .= '</form></td><td><form action="select_board.php" method="GET" target="_self">或者直接输入<input type="text" name="board_name" value="" size="12"><input type="submit" value="go"></form></td></tr></table>';
*/
	$html = '<form id="select_board" action="select_board.php" method="GET" target="_self"><a href="board_list.php">选择讨论区</a>或者直接输入<input type="text" name="board_name" value="" size="12"><input type="submit" value="go"></form>';
	return $html;
}

function get_select_all_board_html() {
	$dataset = get_dataset('data/boards');
	$boards = array();
	for ($i = 0; $i < count($dataset); ++$i) {
		$boards[$dataset[$i][0]] = $dataset[$i][1];
	}
	ksort($boards, SORT_STRING);
	//print_r($boards);
	$html = '<table><tr><td><form id="select_board" action="select_board.php" method="GET" target="_self"><select name="board_name" id="btsmth_board_name" onchange="document.getElementById(\'select_board\').submit()"><option>选择讨论区</option>';
	while (list($board_en_name, $board_cn_name) = each($boards)) {
		$html .= "<option value=\"$board_en_name\">".substr($board_en_name, 0, 1)." $board_cn_name</option>";
	}
	$html .= '</select></form></td><td><form action="select_board.php" method="GET" target="_self">或者直接输入<input type="text" name="board_name" value="" size="12"><input type="submit" value="go"></form></td></tr></table>';
	return $html;
}
/*
function get_hour_dropdownlist ($hour) {
	$html = '<form target="_self">查看<select id="select_hour" onchange="location.href=document.getElementById(\'select_hour\').value">';
	for ($h = 3; $h <= 24; $h *= 2) {
		if ($h == $hour) {
			$html .= "<option selected=\"selected\">$h"."小时热点</option>";
		}
		else {
			$url = $_SERVER['PHP_SELF']."?hour=$h";
			reset($_GET);
			while (list($key, $value) = each($_GET)) {
				if ($key != "hour") {
					$url .= "&$key=".urlencode($value);
				}
			}
			$html .= "<option value=\"$url\">$h"."小时热点</option>";
		}
	}
	$html .= "</select></form>";
	return $html;
}
*/

function get_all_topics() {
/*
	$memcache = new memcache;
	if ($memcache->connect('127.0.0.1', 11211)) {
		$topics = $memcache->get('all_topics');
		if ($topics !== false) {
			$memcache->close();
			return $topics;
		}
	}
	else {
		$memcache = false;
	}
*/
	$topics = get_dataset("snippets/热门话题.all");
/*
	if ($memcache) {
		$memcache->set('all_topics', $topics);
		$memcache->close();
	}
*/
	return $topics;
}

function get_prev_next_article($board_cn_name, $gid) {
	$articles = array();
	$board = array();
/*
	global $memcache_ip, $memcache_port;
	$memcache = new memcache;
	if ($memcache->connect($memcache_ip, $memcache_port)) {
		$board = $memcache->get($board_cn_name);
		if ($board === false) {
			$board = array();
			$all_topics = get_dataset('snippets/热门话题.all');
			foreach ($all_topics as $topic) {
				if ($topic[1] == $board_cn_name) {
					array_push($board, $topic);
				}
			}
			$memcache->set($board_cn_name, $board);
		}
		$memcache->close();
	}
*/
	$all_topics = get_dataset('snippets/热门话题.all');
	foreach ($all_topics as $topic) {
		if ($topic[1] == $board_cn_name) {
			array_push($board, $topic);
		}
	}
	for ($i = 0; $i < count($board); ++$i) {
		if ($gid == $board[$i][2]) {
			for ($j = $i + 1; $j  < count($board); ++$j) {
				if ($board_cn_name == $board[$j][1]) {
					$next_article = array();
					$next_article['board_en_name'] = $board[$j][0];
					$next_article['gid'] = $board[$j][2];
					$next_article['title'] = $board[$j][4];
					if (count($articles) == 0) {
						array_push($articles, array());
					}
					array_push($articles, $next_article);
					if (count($articles) >= 6) {
						break;
					}
				}
			}
			break;
		}
		else {
			$prev_article = array();
			$prev_article['board_en_name'] = $board[$i][0];
			$prev_article['gid'] = $board[$i][2];
			$prev_article['title'] = $board[$i][4];
			$articles[0] = $prev_article;
		}
	}
	if (count($articles) == 1) {
		$articles[0] = array();
	}
	while (count($articles) < 6) {
		array_push($articles, array());
	}
	return $articles;
}

function get_random_description() {
	$descs = array("水木社区", "水木清华", "newsmth", "水木清华bbs", "清华大学论坛", "新水木");
	return $descs[rand(0, 5)];
}

function record_post($board_en_name, $user, $title, $content, $promote) {
	$gid = $_POST['gid'];
	$sql = "insert into post(board_en_name, gid, `user`, title, content, promote, post_time) values('$board_en_name', $gid, '$user', '".addslashes($title)."', '".addslashes($content)."', $promote, CURRENT_TIMESTAMP)";
	$db_conn = conn_db();
	$result = mysql_query($sql);
	if (!$result) {
		echo mysql_error()."<br>";
	}
	mysql_close($db_conn);
}

function record_error($db_conn, $message = "") {
	$url = $_SERVER["REQUEST_URI"];
	if (isset($_SERVER['HTTP_USER_AGENT'])) {
		$url .= ' '.$_SERVER['HTTP_USER_AGENT'];
	}
	$post = "";
	while (list($key, $value) = each($_POST)) {
		$post .= "$key\t$value\n";
	}
	$sql = "insert delayed into error_log(error_time, url, post, message) values(CURRENT_TIMESTAMP, '$url', '".addslashes($post)."', '".addslashes($message)."')";
//	$db_conn = conn_db();
	$result = mysql_query($sql);
	if (!$result) {
		echo mysql_error()."<br>";
	}
//	mysql_close($db_conn);
}

function is_duplication(&$hash, $key) {
	if (isset($hash[$key])) {
		return true;
	}
	$hash[$key] = 0;
	return false;
}

function get_gif() {
	$GIF_DATA = array(
			chr(0x47), chr(0x49), chr(0x46), chr(0x38), chr(0x39), chr(0x61),
			chr(0x01), chr(0x00), chr(0x01), chr(0x00), chr(0x80), chr(0xff),
			chr(0x00), chr(0xff), chr(0xff), chr(0xff), chr(0x00), chr(0x00),
			chr(0x00), chr(0x2c), chr(0x00), chr(0x00), chr(0x00), chr(0x00),
			chr(0x01), chr(0x00), chr(0x01), chr(0x00), chr(0x00), chr(0x02),
			chr(0x02), chr(0x44), chr(0x01), chr(0x00), chr(0x3b)
			);
	return join($GIF_DATA);
}

function is_from_search_engine() {
	if (!isset($_SERVER['HTTP_REFERER'])) {
		return false;
	}
	$referer = $_SERVER['HTTP_REFERER'];
	$search_engines = array('.google.', '.baidu.com', '.sogou.com', 's.maxthon.com', 'web.gougou.com', 'g.firebird.cn', '.so.com', '.sm.cn', 'baidu.mobi');
	foreach ($search_engines as $se) {
		if (strpos($referer, $se) > 0) {
			return true;
		}
	}
	return false;
}

function is_from_cn_search_engine() {
	if (!isset($_SERVER['HTTP_REFERER'])) {
		return false;
	}
	$referer = $_SERVER['HTTP_REFERER'];
	$search_engines = array('.baidu.com', '.sogou.com', 's.maxthon.com', 'web.gougou.com', 'g.firebird.cn', '.so.com', '.sm.cn', 'baidu.mobi');
	foreach ($search_engines as $se) {
		if (strpos($referer, $se) > 0) {
			return true;
		}
	}
	return false;
}

function request2sql($get, $content = "*") {
	$select = '';
	if ($content == '*') {
		$select = 'select snapshot.bid as bid, snapshot.gid, snapshot.aid as aid, snapshot.author as author, nick, title, pub_time';
	}
	else {
		$select = "select $content";
	}
	$use_idx = 'force index(author_pubtime_index)';
	$condition = 'where 1';
	$additional_table = '';
	$groupby = '';
	$orderby = '';
	if (isset($get['author'])) {
		$condition .= " and snapshot.author = '".$get['author']."'";
	}
	if (isset($get['title'])) {
		$condition .= " and title like '%".urldecode($get['title'])."%'";
	}
	if ((isset($get['topic_only']) && $get['topic_only'] == "1") || is_spider()) {
		$condition .= " and is_topic = 1";
		$use_idx = 'force index(author_istopic_pubtime_index)';
	}
	if (isset($get['bid']) && $get['bid'] != '' && $get['bid'] != 'all') {
		$condition .= " and snapshot.bid = ".$get['bid'];
		$use_idx = 'force index(author_bid_istopic_pubtime_index)';
	}
	if (isset($get['attachment']) && $get['attachment'] == '1') {
		$additional_table = ', attachment force index(author_bid_index)';
		$use_idx = 'force index(bid_gid_istopic_index)';
		$condition .= " and snapshot.bid = attachment.bid and snapshot.aid = attachment.aid and (snapshot.gid = attachment.gid) and attachment.author = '".$get['author']."'";
//		$condition .= " and snapshot.bid = attachment.bid and snapshot.aid = attachment.aid and snapshot.author = attachment.author";
//		$use_idx = 'force index(author_bid_istopic_pubtime_index)';
		if (strpos($content, 'count(') !== false) {
			$select = 'select count(distinct(attachment.aid))';
		}
		else {
			$groupby = 'group by attachment.bid, attachment.aid';
		}
	}
	if (strpos($content, 'count(') === false) {
		$orderby = " order by pub_time desc";
	}
	$sql = "$select from snapshot $use_idx $additional_table $condition $groupby $orderby";
	if ($content == "*") {
		$page_size = 40;
		if (is_spider()) {
			$sql .= ' limit 40';
		}
		else {
			if (isset($get['pn'])) {
				$sql .= " limit ".(($get['pn'] - 1) * $page_size).", 40";
			}
			else {
				$sql .= " limit 0, 40";
			}
		}
	}
	return $sql;
}

function modify_request($modify_key, $modify_value) {
	$request = "";
	$modified = false;
	reset($_GET);
	while (list($key, $value) = each($_GET)) {
		if ($key == $modify_key) {
			if ($key != 'pn' || $modify_value != 1) {
				$request .= "$modify_key=$modify_value&";
			}
			$modified = true;
		}
		else {
			$request .= "$key=".urlencode($value).'&';
		}
	}
	if (!$modified) {
		$request .= "$modify_key=$modify_value";
	}
	if (strrpos($request, '&') == strlen($request) - 1) {
		$request = substr($request, 0, strlen($request) - 1);
	}
	return $request;
}

function show_query_result(&$nick_out = '') {
	global $head_row_bg_color;
	$page_size = 40;
	$is_spider = is_spider();
	if ($is_spider) {
		if (isset($_GET['pn']) && $_GET['pn'] != 1) {
			return '';
		}
		$page_size = 4000000;	//无穷大
	}
	$db_conn = conn_db();
	$result_num = 40;
	if (!$is_spider) {
		$sql = request2sql($_GET, 'count(snapshot.author)');
		$result = mysql_query($sql);
		$row = mysql_fetch_array($result);
		$result_num = $row[0];
	}
	$sql = request2sql($_GET);
	if (strstr($sql, ' and ') === false) {
		return '';
	}
//	print "<!-- $sql -->";
	$result = mysql_query($sql);
//	$result_num = mysql_num_rows($result);
	if (isset($_GET['pn'])) {
		$result_num += $page_size * ($_GET['pn'] - 1);
	}

	if ($result_num > 0) {
		$html = '<table align="center" width="90%"><tr><td align="center" valign="top"><img src="pie_chart.php?author='.$_GET['author'].'"></td><td valign="top">';
		$html .= '<table align="center" width="100%"><tr><td colspan="4" align="center">';
		$banner_ad = load_file("baidu.960_90");
		$html .= $banner_ad;
		$html .='</td></tr><tr style="margin: 0 24px 0 0; background:'.$head_row_bg_color.';padding:0 0 0 15px;"><td colspan="4"><strong>共'.$result_num.'个帖子</strong></td></tr>';
		$result_count = 0;
		$board_map = get_board_map();
		while($row = mysql_fetch_array($result)) {
			$bid = $row['bid'];
			$gid = $row['gid'];
			$aid = $row['aid'];
			$nick = $row['nick'];
			$author = $row['author'];
			$title = substr(str2html($row['title']), 4);

			$pub_time = $row['pub_time'];
			$board_cn_name = $board_map[$bid][1];
			$board_en_name = $board_map[$bid][0];
			$html .= '<tr onmouseover="this.style.backgroundColor=\'#FEF3CE\'" onmouseout="this.style.backgroundColor=\'\'"><td>[<a href="show_all.php?board_cn_name='.urlencode($board_cn_name)."\" onclick=\"pingback(this)\">$board_cn_name</a>]</td><td><a href=\"show_topic.php?en_name=$board_en_name&gid=$gid#${author}_$aid\" onclick=\"pingback(this)\">$title</a><td>";
			if (true || isset($_GET['author'])) {
				$html .= "$author (".stripslashes($nick).")";
			}
			else {
				$html .= "<a href=\"query.php?author=$author&topic_only=1\" target=\"_self\" onclick=\"pingback(this)\">$author (".stripslashes($nick).")</a>";
			}
			$html .= "</td><td>$pub_time</td></tr>";
			if (!isset($nick_out) || $nick_out == '') {
				$nick_out = $nick;
			}
			if (++$result_count >= $page_size) {
				break;
			}
		}
		if ($result_num > $page_size) {
			$pn = 1;
			if (isset($_GET['pn'])) {
				$pn = $_GET['pn'];
			}
			$total_page = ceil($result_num / 40);
			$html .= '<tr><td align="left" colspan="2">&nbsp;';
			if ($pn > 1) {
				$html .= '<a href="'.$_SERVER['SCRIPT_NAME'].'?'.modify_request('pn', $pn - 1).'" target="_self" onclick="pingback(this)">上一页</a>';
			}
			$html .= '</td><td align="right" colspan="2">';
			if ($pn < $total_page) {
				$html .= '<a href="'.$_SERVER['SCRIPT_NAME'].'?'.modify_request('pn', $pn + 1).'" target="_self" onclick="pingback(this)">下一页</a>';
			}
			$html .= '</td></tr>';
		}
		$html .= '</table></td></tr></table>';
	}
	else {
		$html .= '<p align="center">抱歉，没有找到符合条件的帖子。';
#		$html .= load_file("adsense_for_search.htm");
	}
	mysql_close($db_conn);
	return $html;
}

function record_query($query_string) {
	$sql = "insert into `query`(query_time, query_string) values(CURRENT_TIMESTAMP, '$query_string')";
	$db_conn = conn_db();
	mysql_query($sql);
	mysql_close($db_conn);
}

function load_file($file) {
	$fp = fopen($file,"r") or exit("Unable to open $file");
	$content = '';
	while (!feof($fp)) {
		$line = fgets($fp);
		$content .= $line;
	}
	fclose($fp);
	return $content;
}

$picture_bid_old = 382;
$picture_bid_new = 1349;

function get_random_articles_backup($file_name, $article_num = 10) {
	$memcache = new memcache();
	global $memcache_ip, $memcache_port, $picture_bid_old, $picture_bid_new;
	if ($memcache->connect($memcache_ip, $memcache_port)) {
		$set_size = $memcache->get("${file_name}_size");
		if ($set_size === false) {
			if (!file_exists($file_name)) return array();
			$fp_dataset = fopen($file_name, 'r');
			if (!$fp_dataset) return array();
			$dataset = array();
			while (!feof($fp_dataset)) {
				$line = chop(fgets($fp_dataset));
				if (strlen($line) < 2) continue;
				if (strpos($line, "\t") === false) {
					$dataset[] = $line;
				}
				else {
					$arr = explode("\t", $line);
					if ($arr[0] == $picture_bid_old) {
						$arr[0] = $picture_bid_new;
//						error_log('new bid '.$arr[0]);
					}
//					if ($file_name == 'data/accumulated_pic_articles') {
//						error_log('cache '.$picture_bid_old.' '.$arr[0].' '.$arr[1].' '.$arr[2].' '.$arr[3].' '.$arr[4].' '.$arr[5]);
//					}
					$dataset[] = $arr;
				}
			}
			$set_size = count($dataset);
			$memcache->set($file_name.'_size', $set_size);
			for ($i = 0; $i < $set_size; ++$i) {
				$memcache->set(md5($file_name.'_'.$i), $dataset[$i]);
			}
		}
		$random_articles = array();
		$selected_idx = array();
		while (count($random_articles) < $article_num && count($random_articles) < $set_size) {
			$rnd_idx = rand(0, $set_size - 1);
			if (isset($selected_idx[$rnd_idx])) {
				continue;
			}
			$selected_idx[$rnd_idx] = 0;
//			echo "<!-- rnd idx $rnd_idx -->";
			$record = $memcache->get(md5($file_name.'_'.$rnd_idx));
 			if ($record === false) continue;
			array_push($random_articles, $memcache->get(md5($file_name.'_'.$rnd_idx)));
		}
		return $random_articles;
	}
	return array();
}

function get_random_articles($file_name, $article_num = 10) {
	if (!file_exists($file_name)) return array();
	$fp_dataset = fopen($file_name, 'r');
	if (!$fp_dataset) return array();
	$dataset = array();
	while (!feof($fp_dataset)) {
		$line = chop(fgets($fp_dataset));
		if (strlen($line) < 2) continue;
		if (strpos($line, "\t") === false) {
			$dataset[] = $line;
		}
		else {
			$arr = explode("\t", $line);
			if ($arr[0] == $picture_bid_old) {
				$arr[0] = $picture_bid_new;
			}
			$dataset[] = $arr;
		}
	}
	$set_size = count($dataset);
	if ($set_size < $article_num) $article_num = $set_size;
	if ($article_num <= 1) return $dataset;
	$selected_indexes = array_rand($dataset, $article_num);
	$random_articles = array();
	foreach ($selected_indexes as $selected_index) {
		$random_articles[] = $dataset[$selected_index];
	}
	return $random_articles;
}

function get_random_pic_articles($article_num = 4) {
	$articles = get_dataset('data/accumulated_pic_articles');
	$random_pic_articles = array();
	$selected_idx = array();
	while (count($random_pic_articles) < $article_num && count($random_pic_articles) < count($articles)) {
		$rnd_idx = rand(0, count($articles) - 1);
		if (isset($selected_idx[$rnd_idx])) {
			continue;
		}
		$selected_idx[$rnd_idx] = 0;
		if ($articles[$rnd_idx][0] == $picture_bid_old) {
			$articles[$rnd_idx][0] = $picture_bid_new;
		}
		array_push($random_pic_articles, $articles[$rnd_idx]);
	}
	return $random_pic_articles;
}

function get_mime_array($mimePath = '../etc') 
{ 
	$regex = "/([\w\+\-\.\/]+)\t+([\w\s]+)/i"; 
	$lines = file("$mimePath/mime.types", FILE_IGNORE_NEW_LINES); 
	foreach($lines as $line) { 
		if (substr($line, 0, 1) == '#') continue; // skip comments 
		if (!preg_match($regex, $line, $matches)) continue; // skip mime types w/o any extensions 
		$mime = $matches[1]; 
		$extensions = explode(" ", $matches[2]); 
		foreach($extensions as $ext) $mimeArray[trim($ext)] = $mime; 
	} 
	return ($mimeArray); 
} 

function get_mime_type($filename, $mimePath = '/etc') { 
	$fileext = substr(strrchr($filename, '.'), 1); 
	if (empty($fileext)) return (false); 
	$regex = "/^([\w\+\-\.\/]+)\s+(\w+\s)*($fileext\s)/i"; 
	$lines = file("$mimePath/mime.types"); 
	foreach($lines as $line) { 
		if (substr($line, 0, 1) == '#') continue; // skip comments 
		$line = rtrim($line) . " "; 
		if (!preg_match($regex, $line, $matches)) continue; // no match to the extension 
		return ($matches[1]); 
	} 
	return (false); // no match at all 
}

function get_mime_type_simple($filename) {
	$slices = explode('.', $filename);
	$suffix = strtolower($slices[count($slices) - 1]);
	if ($suffix == 'jpg' || $suffix == 'jpeg' || $suffix == 'jpe') {
		return 'image/jpeg';
	}
	else if ($suffix == 'gif') {
		return 'image/gif';
	}
	else if ($suffix == 'png') {
		return 'image/png';
	}
	else if ($suffix == 'tif' || $suffix == 'tiff') {
		return 'image/tiff';
	}
	else if ($suffix == 'bmp') {
		return 'image/x-ms-bmp';
	}
	return '';
}

function empty_img_exit() {
	header("Content-Type: image/gif");
	echo get_gif();
	exit;
}

function get_board_info($in, $type) {
	if ($type > 1) {
		echo 'error get_board_info';
		return '';
	}
	$boards = get_dataset('data/boards');
	foreach ($boards as $board) {
		foreach ($board as $item) {
			if ($item == $in) {
				return $board[$type];
			}
		}
	}
	return '';
}

function get_history_daily_snippets() {
	$ret = array();
//*
	$memcache = new memcache();
	global $memcache_ip, $memcache_port;
	if ($memcache->connect($memcache_ip, $memcache_port)) {
		$ret = $memcache->get('daily_snapshot');
		if ($ret !== false) {
			$memcache->close();
			return $ret;
		}
	}
//*/
	$snippet_dirs = glob('history_snippets/201*');
	foreach ($snippet_dirs as $snippet) {
		if (strlen($snippet) != 27) continue;
		$snippet_time = substr($snippet, 17);
		seperate_date($snippet_time, $year, $month, $day);
		$year_set = &$ret[$year];
		if (!isset($year_set)) {
			$year_set = array($month => array($day => $snippet_time));
			$ret[$year] = &$year_set;
		}
		$month_set = &$year_set[$month];
		if (!isset($month_set)) {
			$month_set = array($day => $snippet_time);
			$year_set[$month] = &$month_set;
		}
		if (!isset($month_set[$day])) {
			$month_set[$day] = $snippet_time;
		}
		elseif ($snippet_time > $month_set[$day]) {
			$month_set[$day] = $snippet_time;
		}
	}
	$memcache->set('daily_snapshot', $ret);
	$memcache->close();
	return $ret;
}

function get_random_history_daily_snippets() {
	$snippets = get_history_daily_snippets();
	$year = array_rand($snippets);
	$month = array_rand($snippets[$year]);
	$day = array_rand($snippets[$year][$month]);
	$snippet_time = $snippets[$year][$month][$day];
//	echo "$year $month $day $snippet_time";
	return $snippet_time;
}

function seperate_date($date, &$year, &$month, &$day) {
	$year = substr($date, 0, 4);
	$month = substr($date, 4, 2);
	if (strpos($month, '0') === 0) {
		$month = substr($month, 1);
	}
	$day = substr($date, 6, 2);
	if (strpos($day, '0') === 0) {
		$day = substr($day, 1);
	}
}

function get_all_board_dropdown_menu_html() {
	$board_set = get_dataset('data/boards');
	$boards = array();
	foreach ($board_set as $board) {
		$boards[$board[0]] = array($board[1], $board[2]);
	}
	ksort($boards);
	$html = '<select name="bid"><option value="all">所有版面</option>';
	while (list($board_en_name, $board_info) = each($boards)) {
		$board_cn_name = $board_info[0];
		$bid = $board_info[1];
		$selected = '';
		if (isset($_GET['bid']) && $_GET['bid'] == $bid) {
			$selected = ' selected';
		}
		$html .= "<option value=\"$bid\"$selected>".substr($board_en_name, 0, 1).' '.$board_cn_name.'</option>';
	}
	$html .= '</select>';
	return $html;
}

function get_board_map() {
//*
	$memcache = new memcache();
	global $memcache_ip, $memcache_port;
	if ($memcache->connect('127.0.0.1', 11211)) {
//	if ($memcache->addServer($memcache_ip, $memcache_port)) {
		$board_map = $memcache->get('board_map');
		if ($board_map !== false) {
			$memcache->close();
//			echo '<!--';
//			print_r($board_map);
//			echo '-->';
			return $board_map;
		}
	}
//*/
	$db_conn = conn_db();
	$sql = 'select bid, en_name, cn_name from board';
	$result = mysql_query($sql);
	$board_map = array();
	while($row = mysql_fetch_array($result)) {
		$board = array($row['en_name'], $row['cn_name'], $row['bid']);
		$board_map[$row['en_name']] = $board;
		$board_map[$row['bid']] = $board;
		$board_map[$row['cn_name']] = $board;
	}
//*
	if (count($board_map) > 0) {
		$memcache->set('board_map', $board_map);
	}
	$memcache->close();
//*/
	return $board_map;

}

function contain_chn($str) {
	$len = strlen($str);
	for ($i = 0; $i < $len; ++$i) {
		if (ord(substr($str, $i)) > 127) {
			return true;
		}
	}
	return false;
}

function get_custom_query($se_query_file) {
	$query_num = 20;
	$rand = get_random_articles("../se_query/$se_query_file", 20);
	if (count($rand) == 0) return '';
	$html = '';
	$queries = array();
	$len = 0;
	foreach ($rand as $r) {
		if (isset($queries[$r])) continue;
		$html .= '<a href="http://www.baidu.com/s?wd='.urlencode($r).'+site%3Awww.btsmth.com">'.$r.'</a> &nbsp; ';
#		$html .= '<a href="http://www.google.com.hk/custom?cx=partner-pub-1529335294283480%3Axu5pk1-316l&ie=GB2312&q='.urlencode($r).'&sa=%C8%AB%CE%C4%CB%D1%CB%F7&adkw=AELymgXo12RUGO381rKvG1mnVAHYW9zfdvIKCE0E-vGzcPysy4dlhMhLDh1RoAukYaC8NmGgqe4Js1HeVh7jAtbH4UYyc4BJAlAnI-SfzJusDlR9xjXlKTE&cof=AH%3Aleft%3BALC%3A0000FF%3BBGC%3AFFFFFF%3BCX%3Abtsmth%25E5%2585%25A8%25E6%2596%2587%25E6%2590%259C%25E7%25B4%25A2%3BDIV%3A336699%3BFORID%3A13%3BGALT%3A008000%3BL%3Ahttp%3A%2F%2Fwww.google.com%2Fintl%2Fzh-CN%2Fimages%2Flogos%2Fcustom_search_logo_sm.gif%3BLC%3A0000FF%3BLH%3A30%3BLP%3A1%3BT%3A000000%3BVLC%3A663399%3B&hl=zh-CN&oe=GB2312&safe=images&client=pub-1529335294283480&channel=8455189153&boostcse=0&siteurl=www.btsmth.org/">'.$r.'</a> &nbsp; ';
		$queries[$r] = 0;
		$len += strlen($r) + 1;
		if ($len > 180) break;
	}
	return $html;
}

function get_boards_table($boards_en_name) {
	$boards = array();
	$max_board_num = 10;
	foreach ($boards_en_name as $en_name) {
		if ($en_name == 'Picture') {
			continue;
		}
		$boards[$en_name] = array();
		if (--$max_board_num <= 0) break;
#		echo "$en_name<br>";
	}
	$board_size = 2;
	$found_num = 0;
	$target_num = $board_size * count($boards_en_name);
	$hot_topics = get_dataset('snippets/热门话题.all');
	if ($hot_topics === false) return '';
	foreach ($hot_topics as $topic) {
		if (!isset($boards[$topic[0]])) continue;
#		echo "$topic[0]<br>";
		$board = $boards[$topic[0]];
		if (count($board) < $board_size) {
			array_push($board, $topic);
			$boards[$topic[0]] = $board;
			if (++$found_num >= $target_num) {
				break;
			}
		}
	}
#	print_r($boards);
	global $head_row_bg_color;
	global $js;
	global $tr_idx;
	global $article_count;
	$html = "<tr style=\"margin: 0 24px 0 0; background:$head_row_bg_color;padding:0 0 0 15px;\"><td><strong>my btsmth</strong></td><td colspan=\"2\"><a href=\"introduction.php#my_btsmth\" title=\"my btsmth是什么？\" onclick=\"pingback(this)\"><img src=\"image/tip.png\" border=\"0\"></a></td></tr>";
	$prev_board_en_name = '';
	foreach ($boards_en_name as $en_name) {
		if (!isset($boards[$en_name])) continue;
		$board = $boards[$en_name];
		foreach ($board as $article) {
			$board_en_name = $article[0];
			$board_cn_name = $article[1];
			$gid = $article[2];
			$author = $article[3];
			$title = $article[4];
			if ($board_en_name != $prev_board_en_name) {
				$js .= "board_len['$board_en_name'] = 1;";
				$tr_id = $tr_idx;
				++$tr_idx;
			}
			else {
				$js .= "++board_len['$board_en_name'];";
				$tr_id = '';
			}
			$html .= '<tr id="'.$tr_id.'" onmouseover="this.style.backgroundColor=\'#FEF3CE\'" onmouseout="this.style.backgroundColor=\'\'"><td>';
			if ($board_en_name == $prev_board_en_name) {
				$html .= "&nbsp;";
			}
			else {
				$url = '';
				if (isset($img_board[$board_en_name])) {
					$url = "show_all_pic_articles.php?board_en_name=".$board_en_name;
				}
				else {
					$url = 'show_all.php?board_cn_name='.urlencode($board_cn_name);
				}
				$html .= "[<a href=\"$url\" onclick=\"pingback(this);\">$board_cn_name</a>]";
			}
			$html .= "</td><td><a href=\"show_topic.php?en_name=$board_en_name&gid=$gid#my_btsmth\" onclick=\"pingback(this); more_topics('$board_en_name', ".($tr_idx - 1).");\">".htmlspecialchars(stripslashes($title))."</a></td><td><a href=\"query.php?author=$author&topic_only=1\" onclick=\"pingback(this)\">$author</a></td></tr>";
			$prev_board_en_name = $board_en_name;
			++$article_count;
		}
	}
	$html .= "<tr><td colspan=\"3\">&nbsp;</td></tr>";
	return $html;
}

function echo_my_btsmth() {
	if (!isset($_COOKIE['uid'])) return;
	$uid = $_COOKIE['uid'];
//	echo "uid = $uid";
	$memcache = new memcache;
	global $memcache_ip, $memcache_port;
	if ($memcache->connect($memcache_ip, $memcache_port)) {
		$boards = $memcache->get($uid);
		if ($boards !== false)
			echo get_boards_table($boards);
	}
}

function get_author_board_activity($db_conn, $board_article_num, $bid, $author) {
	if ($board_article_num <= 0) return -1;
	$sql = "select count(bid) from snapshot force index (author_bid_istopic_pubtime_index) where author='$author' and bid = $bid";
	$result = mysql_query($sql, $db_conn);
	if ($result === false) {
		error_log("get_author_board_activity http://".$_SERVER ['HTTP_HOST'].$_SERVER['REQUEST_URI']);
	}
	while ($row = mysql_fetch_array($result)) {
		return substr(100 / (1 - log($row[0] / $board_article_num)), 0, 4);
	}
}

function get_follower($db_conn, $bid, $author) {
	$followers = array();
	$sql = "select gid from snapshot where author = '$author' and is_topic = 1 and bid = $bid order by pub_time desc limit 10";
	$result = mysql_query($sql, $db_conn);
	$gids = '';
	while ($row = mysql_fetch_array($result)) {
		$gids .= ', '.$row[0];
	}
	if (strlen($gids) == '') {
		return $followers;
	}
	else {
		$gids = substr($gids, 2);
	}
	$sql = "select author, count(*) as c from snapshot force index(bid_gid_istopic_index) where bid = $bid and gid in ($gids) and author <> '$author' group by author order by c desc";
	$result = mysql_query($sql, $db_conn);
	while ($row = mysql_fetch_array($result)) {
		array_push($followers, $row[0]);
		if (count($followers) >= 5) return $followers;
	}
	return $followers;
}

function get_title_author_cnname($en_name, $gid, $db_conn) {
	$result = mysql_query("select title, author, cn_name from board, snapshot where board.bid = snapshot.bid and en_name = '$en_name' and gid = $gid order by aid limit 1", $db_conn);
	while($row = mysql_fetch_array($result)) {
		return htmlspecialchars(stripslashes($row['title'])).' '.$row['author'].' '.$row['cn_name'];
	}
	return '';
}

function get_rand_dip_topics($num = 10) {
	global $dip_root_path;
	$dataset = get_dataset($dip_root_path.'/front/snippet/index');
	$selected_indexes = array_rand($dataset, $num);
	$ret = array();
	foreach ($selected_indexes as $selected_index) {
		array_push($ret, $dataset[$selected_index]);
	}
	return $ret;
}
?>

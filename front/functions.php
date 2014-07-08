<?
require_once("config.php");
//require_once("http.php");
require_once("data.php");
//require_once('ip_location.php');
if (!isset($_SESSION)) {
	session_start();
}
function str2html_backup($str) {
	$str = str_replace("&", "&amp;", $str);
	$str = str_replace("<", "&lt;", $str);
	$str = str_replace(">", "&gt;", $str);
	$str = str_replace("\\n", "<br>", $str);
	$str = str_replace('  ', " &nbsp;", $str);
	$str = preg_replace("/\\\\r[\\[\\d;]+m/", "", $str);
	$str = ereg_replace("\\x20\\x20", "&nbsp;", $str);
	$str = stripslashes($str);
	$str = preg_replace("/<br>: \.\.\.\.\.\.\.\.\.\.[\d\D]*/", "", $str);
	$str = preg_replace("/br>: </", "", $str);
	$str = preg_replace("/br>: ([\d\D]*?)</", "br><font color=\"gray\">: \\1</font><", $str, 1);
	$str = preg_replace("/<br>: [\d\D]*/", "", $str);
	while (strstr($str, "<br><br>")) {
		$str = preg_replace("/<br><br>/", "<br>", $str);
	}
	return $str;
}

function str2html($str) {
	global $img_host;
	$str = str_replace("&", "&amp;", $str);
	$str = str_replace("<", "&lt;", $str);
	$str = str_replace(">", "&gt;", $str);
	$str = preg_replace("/\\\\r[\\[\\d;]+m/", "", $str);
	$str = str_replace('  ', " &nbsp;", $str);
	$lines = explode("\\n", $str);
	$ret = '';
	$flag = true;
	for ($i = 0; $i < count($lines); ++$i) {
		$line = $lines[$i];
		if (strstr($line, ': 标 &nbsp;题') !== false) continue;
		if (strstr($line, ': 发信站') !== false) continue;
		if (strstr($line, ': 发信人') !== false) continue;
		if (strstr($line, ': 【 以下文字转载自') !== false) continue;
		if (strstr($line, ": ..........") !== false) continue;
		if (trim($line) == '') continue;
		if (strpos($line, ': ') === 0) {
			if ($flag) $line = "<font color=\"gray\">$line</font>";
			else continue;
			$flag = false;
		}
		$line = stripslashes($line);
		$ret .= "<br>$line";
	}
	//$ret = substr($ret, 4);
	$ret = preg_replace('/\[em([a-z]*)(\d+)\]/', "<img src=\"$img_host/image/em\${1}/\${2}.gif\">", $ret);
	return $ret;
	$str = str_replace("\\n", "<br>", $str);
	$str = stripslashes($str);
	$str = preg_replace("/br>: </", "", $str);
	$str = preg_replace("/br>: ([\d\D]*?)</", "br><font color=\"gray\">: \\1</font><", $str, 1);
	$str = preg_replace("/<br>: [\d\D]*/", "", $str);
	while (strstr($str, "<br><br>")) {
		$str = preg_replace("/<br><br>/", "<br>", $str);
	}
	return $str;
}

function is_spider() {
	if (!isset($_SERVER['HTTP_USER_AGENT'])) return false;
	$ua = $_SERVER['HTTP_USER_AGENT'];
	if (strpos($ua, 'Baiduspider') === false && strpos($ua, 'Googlebot') === false && strpos($ua, 'baidu Transcoder') === false && strpos($ua, 'msnbot') === false && strpos($ua, 'Sogou') === false && strpos($ua, 'Sosospider') === false && strpos($ua, 'Yahoo!') === false && strpos($ua, 'Kmspider') === false && strpos($ua, 'Mediapartners-Google') === false && strpos($ua, 'YoudaoBot') === false && strpos($ua, '360Spider') === false && strpos($ua, 'bingbot') === false && strpos($ua, 'JikeSpider') === false && strpos($ua, 'EasouSpider') === false) {
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

function login_newsmth($user, $password) {
	if ($user == '' || $password == '') {
		return -3;
	}
	$user = trim(urlencode($user));
	$password = trim(urlencode($password));
//	$params = "id=$user&passwd=$password&kick_multi=1";
	$params = array('id' => $user, 'passwd' => $password, 'kick_multi' => '1', 'mode' => 1);
	list($response, $header, $cookie) = http_request('http://www.newsmth.net/bbslogin1203.php', 'POST', $params);
	//list($response, $header, $cookie) = http_request('http://www.zhuishubao.com/login_newsmth.php', 'GET', $params);
	//print_r($params);
	echo "<!--$response<br>$header<br>-->";
	//exit;
	$_SESSION['login_smth_tick'] = date('U');
	if (strpos($response, "window.location.href='frames.html';") || strpos($response, 'window.location = "index.html";') || strpos($response, "window.location.href='frames.html?mainurl=bbsnew.php'")) {	//
		$_SESSION['user'] = $user;
		$_SESSION['password'] = $password;
		$_SESSION['smth_cookie'] = $cookie;
		return 1;
	}
	elseif (strpos($response, "用户密码错误，请重新登录！")) {
		return -1;
	}
	elseif (strpos($response, '登录过于频繁')) {
		return -2;
	}
	else {
		error_log ("<!--wydebug	login_newsmth	illegal login response: ".$user.' '.$password.' '.$response.'-->');
		return 0;
	}
//	if ()
#	$telnet = new PHPTelnet();
#	$telnet->show_connect_error = 0;
#	$telnet->Connect("newsmth.net", $user, $password);
#	$response = "";
#	$telnet->GetResponse($response);
	if (strpos($response, "本日十大衷心祝福") || strpos($response, "酸甜苦辣板")) {
		setcookie("uid", $user, time() + 3600 * 24 * 365 * 10);
		update_user_info($user, $password);
		$telnet->DoCommand("          g\n\n\n");
		return 0;
	}
	elseif (strpos($response, "错误的使用者代号")) {
		return 1;
	}
	elseif (strpos($response, "密码输入错误")) {
		return 2;
	}
	else {
		return -1;
	}
}

//echo "out of function: session_user = ".$_SESSION['user']."<br>";
function check_login() {	//如果已登录，返回true，否则重定向到登录页
//	if (strrpos($_SERVER['HTTP_USER_AGENT'], 'Baidu') || strrpos($_SERVER['HTTP_USER_AGENT'], 'Google') || strrpos($_SERVER['HTTP_USER_AGENT'], 'Yahoo') || strrpos($_SERVER['HTTP_USER_AGENT'], 'MSN') || strrpos($_SERVER['HTTP_USER_AGENT'], 'Sogou')) {	//允许搜索引擎抓取
//		return true;
//	}
	if (!isset($_SESSION['user'])) {
		$current_url = "http://".$_SERVER ['HTTP_HOST'].$_SERVER['REQUEST_URI']; 
		header("Location: login.php?ref=".urlencode($current_url));
		exit;
	}
	return true;
}

function http_request($url, $method='GET', $data=array(), $cookie = array(), $referer=''){
#	set_time_limit(0);
	$http = new http_class;
	$http->follow_redirect = 5;
	$http->GetRequestArguments($url,$arguments);
	$arguments['RequestMethod'] = $method;
	if (isset($cookie)) {
		$http->RestoreCookies($cookie);
	}
	if (count($data) > 0) {
		$arguments['PostValues'] = $data;
	}
	if (isset($referer)) {
		$arguments['Referer'] = $referer;
	}
	if (true || strpos($url, 'www.newsmth.net') >= 0) {
		$memcache = new memcache;
		global $memcache_ip, $memcache_port;
		if (true || $memcache->connect($memcache_ip, $memcache_port)) {
			//$proxies = $memcache->get('proxies');
			if (true || $proxies === false) $proxies = load_proxies();
			while (true) {
				$http->error = '';
				$http->state = 'Disconnected';
				if (true && count($proxies) < 10) {
					$proxies = load_proxies();
					//$proxies = $memcache->get('proxies');
					if ($proxies === false) {
						return array('', '', '');
					}
				}
				$proxy_idx = rand(0, count($proxies) - 1);
				if (isset($_SESSION['proxy'])) {
					$arguments["ProxyHostName"] = $_SESSION['proxy'][0];
					$arguments["ProxyHostPort"] = $_SESSION['proxy'][1];
				}
				else {
					$arguments["ProxyHostName"] = $proxies[$proxy_idx][0];
					$arguments["ProxyHostPort"] = $proxies[$proxy_idx][1];
				}
				$http->Open($arguments);
				$err = $http->SendRequest($arguments) . $http->ReadReplyHeaders($response_header) . $http->ReadReplyBody($body, 6553600);
				if ($err == '' and strpos($body, 'We have limited resources') === false && strpos($body, 'Access Denied') === false) {
					$http->SaveCookies($new_cookie);
					$_SESSION['proxy'] = $proxies[$proxy_idx];
					break;
				}
				else {
					if (isset($_SESSION['proxy'])) {
						unset($_SESSION['proxy']);
					}
					else {
						for ($i = $proxy_idx; $i + 1 < count($proxies); ++$i) {
							$proxies[$i] = $proxies[$i + 1];
						}
						array_pop($proxies);
						$memcache->set('proxies', $proxies);
					}				
					error_log ("wydebug	http client error: $err proxy_idx = $proxy_idx ".count($proxies).' proxies left. ');
					break;
				}
			}
			$memcache->close();
			return array($body, $response_header, $new_cookie);
		}
		else {
			return array('', '', '');
		}
	}
	$http->Open($arguments);
	if ($http->SendRequest($arguments) == '' 
	&& $http->ReadReplyHeaders($response_header) == ''
	&& $http->ReadReplyBody($body, 6553600) == '') {
		$http->SaveCookies($new_cookie);
		return array($body, $response_header, $new_cookie);
	}
	return array('', '', array());
	
	
	
	$header='';
	$body='';
	$newcookie = array(array(), array(), array());
	if (preg_match('/^http:\/\/(.*?)(\/.*)$/',$url,$reg)){$host=$reg[1]; $path=$reg[2];}
	else {outs(1,"URL($url)格式非法!"); return;}
	$http_host=$host;
	if (preg_match('/^(.*):(\d+)$/', $host, $reg)) {$host=$reg[1]; $port=$reg[2];}
	else $port=80;
	$fp = fsockopen($host, $port, $errno, $errstr, 30);
	if (!$fp) {
		outs(1,"$errstr ($errno)\n");
	} else {
		fputs($fp, "$method $path HTTP/1.1\r\n");
		fputs($fp, "Host: $http_host\r\n");
		if ($refer!='') fputs($fp, "Referer: $refer\r\n");
		if (count($cookie[0]) != 0) {
			$str_cookie = "Cookie: ";
			for ($i = 0; $i < count($cookie[0]); ++$i) {
				$str_cookie .= $cookie[1][$i]."=".$cookie[2][$i].";";
//				echo "Cookie: ".$cookie[1][$i]."=".$cookie[2][$i]."\r\n";
			}
			fputs($fp, "$str_cookie\r\n");
		}
		fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
		fputs($fp, "Content-length: ".strlen($data)."\r\n");
		fputs($fp, "Connection: close\r\n\r\n");
		fputs($fp, $data . "\r\n\r\n");
		$header_body=0;
		$chunked_format=0;
		$chunked_len=0;
		while (!feof($fp)) {
			$str=fgets($fp);
			//$len=hexdec($str); if ($header_body==1) {echo ">>$str\t$len\n"; $str=fread($fp,$len);echo $str;}
			if ($header_body==1){
				if ($chunked_format){
					if ($chunked_len<=0){
						$chunked_len=hexdec($str);
						if ($chunked_len==0) break;
						else continue;
					} else {
						$chunked_len-=strlen($str);
						if ($chunked_len<=0) $str=trim($str);
						//elseif ($chunked_len==0) fgets($fp);
					}
				}
				$body.=$str;
			}
			else if ($str=="\r\n") $header_body=1;
			else {
				$header.=$str;
				if ($str=="Transfer-Encoding: chunked\r\n") $chunked_format=1;
				if (preg_match_all('|Set-Cookie: (\S+)=(\S+);|',$str,$reg)) {
					array_push($newcookie[0], $reg[0][0]);
					array_push($newcookie[1], $reg[1][0]);
					array_push($newcookie[2], $reg[2][0]);
				}
			}
		}
		fclose($fp);
	}
//	$GLOBALS['TRAFFIC']+=414+strlen($url)+strlen($data)+strlen($header)+strlen($body);
	if (preg_match('/^Location: (\S+)\r\n/m',$header,$reg)) {
		$new_location = '';
		if (substr($reg[1],0,1)!='/'){
			print "reg = ".$reg[1]."\n";
			if (strpos($reg[1], 'http://') === false) { //基于当前路径的相对路径
				$path=substr($path,0,strrpos($path,'/')+1);
			$path.=$reg[1];
		}
			else {  //绝对路径
				$path = $reg[1];
			}
		} else $path=$reg[1];
		if ($newcookie) $cookie=$newcookie;
		if (strstr($path, 'http://') === false) {
			$new_location = 'http://'.$http_host.$path;
	}
		else {
			$new_location = $path;
		}
		return http_request($new_location,'GET','',$cookie,$url);
	}
	return array($body, $header, $newcookie);
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

function post_article($board_en_name, $user, $password, $title, $content, $aid = '0') {
	if (true || date('U') - $_SESSION['login_smth_tick'] > 60 * 10) {	// smth session expired
		switch (login_newsmth($user, $password)) {
		case 0:		//未知错误
			return -7;
		case -1:	//用户名密码错
			return -1;
		case -2:	//登录过于频繁
			return -8;
		case -3:
			return -10;	//用户名或密码为空
		case 1:
			break;
		default:
			return -7;
		}
	}
	$gid = $_POST['gid'];
	$bid = $_POST['bid'];
	if (contain_chn($content) && $aid != 0) {
		$db_conn = conn_db();
		$sql = "select max(aid) from snapshot where bid = $bid and gid = $gid";
		$result = mysql_query($sql);
		$row = mysql_fetch_array($result);
		$newaid = $row[0] + 1;
		$sql = "insert into snapshot(bid, gid, aid, author, nick, title, pub_time, content, signature, `from`, ip, attachments, modifier, modification_time, fetch_time, is_topic) values($bid, $gid, $newaid, '$user', '', '$title', CURRENT_TIMESTAMP, '".str_replace("\n", "\\n", $content)."', '', 'telnet', '', '()', '', '', CURRENT_TIMESTAMP, 0)";
		mysql_query($sql);
		echo "<!-- $sql -->";
	}
	$url = "http://www.newsmth.net/bbssnd.php?board=$board_en_name&reid=$aid";
//	$data = "title=".urlencode($title)."&signature=1&text=".urlencode($content);
	$data = array('title' => $title, 'signature' => 1, 'text' => $content);
	list($response) = http_request($url, 'POST', $data, $_SESSION['smth_cookie']);
//	error_log("wydebug	post_article	post article response: $response");
	if (strpos($response, '本文可能含有不当内容') > 0) {
		return -6;
	}
	elseif (strpos($response, "发文成功！") > 0) {
		return 0;
	}
	elseif (strpos($response, "该文不可回复!") > 0) {
		return -2;
	}
	elseif (strpos($response, "错误的Re文编号!") > 0) {
		return -3;
	}
	else if (strpos($response, '您的积分不符合当前讨论区的设定') > 0) {
		return -5;
	}
	else if (strpos($response, '您无权在该版面发文') > 0) {
		return -9;
	}
	else {
		error_log("wydebug	post_article	illegal post article response: ".$response);
		return -4;
	}
/*	$telnet = new PHPTelnet();
	$telnet->show_connect_error = 0;
	$telnet->Connect("newsmth.net", $user, $password);
	$response = "";
	$telnet->GetResponse($response);
#echo $response;
	$telnet->DoCommand("\n\n\n\ns\n$board_en_name\n".chr(16)."$title\n\r\n\r$content\n\n".chr(0x17)."\n\n\n\r!\n\r\n\r", $response);
*/
}

function post_reply($board_en_name, $aid, $user, $password, $title, $content) {

/*	$telnet = new PHPTelnet();
	$telnet->show_connect_error = 0;
	$telnet->Connect("newsmth.net", $user, $password);
	$response = "";
	$telnet->GetResponse($response);
	$telnet->DoCommand("\n\n\n\ns\n$board_en_name\n$aid\n\nrT\n$title\n\n$content\n".chr(0x17)."\n\n\n\r!\n\r\n\r", $response);
*/
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

function share_to($current_url, $title) {
	$encoded_title = urlencode($title);
	$encoded_url = urlencode($current_url);
	$utf8_encoded_title = urlencode(iconv('GBK', 'UTF-8', $title));
	$html = '分享到:';
	$html .= ' <a href="http://v.t.sina.com.cn/share/share.php?title='.$encoded_title.'&url='.$encoded_url.'&content=gb2312" onclick="pingback(this)" title="新浪微博"><img src="http://t.sina.com.cn/favicon.ico" border="0" width="16"></a>';
	$html .= ' <a href="http://www.kaixin001.com/repaste/share.php?rtitle='.$utf8_encoded_title.'&rurl='.$encoded_url.'&rcontent='.$encoded_url.'" onclick="pingback(this)" title="开心网"><img src="http://www.kaixin001.com/favicon.ico" border="0"></a>';
	$html .= ' <a href="http://bai.sohu.com/app/share/blank/add.do?link='.$encoded_url.'" onclick="pingback(this)" title="白社会"><img src="http://bai.sohu.com/favicon.ico" width="16" border="0"></a>';
	$html .= ' <a href="http://www.douban.com/recommend/?url='.$encoded_url.'&title='.$utf8_encoded_title.'" onclick="pingback(this)" title="豆瓣"><img src="http://www.douban.com/favicon.ico" border="0"></a>';
	$html .= ' <a href="http://tieba.baidu.com/i/app/open_share_api?link='.$encoded_url.'" onclick="pingback(this)" title="i贴吧"><img src="image/itieba.gif" border="0"></a>';
	$html .= ' <a href="http://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?url='.$encoded_url.'" onclick="pingback(this)" title="qq空间"><img src="http://qzone.qq.com/favicon.ico" border="0"></a>';
	$html .= ' <a href="http://apps.hi.baidu.com/share/?url='.$encoded_url.'&title='.$utf8_encoded_title.'" onclick="pingback(this)" title="百度博客"><img src="http://hi.baidu.com/favicon.ico" border="0"></a>';
	$html .= ' <a href="http://t.sohu.com/third/post.jsp?url='.$encoded_url.'&title='.$utf8_encoded_title.'" onclick="pingback(this)" title="搜狐微博"><img src="http://t.sohu.com/favicon.ico" width="16" border="0"></a>';
	$html .= ' <a href="http://v.t.qq.com/share/share.php?url='.$encoded_url.'&title='.$utf8_encoded_title.'" onclick="pingback(this)" title="腾讯微博"><img src="http://t.qq.com/favicon.ico" border="0"></a>';
	$html .= '<br>';
	return $html;
}

function is_different_reply_title($topic_title, $reply_title, &$prev_titles) {
	$trimed_title = preg_replace("/Re:[\s]*/", '', $reply_title);
	$trimed_title = preg_replace("/ \(转载\)/", '', $trimed_title);
	$trimed_title = chop($trimed_title);
	if (!isset($prev_titles[$reply_title]) && $trimed_title != '') {
		if (strpos($topic_title, $trimed_title) === false && !isset($prev_titles[$trimed_title])) {
			$prev_titles[$trimed_title] = 0;
			$prev_titles[$reply_title] = 0;
			return true;
		}
	}
	return false;
}

function load_proxies() {
	/*
	$memcache = new memcache;
	if (!$memcache->connect('127.0.0.1', 11211)) {
		return 0;
	}
	*/
	$proxies = file('tmp/proxies');
	for ($i = 0; $i < count($proxies); ++$i) {
		$proxy = chop($proxies[$i]);
		$arr = explode(':', $proxy);
		if (count($arr) == 2) {
			$proxies[$i] = $arr;
		}
	}
	/*
	if (count($proxies) > 50) {
		$memcache->set('proxies', $proxies);
	}
	$memcache->close();
	*/
	//echo "proxy num ".count($proxies);
	return $proxies;
}

function get_msec() {
	return round(microtime(true) * 1000);
}

function get_location($ip) {
	if (strpos($ip, '*') > 0) {
		$ip = substr($ip, 0, strlen($ip) - 1).'1';
		$location = IpLocation::getInstance()->getlocation($ip);
		return $location['country'].$location['area'];
	}
	return '';
}

function show_random_pic_articles() {
	global $is_spider;
	global $img_host, $dip_host;
	global $baidu_728_15, $baidu_200_200_footer_mix;
	global $html;
	if (!$is_spider) {
		$rnd_pic_article_num = 8;
		global $is_from_search_engine;
		if ($is_from_search_engine) $rnd_pic_article_num = 4;
		$random_pic_articles = get_random_articles('data/accumulated_pic_articles');
		$random_ptt_pic_articles = get_random_articles('data/ptt_att_articles');
		if (count($random_pic_articles) > 0) {
//			echo '<tr><td colspan="2" bordercolor="gray" align="center"><table><tr><td align="center" colspan="'.$rnd_pic_article_num.'">精彩图片帖<br>';
			$html .= '<tr><td colspan="2" bordercolor="gray" align="center"><table><tr><td align="center" colspan="'.$rnd_pic_article_num.'">精彩图片帖<br>';
//			require('baidu.728_15');
			$html .= $baidu_728_15;
//			echo '</td></tr><tr>';
			$html .= '</td></tr><tr>';
			$rand_ad_pos = rand(0, $rnd_pic_article_num - 1);
			$rand_ptt_pos = rand(0, $rnd_pic_article_num - 1);
			for ($i = 0; $i < $rnd_pic_article_num; ++$i) {
				if (false && $i == $rand_ad_pos) {
//					echo '<td>';
//					require('baidu.200_200');
//					echo '</td>';
					$html .= "<td>$baidu_200_200_footer_mix</td>";
				}
				else {
					if ($i != $rand_ptt_pos) {
//						$idx = $i / 2;
						$idx = $i;
						$img_path = $img_host.'/attachments/'.$random_pic_articles[$idx][0].'.'.$random_pic_articles[$idx][2].'.'.$random_pic_articles[$idx][3].'.'.$random_pic_articles[$idx][8];
						//echo $img_host.' '.$img_path;
						$article_path = 'show_topic.php?en_name='.$random_pic_articles[$idx][5].'&gid='.$random_pic_articles[$idx][1];
						$pic_board_cn_name = $random_pic_articles[$idx][4];
						$pic_article_title = $random_pic_articles[$idx][6];
//						echo "<td align=center style=\"width:230\"><a href=\"$article_path\" onclick=\"pingback(this)\"><img src=\"$img_path\" border=\"0\" onload=\"resize_thumb(this)\"></a><br>$pic_board_cn_name<br><a href=\"$article_path\" onclick=\"pingback(this)\">".stripslashes($pic_article_title)."</a></td>";
						$html .= "<td align=center style=\"width:230\"><a href=\"$article_path\" onclick=\"pingback(this)\"><img src=\"$img_path\" border=\"0\" onload=\"resize_thumb(this)\"></a><br>$pic_board_cn_name<br><a href=\"$article_path\" onclick=\"pingback(this)\">".stripslashes($pic_article_title)."</a></td>";
					}
					else {
						$idx = $i;
						$random_ptt_pic_article = $random_ptt_pic_articles[$idx];
						$bid = $random_ptt_pic_article[0];
						$tid = $random_ptt_pic_article[1];
						$file_name = $random_ptt_pic_article[2];
						$title = $random_ptt_pic_article[3];
						$img_path = "$img_host/ptt_att_thumb/$bid.$tid.$file_name";
						$article_path = "$dip_host/show_ptt_topic.php?bid=$bid&tid=$tid";
//						echo "<td align=center style=\"width:230\"><a href=\"$article_path\" onclick=\"pingback(this)\"><img src=\"$img_path\" border=\"0\" onload=\"resize_thumb(this)\"></a><br>表特<br><a href=\"$article_path\" onclick=\"pingback(this)\">".stripslashes($title)."</a></td>";
						$html .= "<td align=center style=\"width:230\"><a href=\"$article_path\" onclick=\"pingback(this)\"><img src=\"$img_path\" border=\"0\" onload=\"resize_thumb(this)\"></a><br>表特<br><a href=\"$article_path\" onclick=\"pingback(this)\">".stripslashes($title)."</a></td>";
					}
				}
				if ($i == 3) {
//					echo '</tr><tr><td colspan="4" align="center">';
//					require('baidu.728_15');
//					echo '</td><tr>';
					$html .= '</tr><tr><td colspan="4" align="center">'.$baidu_728_15.'</td><tr>';
				}
			}
//			echo '</tr></table></td></tr>';
			$html .= '</tr></table></td></tr>';
		}
	}
	return $html;
}

function str_contain($str, $pattern) {
	return !(strstr($str, $pattern) === false);
}

function str_seperate_contain($str, $s1, $s2) {
	if (strstr($str, $s1) === false) return false;
	if (strstr($str, $s2) === false) return false;
	return true;
}

function is_forbidden($title) {
//	$result = (strstr($title, '小姐') != false) && (strstr($title, '电话') != false || strstr($title, '服务') != false || strstr($title, '哪') != false || strstr($title, '服務') != false || strstr($title, '信息') != false);
	$result = false;
	if (!$result) {
		$space_count = 0;
		for ($i = 0; $i < strlen($title); ++$i) {
			$char = substr($title, $i, 1);
			if ($char == ' ')
				++$space_count;
		}
		if ($space_count >= 8) {
			$result = true;
		}
	}
	$title = str_replace(' ', '', $title);
	if (!$result) {
		$result = str_seperate_contain($title, '小', '姐') && (str_contain($title, '找') || str_contain($title, '包夜') || str_seperate_contain($title, '电', '话') || str_seperate_contain($title, '服', '务') || str_seperate_contain($title, '信', '息') || str_seperate_contain($title, '服', '務') || str_seperate_contain($title, '价', '格') || str_contain($title, '哪') || str_contain($title, '兼职') || str_contain($title, '妹妹') || str_contain($title, '小妹') || str_contain($title, '富婆'));
	}
	if (!$result) {
		$result = strstr($title, '红灯区') != false;
	}
	if (!$result) {
		$result = str_contain($title, '鎗');
	}
	if (!$result) {
		$result = (strstr($title, '藥') != false || strstr($title, '药') != false || strstr($title, '葯') != false) && (strstr($title, '迷') != false || strstr($title, '晕') != false || strstr($title, '情') != false || strstr($title, '幻') != false || strstr($title, '昏') != false || strstr($title, '春') != false);
//		$result = strstr($title, '迷藥') != false || strstr($title, '迷药') != false || strstr($title, '晕药') != false || strstr($title, '情药') != false || strstr($title, '幻药') != false || strstr($title, '昏药') != false;
	}
	if (!$result) {
		$result = str_contain($title, '代') && $result = str_contain($title, '开') && $result = str_contain($title, '发') && str_contain($title, '票');
	}
	if (!$result) {
		$result = str_contain($title, '麻果');
	}
	if (!$result) {
		$result = str_contain($title, '兼职') && str_contain($title, '妹妹');
	}
//	if (!$result) {
//		$result = str_contain($title, '慢') && str_contain($title, '性') && str_contain($title, '毒');
//	}
	if (!$result) {
		$result = str_contain($title, '办理') && (str_contain($title, '毕業') || str_contain($title, '学位'));
	}
	if (!$result) {
		$result = (str_contain($title, '办') || str_contain($title, '辦')) && (str_contain($title, '证') || str_contain($title, '證'));
	}
	if (!$result) {
		$result = str_contain($title, '股民资源');
	}
	if (false && !$result) {
		$digit_count = 0;
		for ($i = 0; $i < strlen($title); ++$i) {
			$char = substr($title, $i, 1);
			if (ctype_digit($char))
				++$digit_count;
		}
		if ($digit_count >= 6) {
			$result = true;
		}
	}
	return $result;
}

function get_prev_topics($pub_time) {
	$ret = array();
	$sql = "select en_name, gid, title from board, snapshot where board.bid = snapshot.bid and is_topic = 1 and pub_time < '$pub_time' order by pub_time desc limit 10";
	$result = mysql_query($sql);
	while($row = mysql_fetch_array($result)) {
		$en_name = $row[0];
		$gid = $row[1];
		$title = $row[2];
		array_push($ret, array($en_name, $gid, $title));
	}
	return $ret;
}

function validate_parameters() {
	while (list($key, $value) = each($_GET)) {
		if (str_contain($value, '>') || str_contain($value, '%') || str_contain($value, ' ')) {
			return false;
		}
	}
	while (list($key, $value) = each($_POST)) {
		if (str_contain($value, '>') || str_contain($value, '%') || str_contain($value, ' ')) {
			return false;
		}
	}
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

function get_jandan_pics($type, $size = 20) {
	$types = array('jandan_beauty', 'jandan_funny');
	$counts = array(23564, 63592);
	$ret = array();
//*
	while (count($ret) < $size) {
		$rand = rand(0, $counts[$type]);
		$result = mysql_query("select id, url, enabled, width, height from ".$types[$type]." limit $rand, ".($size * 2));
//		$result = mysql_query("select id, url, enabled from ".$types[$type]." limit $rand, 1");
		while ($row = mysql_fetch_array($result)) {
//		$row = mysql_fetch_array($result);
			if (count($ret) >= $size) break;
			if (!isset($row['enabled']) || $row['enabled'] == 0 || $row['height'] > 400) continue;
			$ret[$row['id']] = array($row['id'], $row['url'], $row['width'], $row['height']);
		}
	}
//*/
	return $ret;
}

function get_jandan_rand_pics($size = 5) {
	$ret = array();
	while (count($ret) < $size) {
		$rand = rand(0, 23564);
		$result = mysql_query("select id, url, enabled from jandan_beauty limit $rand, ".($size * 2));
//		$result = mysql_query("select id, url, enabled from jandan_beauty limit $rand, 1");
		while ($row = mysql_fetch_array($result)) {
			if (count($ret) >= $size) break;
			if (!isset($row['enabled']) || $row['enabled'] == 0) continue;
			$ret[$row['id']] = array($row['id'], $row['url']);
		}
	}
	return $ret;
}

function get_jandan_rand_pic_html($size = 5) {
	global $sogou_200_200_jdbt;
	$dataset = get_jandan_rand_pics($size);
	$html = '<table><tr>';
	$ad_pos = rand(0, count($dataset));
	$i = 0;
//	$html .= "ad pos = $ad_pos, count = ".count($dataset);
	foreach (array_values($dataset) as $item) {
		if ($i++ == $ad_pos) {
			$html .= "<td> &nbsp; $sogou_200_200_jdbt &nbsp; </td>";
		}
		$id = $item[0];
		$url = $item[1];
		$html .= "<td> &nbsp; <a href=\"http://dip.btsmth.org/jd.php?id=$id&type=0\"><img src=\"$url\" height=\"200\" border=\"0\" /></a> &nbsp; </td>";
	}
	if ($ad_pos == count($dataset)) {
		$html .= "<td> &nbsp; $sogou_200_200_jdbt &nbsp; </td>";
	}
	$html .= '</tr></table>';
	return $html;
}

function get_jandan_featured_pics() {
	$table_names = array('jandan_beauty', 'jandan_funny');
	$limits = array(1000, 10000);
	$ret = array();
	for ($type = 0; $type < 2; ++$type) {
		$rnd = rand(0, $limits[$type]);
		list($id, $url) = execute_vector("select id, url from ".$table_names[$type]." where enabled = 1 order by oo desc limit $rnd, 1");
		$ret[] = array($type, $id, $url);
	}
	return $ret;
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
	'/disp.php' => 1
	);
	if ($is_from_search_engine) {}
	else if ($is_spider) {
		$is_loyal = 1;
	}
	else {
		if (!isset($_COOKIE['is_loyal']) || $_COOKIE['is_loyal'] == 0) {
			$is_loyal = isset($loyal_user_uris[$_SERVER['REQUEST_URI']]) ? 1 : 0;
			if ($is_loyal) {
				setcookie('is_loyal', $is_loyal, time() + 3600 * 24 * 365);
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

function get_old_ck101_topic_html() {
	list($tid_max, $tid_min) = execute_vector('select max(tid), min(tid) from ck101.topic');
	$result = mysql_query('select bid, title, author, tid from ck101.topic where tid > '.rand($tid_min, $tid_max).' order by tid limit 10');
	while (list($bid, $title, $author, $tid) = mysql_fetch_array($result)) {
		$old_topics[] = array($title, $author, $bid, $tid);
	}
	$html = '<div class="panel panel-default"><div class="panel-heading">'.i18n('jixuyuedu').'</div>';
	$html .= '<div class="list-group">';
	foreach ($old_topics as $topic) {
		list($title, $author, $bid, $tid) = $topic;
		$html .= "<a href=\"/ck101/$bid/$tid\" class=\"list-group-item\">".i18n($title.' '.$author)." </a>";
	}
	$html .= '</div></div>';
	return $html;
}
?>

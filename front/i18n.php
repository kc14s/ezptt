<?
/*
$protocol = $_SERVER['SERVER_PROTOCOL'];
if (is_from_china()) {
	if (strpos($protocol, 'HTTP/') === 0) {
		header('Location: https://www.ucptt.com'.$_SERVER['REQUEST_URI'], TRUE, 301);
		exit;
	}
}
else {
	if (strpos($protocol, 'HTTPS/') === 0) {
		header('Location: http://www.ucptt.com'.$_SERVER['REQUEST_URI'], TRUE, 301);
		exit;
	}
}
//*/
if ($is_google_spider && $_SERVER['HTTP_HOST'] == 'cn.ucptt.com') {
	header('Location: http://www.ucptt.com'.$_SERVER['REQUEST_URI'], TRUE, 301);
}
require_once('ZhConversion.php');
$lang = 'zh_TW';
if ($_SERVER['HTTP_HOST'] == 'cn.ucptt.com') {
	$lang = 'zh_CN';
}
else if ($is_spider && !$is_google_spider) {
	$lang = 'zh_CN';
}
else if (is_from_cn_search_engine()) {
	$lang = 'zh_CN';
}
else if (strtolower(substr($_SERVER["HTTP_ACCEPT_LANGUAGE"], 0, 5)) == 'zh-cn') {
	$lang = 'zh_CN';
}
else if (false && is_from_china()) {
	$lang = 'zh_CN';
//	error_log('from china');
}
else {
	$lang = 'zh_TW';
//	error_log('zh_TW');
}

$i18n = array(
'zh_CN' => array(
'louzhu' => '楼主',
'zuozhe' => '作者',
'jixuyuedu' => '继续阅读',
'xuanzeyuyan' => '选择语言',
'xuanzekanban' => '选择讨论区',
'meizhaodaokanban' => '未找到讨论区，请重新输入',
'defawen' => '的全部帖子',
'meizhaodaozuozhe' => '抱歉，未找到此用户的帖子',
'chaxunzuozhe' => '查询作者',
'chengrenwenxue' => '成人文学',
'' => '',
'' => '',
'' => '',
'' => '',
'' => '',
'' => ''
),
'zh_TW' => array(
'louzhu' => '作者',
'zuozhe' => '作者',
'jixuyuedu' => '繼續閱讀',
'xuanzeyuyan' => '選擇語言',
'xuanzekanban' => '選擇看板',
'meizhaodaokanban' => '未知看板，請重新輸入',
'defawen' => '的全部發文',
'meizhaodaozuozhe' => '抱歉，未找到此ID的發文',
'chaxunzuozhe' => '檢索用戶',
'chengrenwenxue' => '成人文學',
'' => '',
'' => '',
'' => '',
'' => '',
'' => ''

)
);

$is_default_tw = strpos($_SERVER['PHP_SELF'], '/douban') === 0 ? false : true;

function get_hreflang() {
	global $lang;
	$protocol = $_SERVER['HTTPS'] ? 'https' : 'http';
	if ($lang == 'zh_TW') {
		return '<link rel="alternate" hreflang="zh_CN" href="'.$protocol.'://cn.ucptt.com'.$_SERVER['REQUEST_URI'].'" />';
	}
	else if ($lang == 'zh_CN') {
		return '<link rel="alternate" hreflang="zh_CN" href="'.$protocol.'://www.ucptt.com'.$_SERVER['REQUEST_URI'].'" />';
	}
	return '';
}

function i18n($key) {
	global $lang, $i18n, $is_default_tw;
	if (isset($i18n[$lang][$key])) return $i18n[$lang][$key];
	if ($lang == 'zh_TW') {
		if ($is_default_tw) {
			return $key;
		}
		else {
			global $zh2Hant, $zh2TW;
			return strtr(strtr($key, $zh2TW), $zh2Hant);
		}
	}
	else {
		if ($is_default_tw) {
			global $zh2Hans, $zh2CN;
			return strtr(strtr($key, $zh2CN), $zh2Hans);
		}
		else {
			return $key;
		}
	}
}
?>

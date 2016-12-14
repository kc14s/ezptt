<?
function __autoload($class_name) {
	include $class_name . '.php';
}
$lang = 'zh_CN';
if (strpos($_SERVER['HTTP_HOST'], 'tw') === 0) {
	$lang = 'zh_TW';
}

$i18n = array(
	'zh_CN' => array(
		'xuanzebanmian' => '选择讨论区',
		'chaxunzuozhe' => '查询作者',
		'meizhaodaokanban' => '没找到讨论区',
		'defawen' => '的文章',
		'jixuyuedu' => '继续阅读',
		'tongzuozhe' => '同作者',
		'' => '',
		'' => '',
		'' => '',
		'' => ''
	),
	'zh_TW' => array(
		'xuanzebanmian' => '選擇看板',
		'chaxunzuozhe' => '查詢作者',
		'meizhaodaokanban' => '沒找到討論區',
		'defawen' => '的發文',
		'jixuyuedu' => '繼續閱讀',
		'tongzuozhe' => '同作者',
		'' => '',
		'' => '',
		'' => '',
		'' => ''
	)
);

function get_html_lang() {
	global $lang;
	if ($lang == 'zh_CN') return 'zh-hans';
	if ($lang == 'zh_TW') return 'zh-hant';
	return 'en_US';
}

function get_hreflang() {
	global $lang;
	$protocol = $_SERVER['HTTPS'] ? 'https' : 'http';
	if ($lang == 'zh_TW') {
		return '<link rel="alternate" hreflang="cn" href="'.$protocol.'://www.ezsmth.com'.$_SERVER['REQUEST_URI'].'" />';
	}
	else if ($lang == 'zh_CN') {
		return '<link rel="alternate" hreflang="tw" href="'.$protocol.'://tw.ezsmth.com'.$_SERVER['REQUEST_URI'].'" />';
	}
	return '';
}

function i18n($key) {
	global $lang, $i18n;
	if (isset($i18n[$lang][$key])) return $i18n[$lang][$key];
	if ($lang == 'zh_TW') {
		return strtr(strtr($key, zhs2t::$zh2TW), zhs2t::$zh2Hant);
	}
	else if ($lang == 'zh_CN') {
		return $key;
	}
	return $key;
}
?>

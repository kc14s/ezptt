<?
require_once('ZhConversion.php');
$lang = 'zh_TW';
if ($_SERVER['HTTP_HOST'] == 'cn.ezptt.com') {
	$lang = 'zh_CN';
}
else if ($is_spider && !$is_google_spider) {
	$lang = 'zh_CN';
}
else if (is_from_cn_search_engine()) {
	$lang = 'zh_CN';
}

$i18n = array(
'zh_CN' => array(
'louzhu' => '楼主',
'zuozhe' => '作者',
'jixuyuedu' => '继续阅读',
'xuanzeyuyan' => '选择语言',
'xuanzekanban' => '选择讨论区',
'meizhaodaokanban' => '未找到讨论区，请重新输入',
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
'' => '',
'' => '',
'' => ''

)
);

function i18n($key) {
	global $lang, $i18n;
	if (isset($i18n[$lang][$key])) return $i18n[$lang][$key];
	if ($lang == 'zh_TW') {
		return $key;
	}
	else {
		global $zh2Hans, $zh2CN;
		return strtr(strtr($key, $zh2CN), $zh2Hans);
	}
}
?>

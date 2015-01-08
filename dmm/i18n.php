<?
require_once('ZhConversion.php');
$lang = 'en_US';
$sub_domain = substr($_SERVER['HTTP_HOST'], 0, 2);
if ($sub_domain == 'cn') {
	$lang = 'zh_CN';
}
else if ($sub_domain == 'tw') {
	$lang = 'zh_TW';
}
else if ($sub_domain == 'jp') {
	$lang = 'ja_JP';
}
$lang_short = substr($lang, 0, 2);
function get_lang_short() {
	global $lang_short;
	return $lang_short;
}

$i18n = array(
'zh_CN' => array(
'star' => '女优',
'company' => '发行商',
'director' => '导演',
'genre' => '标签',
'sn' => '番号',
'fav_count' => '赞',
'rating' => '评分',
'star_video' => '的其他作品',
'release_date' => '发行日期',
'hottest' => '热门AV',
'latest' => '最新上线',
'more' => '更多',
'page_up' => '上一页',
'page_down' => '下一页',
'select_language' => '选择语言',
'video_not_found' => '抱歉，未找到您要找的AV。',
'chengrenwenxue' => '成人文学',
'' => ''
),
'zh_TW' => array(
'star' => '女優',
'company' => '發行商',
'director' => '導演',
'genre' => '標籤',
'sn' => '番號',
'fav_count' => '贊',
'rating' => '評分',
'star_video' => '的其他作品',
'release_date' => '發行日期',
'hottest' => '熱門AV',
'latest' => '最新上線',
'more' => '更多',
'page_up' => '上一頁',
'page_down' => '下一頁',
'select_language' => '選擇語言',
'video_not_found' => '抱歉，未找到您要的AV。',
'chengrenwenxue' => '成人文學',
'' => '',
'' => '',
'' => '',
'' => ''
),
'en_US' => array(
'star' => 'Stars',
'company' => 'Studio',
'director' => 'Director',
'genre' => 'Genre',
'sn' => 'SN',
'fav_count' => 'Likes',
'rating' => 'Rating',
'star_video' => '\'s other works',
'release_date' => 'Release Date',
'hottest' => 'Top AV',
'latest' => 'What\'s New',
'more' => 'more',
'page_up' => 'Page Up',
'page_down' => 'Page Down',
'select_language' => 'Select Language',
'video_not_found' => 'Sorry, we can not find the video.',
'' => '',
'' => '',
'' => ''
),
'ja_JP' => array(
'star' => '出演者',
'company' => 'メーカー',
'director' => '監督',
'genre' => 'ジャンル',
'sn' => '品番',
'fav_count' => 'お気に入り登録数',
'rating' => '平均評価',
'star_video' => 'によって行われる',
'release_date' => '配信開始日',
'hottest' => 'トップ',
'latest' => '最新である',
'more' => 'more',
'page_up' => '前の',
'page_down' => '次の',
'select_language' => '言語の選択',
'video_not_found' => 'AVが見つかりませんでした。',
'' => '',
'' => '',
'' => ''
)
);

function i18n($key) {
	global $lang, $i18n;
	if (isset($i18n[$lang][$key])) return $i18n[$lang][$key];
	return $key;
	if ($lang == 'zh_TW') {
		return $key;
	}
	else {
		global $zh2Hans, $zh2CN;
		return strtr(strtr($key, $zh2CN), $zh2Hans);
	}
}
?>

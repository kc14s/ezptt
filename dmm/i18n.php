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
'seed_name' => '种子名称',
'seed_size' => '大小',
'seed_file_num' => '文件数',
'seed_created' => '创建日期',
'seed_popularity' => '人气',
'seed_torrent' => 'BT种子',
'seed_magnet' => '磁力链',
'seed_download_bt' => '下载',
'seed_download_magnet' => '下载',
'download' => '下载',
'' => '',
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
'seed_name' => '種子名稱',
'seed_size' => '大小',
'seed_file_num' => '文件數',
'seed_created' => '創建日期',
'seed_popularity' => '人氣',
'seed_torrent' => 'BT種子',
'seed_magnet' => '磁力鏈',
'seed_download_bt' => '下載',
'seed_download_magnet' => '下載',
'download' => '下載',
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
'seed_name' => 'Name',
'seed_size' => 'Size',
'seed_file_num' => 'Number of Files',
'seed_created' => 'Creation Date',
'seed_popularity' => 'Popularity',
'seed_torrent' => 'Torrent File',
'seed_magnet' => 'Magnet Link',
'seed_download_bt' => 'Download',
'seed_download_magnet' => 'Download',
'download' => 'Download',
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
	if ($lang == 'ja_JP') {
		if (!isset($i18n[$lang][$key]) && isset($i18n['en_US'][$key])) {
			return $i18n['en_US'][$key];
		}
	}
	return $key;
}
?>

<?
#error_log("language ".$_SERVER["HTTP_ACCEPT_LANGUAGE"]);
//*
require_once('init.php');
function __autoload($class_name) {
	include $class_name . '.php';
}

if (true || $is_spider || $is_from_search_engine || $_COOKIE['is_from_search_engine'] == 1 || isset($_COOKIE['xM2S_2132_auth']) || (isset($_SERVER["HTTP_ACCEPT_LANGUAGE"]) && str_contain($_SERVER["HTTP_ACCEPT_LANGUAGE"], 'zh'))) {}
else {
	header("HTTP/1.1 301 Moved Permanently"); 
	header("Location: /discuz/forum.php?mod=viewthread&tid=21&extra=page%3D1"); 
	error_log("reject ".$_SERVER["HTTP_ACCEPT_LANGUAGE"].' '.$_SERVER['HTTP_USER_AGENT']);
	exit;
}
//*/
$lang = 'zh_CN';
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
else if ($sub_domain == 'en') {
	$lang = 'en_US';
}
$lang_short = substr($lang, 0, 2);
function get_lang_short() {
	global $lang_short;
	return $lang_short;
}

$i18n = array(
'zh_CN' => array(
'star' => '女优',
'company' => '片商',
'director' => '导演',
'genre' => '标签',
'sn' => '番号',
'fav_count' => '赞',
'rating' => '评分',
'star_video' => '的其他作品',
'release_date' => '发行日期',
'hottest' => '新品推荐',
'latest' => '最新上线',
'popularity' => '人气榜单',
'more' => '更多',
'page_up' => '上一页',
'page_down' => '下一页',
'select_language' => '选择语言',
'video_not_found' => '抱歉，未找到您要找的AV。',
'chengrenwenxue' => '成人文学',
'seed_name' => 'BT下载',
'seed_size' => '大小',
'seed_file_num' => '文件数',
'seed_created' => '创建日期',
'seed_popularity' => '人气',
'seed_torrent' => 'BT种子',
'seed_magnet' => '磁力链',
'seed_download_bt' => '下载',
'seed_download_magnet' => '下载',
'download' => '下载',
'star_all_video' => '的所有作品、番号、封面、下载',
'top_stars' => '人气女优',
'best_seller' => '畅销榜',
'seed_available_sources' => '有效源',
'seed_completed_sources' => '下载量',
'seed_download_emule' => '下载',
'empty_kw' => '检索词为空',
'no_search_result' => '无检索结果',
'forum' => '论坛',
'favourite' => '最受欢迎',
'hot_download' => '热门下载',
#'share_request' => '无法下载？向朋友们求种吧：',
'share_request' => '<a href="/discuz/forum.php?mod=forumdisplay&fid=2">无法下载？去论坛向朋友们求种吧</a>',
'channel_1' => '',
'channel_2' => '素人',
'channel_3' => '动画',
'channel_4' => '',
'channel_5' => '无码',
'channel_6' => '动画',
'channel_7' => '欧美',
'channel_8' => '素人',
'channel_9' => '东京热',
'channel_10' => '一本道',
'emule_name' => '电驴下载',
'series' => '系列',
'runtime' => '播放时长',
'minute' => '分钟',
'シチュエーション' => '身份/职业',
'タイプ' => '身材',
'コスチューム' => '衣着',
'ジャンル' => '企划/主题',
'プレイ' => '玩法',
'その他' => '杂项',
'channel_9' => 'Tokyo Hot',
'censored' => '有码',
'uncensored' => '无码',
'amateur' => '素人',
'louzhu' => '楼主',
'jixuyuedu' => '继续阅读',
'' => '',
'' => '',
'' => '',
'' => '',
'' => '',
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
'popularity' => '人氣榜單',
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
'star_all_video' => '的所有作品/番號/封面/下載',
'top_stars' => '人氣女優',
'best_seller' => '暢銷榜',
'seed_available_sources' => '有效源',
'seed_completed_sources' => '下載量',
'seed_download_emule' => '下載',
'empty_kw' => '檢索詞為空',
'no_search_result' => '無檢索結果',
'forum' => '論壇',
'favourite' => '最受歡迎',
'hot_download' => '熱門下載',
'share_request' => '無法下載？向朋友們求種吧：',
'channel_2' => '素人',
'channel_3' => '動畫',
'emule_name' => '電驢下載',
'series' => '系列',
'runtime' => '播放時長',
'minute' => '分鐘',
'channel_5' => '無碼',
'channel_6' => '動畫',
'channel_7' => '歐美',
'channel_8' => 'MGS',
'channel_9' => 'Tokyo Hot',
'channel_10' => '一本道',
'censored' => '有碼',
'unsensored' => '無碼',
'amateur' => '素人',
'louzhu' => '楼主',
'jixuyuedu' => '繼續閱讀',
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
'hottest' => 'New & Hot',
'latest' => 'What\'s New',
'popularity' => 'Most Popular',
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
'star_all_video' => '\'s all movies',
'top_stars' => 'Top Actresses',
'best_seller' => 'Best Sellers',
'seed_available_sources' => 'Available Sources',
'seed_completed_sources' => 'Completed Sources',
'seed_download_emule' => 'Download',
'empty_kw' => 'Query is empty.',
'no_search_result' => 'No search results.',
'forum' => 'Forum',
'favourite' => 'Favourite',
'share_request' => 'Cannot download? Ask friends to help:',
'channel_2' => 'Amateur',
'channel_3' => 'Anime',
'channel_8' => 'MGS',
'emule_name' => 'Emule ED2K',
'series' => 'Series',
'runtime' => 'Play time',
'minute' => 'minutes',
'channel_5' => 'Unsensored',
'channel_6' => 'Anime',
'channel_7' => 'Western',
'channel_9' => 'Tokyo Hot',
'channel_10' => '一本道',
'hot_download' => 'Most Popular Download',
'censored' => 'Censored',
'uncensored' => 'Uncensored',
'amateur' => 'Amateur',
'louzhu' => 'Author',
'' => '',
'' => '',
'' => '',
'' => '',
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
'popularity' => '人気リスト',
'more' => 'more',
'page_up' => '前の',
'page_down' => '次の',
'select_language' => '言語の選択',
'video_not_found' => 'AVが見つかりませんでした。',
'star_all_video' => '',
'top_stars' => ' AV女優ランキング ベスト',
'best_seller' => 'ベストセラー',
'forum' => 'フォーラム',
'favourite' => 'お気に入り数順',
'share_request' => 'Cannot download? Ask friends to help:',
'channel_2' => '素人',
'channel_3' => '動画',
'channel_5' => '無修正',
'channel_6' => '動画',
'channel_7' => '洋物ポルノ',
'channel_8' => 'MGS',
'emule_name' => 'イードンキー',
'series' => 'シリーズ',
'runtime' => '収録時間',
'minute' => 'minutes',
'channel_9' => 'Tokyo Hot',
'channel_10' => '一本道',
'hot_download' => 'Most Popular Download',
'censored' => 'Censored',
'uncensored' => 'Uncensored',
'amateur' => 'Amateur',
'' => '',
'' => '',
'' => '',
'' => '',
'' => '',
'' => '',
'' => '',
'' => '',
'' => ''
)
);

require_once('i18n_genre.php');

function i18n($key) {
	global $lang, $i18n;
	if (isset($i18n[$lang][$key])) return $i18n[$lang][$key];
	if ($lang == 'ja_JP') {
		if (!isset($i18n[$lang][$key]) && isset($i18n['en_US'][$key])) {
			return $i18n['en_US'][$key];
		}
		return $i18n[$lang][$key];
	}
	else if ($lang == 'zh_CN') {
		return strtr(strtr($key, zht2s::$zh2CN), zht2s::$zh2Hans);
	}
	return $key;
}
?>
